<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Enums\PengajuanStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $pengajuans = $request->user()->pengajuans();

        $stats = [
            'total' => (clone $pengajuans)->count(),
            'diproses' => (clone $pengajuans)->where('status', PengajuanStatus::Diproses)->count(),
            'perlu_perbaikan' => (clone $pengajuans)->where('status', PengajuanStatus::PerluPerbaikan)->count(),
            'selesai' => (clone $pengajuans)->where('status', PengajuanStatus::Selesai)->count(),
            'ditolak' => (clone $pengajuans)->where('status', PengajuanStatus::Ditolak)->count(),
        ];

        $pengajuanTerbaru = $pengajuans->with('jenisSurat')->latest()->take(5)->get();

        return view('mahasiswa.dashboard', compact('stats', 'pengajuanTerbaru'));
    }
}
