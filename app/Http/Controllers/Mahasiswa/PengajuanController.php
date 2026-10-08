<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Enums\PengajuanStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePengajuanRequest;
use App\Http\Requests\UpdatePengajuanRequest;
use App\Models\DokumenPengajuan;
use App\Models\JenisSurat;
use App\Models\Pengajuan;
use App\Services\PengajuanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PengajuanController extends Controller
{
    public function __construct(private readonly PengajuanService $pengajuanService) {}

    /**
     * Riwayat pengajuan milik mahasiswa yang sedang login.
     */
    public function index(Request $request): View
    {
        $pengajuans = Pengajuan::query()
            ->where('user_id', $request->user()->id)
            ->with('jenisSurat')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->query('q');
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('nomor_pengajuan', 'like', "%{$keyword}%")
                        ->orWhere('keperluan', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('mahasiswa.pengajuan.index', [
            'pengajuans' => $pengajuans,
            'statuses' => PengajuanStatus::options(),
        ]);
    }

    /**
     * Form pengajuan untuk jenis surat tertentu.
     */
    public function create(JenisSurat $jenisSurat): View
    {
        abort_unless($jenisSurat->isActive(), 404);

        $jenisSurat->load('persyaratans');

        return view('mahasiswa.pengajuan.create', [
            'jenisSurat' => $jenisSurat,
            'user' => auth()->user(),
        ]);
    }

    /**
     * Simpan pengajuan baru.
     */
    public function store(StorePengajuanRequest $request): RedirectResponse
    {
        $pengajuan = $this->pengajuanService->buatPengajuan(
            $request->user(),
            $request->validated(),
            $request->file('dokumen', []),
        );

        return redirect()
            ->route('mahasiswa.pengajuan.show', $pengajuan)
            ->with('success', "Pengajuan {$pengajuan->nomor_pengajuan} berhasil dikirim.");
    }

    /**
     * Detail pengajuan milik mahasiswa.
     */
    public function show(Request $request, Pengajuan $pengajuan): View
    {
        Gate::authorize('view', $pengajuan);

        $pengajuan->load([
            'jenisSurat.persyaratans',
            'dokumenPengajuans.persyaratan',
            'riwayatStatuses.user',
        ]);

        return view('mahasiswa.pengajuan.show', [
            'pengajuan' => $pengajuan,
            'user' => $request->user(),
        ]);
    }

    /**
     * Form perbaikan pengajuan (hanya status "Perlu Perbaikan").
     */
    public function edit(Pengajuan $pengajuan): View
    {
        Gate::authorize('update', $pengajuan);

        $pengajuan->load(['jenisSurat.persyaratans', 'dokumenPengajuans']);

        return view('mahasiswa.pengajuan.edit', [
            'pengajuan' => $pengajuan,
            'user' => auth()->user(),
        ]);
    }

    /**
     * Simpan hasil perbaikan dan kirim ulang pengajuan.
     */
    public function update(UpdatePengajuanRequest $request, Pengajuan $pengajuan): RedirectResponse
    {
        Gate::authorize('update', $pengajuan);

        $this->pengajuanService->perbaikiPengajuan(
            $pengajuan,
            $request->validated(),
            $request->file('dokumen', []),
        );

        return redirect()
            ->route('mahasiswa.pengajuan.show', $pengajuan)
            ->with('success', 'Perbaikan pengajuan berhasil dikirim ulang.');
    }

    /**
     * Download dokumen persyaratan (pemilik pengajuan).
     */
    public function downloadDokumen(Pengajuan $pengajuan, DokumenPengajuan $dokumen): StreamedResponse
    {
        Gate::authorize('downloadDokumen', $pengajuan);

        abort_unless($dokumen->pengajuan_id === $pengajuan->id, 404);
        abort_unless(Storage::disk('local')->exists($dokumen->file_path), 404);

        return Storage::disk('local')->download($dokumen->file_path, 'Dokumen-'.$dokumen->nama_file);
    }

    /**
     * Download surat hasil (hanya jika status "Selesai").
     */
    public function downloadSurat(Pengajuan $pengajuan): StreamedResponse
    {
        Gate::authorize('downloadSurat', $pengajuan);

        abort_unless($pengajuan->statusIs(PengajuanStatus::Selesai) && $pengajuan->file_surat, 403);
        abort_unless(Storage::disk('local')->exists($pengajuan->file_surat), 404);

        return Storage::disk('local')->download(
            $pengajuan->file_surat,
            'Surat-'.$pengajuan->nomor_pengajuan.'.pdf'
        );
    }
}
