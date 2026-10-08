<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PengajuanStatus;
use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Laporan pengajuan berdasarkan periode, jenis surat, dan status.
     */
    public function __invoke(Request $request): View
    {
        $query = Pengajuan::query()->with(['user', 'jenisSurat']);

        $tanggalMulai = $request->query('tanggal_mulai');
        $tanggalSampai = $request->query('tanggal_sampai');
        $jenisSuratId = $request->query('jenis_surat_id');
        $status = $request->query('status');

        if ($tanggalMulai || $tanggalSampai) {
            $query->whereBetween('tanggal_pengajuan', [
                $tanggalMulai ?: '1970-01-01',
                $tanggalSampai ?: now()->toDateString(),
            ]);
        }

        if ($jenisSuratId) {
            $query->where('jenis_surat_id', $jenisSuratId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $stats = [
            'total' => (clone $query)->count(),
            'selesai' => (clone $query)->where('status', PengajuanStatus::Selesai)->count(),
            'diproses' => (clone $query)->where('status', PengajuanStatus::Diproses)->count(),
            'perlu_perbaikan' => (clone $query)->where('status', PengajuanStatus::PerluPerbaikan)->count(),
            'ditolak' => (clone $query)->where('status', PengajuanStatus::Ditolak)->count(),
        ];

        $pengajuans = (clone $query)->latest('tanggal_pengajuan')->paginate(15)->withQueryString();

        return view('admin.laporan', [
            'pengajuans' => $pengajuans,
            'stats' => $stats,
            'jenisSurats' => JenisSurat::orderBy('nama_surat')->get(),
            'statuses' => PengajuanStatus::options(),
        ]);
    }
}
