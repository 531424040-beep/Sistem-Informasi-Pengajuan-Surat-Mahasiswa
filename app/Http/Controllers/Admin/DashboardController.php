<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PengajuanStatus;
use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $base = Pengajuan::query();

        $stats = [
            'total' => (clone $base)->count(),
            'baru' => (clone $base)->where('status', PengajuanStatus::Diajukan)->count(),
            'diperiksa' => (clone $base)->where('status', PengajuanStatus::Diperiksa)->count(),
            'perlu_perbaikan' => (clone $base)->where('status', PengajuanStatus::PerluPerbaikan)->count(),
            'diproses' => (clone $base)->where('status', PengajuanStatus::Diproses)->count(),
            'selesai' => (clone $base)->where('status', PengajuanStatus::Selesai)->count(),
            'ditolak' => (clone $base)->where('status', PengajuanStatus::Ditolak)->count(),
        ];

        $pengajuanTerbaru = Pengajuan::with(['user', 'jenisSurat'])->latest()->take(8)->get();

        // Pengajuan yang membutuhkan tindakan Admin
        $perluTindakan = Pengajuan::with(['user', 'jenisSurat'])
            ->whereIn('status', [PengajuanStatus::Diajukan, PengajuanStatus::Diperiksa, PengajuanStatus::Diproses])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'pengajuanTerbaru', 'perluTindakan'));
    }
}
