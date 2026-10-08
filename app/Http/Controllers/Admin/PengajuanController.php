<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PengajuanStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuratHasilRequest;
use App\Http\Requests\VerifikasiPengajuanRequest;
use App\Models\DokumenPengajuan;
use App\Models\JenisSurat;
use App\Models\Pengajuan;
use App\Services\PengajuanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PengajuanController extends Controller
{
    public function __construct(private readonly PengajuanService $pengajuanService) {}

    /**
     * Daftar seluruh pengajuan dengan filter dan pencarian.
     */
    public function index(Request $request): View
    {
        $pengajuans = Pengajuan::query()
            ->with(['user', 'jenisSurat'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('jenis_surat_id'), fn ($query) => $query->where('jenis_surat_id', $request->query('jenis_surat_id')))
            ->when($request->filled('tanggal_mulai'), fn ($query) => $query->whereDate('tanggal_pengajuan', '>=', $request->query('tanggal_mulai')))
            ->when($request->filled('tanggal_sampai'), fn ($query) => $query->whereDate('tanggal_pengajuan', '<=', $request->query('tanggal_sampai')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->query('q');
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('nomor_pengajuan', 'like', "%{$keyword}%")
                        ->orWhereHas('user', fn ($user) => $user
                            ->where('name', 'like', "%{$keyword}%")
                            ->orWhere('nim', 'like', "%{$keyword}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengajuan.index', [
            'pengajuans' => $pengajuans,
            'jenisSurats' => JenisSurat::orderBy('nama_surat')->get(),
            'statuses' => PengajuanStatus::options(),
        ]);
    }

    /**
     * Halaman verifikasi: antrean pengajuan yang menunggu pemeriksaan/persetujuan.
     */
    public function verifikasiIndex(Request $request): View
    {
        $pengajuans = Pengajuan::query()
            ->with(['user', 'jenisSurat', 'dokumenPengajuans'])
            ->whereIn('status', [PengajuanStatus::Diajukan, PengajuanStatus::Diperiksa])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->query('q');
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('nomor_pengajuan', 'like', "%{$keyword}%")
                        ->orWhereHas('user', fn ($user) => $user
                            ->where('name', 'like', "%{$keyword}%")
                            ->orWhere('nim', 'like', "%{$keyword}%"));
                });
            })
            ->orderBy('tanggal_pengajuan')
            ->paginate(10)
            ->withQueryString();

        return view('admin.verifikasi.index', compact('pengajuans'));
    }

    /**
     * Detail pengajuan untuk pemeriksaan Admin.
     */
    public function show(Pengajuan $pengajuan): View
    {
        Gate::authorize('view', $pengajuan);

        $pengajuan->load([
            'user',
            'jenisSurat.persyaratans',
            'dokumenPengajuans.persyaratan',
            'riwayatStatuses.user',
            'adminPemroses',
        ]);

        return view('admin.pengajuan.show', compact('pengajuan'));
    }

    /**
     * Tandai pengajuan "Diajukan" menjadi "Diperiksa".
     */
    public function periksa(Pengajuan $pengajuan): RedirectResponse
    {
        Gate::authorize('view', $pengajuan);

        if (! $pengajuan->statusIs(PengajuanStatus::Diajukan)) {
            return back()->with('error', 'Hanya pengajuan berstatus "Diajukan" yang dapat diperiksa.');
        }

        $this->pengajuanService->ubahStatus(
            $pengajuan,
            PengajuanStatus::Diperiksa,
            auth()->user(),
            'Pengajuan sedang diperiksa oleh Admin.'
        );

        return back()->with('success', 'Pengajuan ditandai sedang diperiksa.');
    }

    /**
     * Verifikasi pengajuan: Setujui (→ Diproses), Perlu Perbaikan, atau Tolak.
     */
    public function verifikasi(VerifikasiPengajuanRequest $request, Pengajuan $pengajuan): RedirectResponse
    {
        Gate::authorize('view', $pengajuan);

        $statusBaru = $request->targetStatus();

        if (! $pengajuan->statusIs(PengajuanStatus::Diperiksa)) {
            return back()->with('error', 'Pengajuan harus berstatus "Diperiksa" sebelum diverifikasi. Silakan tandai diperiksa terlebih dahulu.');
        }

        $catatan = $request->validated('catatan');

        $keterangan = match ($statusBaru) {
            PengajuanStatus::Diproses => 'Pengajuan disetujui dan sedang diproses oleh Admin.',
            PengajuanStatus::PerluPerbaikan => 'Admin meminta perbaikan: '.$catatan,
            PengajuanStatus::Ditolak => 'Pengajuan ditolak oleh Admin. Alasan: '.$catatan,
        };

        DB::transaction(function () use ($pengajuan, $statusBaru, $request, $catatan, $keterangan) {
            $pengajuan->update([
                'status' => $statusBaru,
                'catatan_admin' => in_array($statusBaru, [PengajuanStatus::PerluPerbaikan, PengajuanStatus::Ditolak], true)
                    ? $catatan
                    : null,
                'processed_by' => $statusBaru === PengajuanStatus::Diproses ? $request->user()->id : $pengajuan->processed_by,
            ]);

            $this->pengajuanService->catatStatus($pengajuan, $statusBaru, $request->user(), $keterangan);
        });

        $pesan = match ($statusBaru) {
            PengajuanStatus::Diproses => 'Pengajuan disetujui dan masuk tahap pemrosesan.',
            PengajuanStatus::PerluPerbaikan => 'Permintaan perbaikan dikirim ke mahasiswa.',
            PengajuanStatus::Ditolak => 'Pengajuan telah ditolak.',
        };

        return back()->with('success', $pesan);
    }

    /**
     * Unggah surat hasil dan selesaikan pengajuan.
     */
    public function selesai(StoreSuratHasilRequest $request, Pengajuan $pengajuan): RedirectResponse
    {
        Gate::authorize('view', $pengajuan);

        if (! $pengajuan->statusIs(PengajuanStatus::Diproses)) {
            return back()->with('error', 'Hanya pengajuan berstatus "Diproses" yang dapat diselesaikan.');
        }

        $file = $request->file('file_surat');
        $path = $file->store('surat', 'local');

        DB::transaction(function () use ($pengajuan, $request, $path) {
            $pengajuan->update([
                'status' => PengajuanStatus::Selesai,
                'file_surat' => $path,
                'processed_by' => $request->user()->id,
                'completed_at' => now(),
            ]);

            $this->pengajuanService->catatStatus(
                $pengajuan,
                PengajuanStatus::Selesai,
                $request->user(),
                'Surat hasil telah selesai dan dapat diunduh oleh mahasiswa.'
            );
        });

        return back()->with('success', 'Surat hasil berhasil diunggah. Pengajuan diselesaikan.');
    }

    /**
     * Download dokumen persyaratan untuk pemeriksaan Admin.
     */
    public function downloadDokumen(Pengajuan $pengajuan, DokumenPengajuan $dokumen): StreamedResponse
    {
        Gate::authorize('downloadDokumen', $pengajuan);

        abort_unless($dokumen->pengajuan_id === $pengajuan->id, 404);
        abort_unless(Storage::disk('local')->exists($dokumen->file_path), 404);

        return Storage::disk('local')->download($dokumen->file_path, 'Dokumen-'.$dokumen->nama_file);
    }
}
