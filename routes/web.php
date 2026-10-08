<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'mahasiswa.dashboard');
})->name('home');

/* ------------------------------------------------------------------ */
/* Autentikasi */
/* ------------------------------------------------------------------ */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

/* ------------------------------------------------------------------ */
/* Area Mahasiswa */
/* ------------------------------------------------------------------ */
Route::prefix('mahasiswa')->name('mahasiswa.')->middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', Mahasiswa\DashboardController::class)->name('dashboard');

    Route::get('/surat', [Mahasiswa\SuratController::class, 'index'])->name('surat.index');
    Route::get('/surat/{jenisSurat}', [Mahasiswa\SuratController::class, 'show'])->name('surat.show');

    Route::get('/pengajuan', [Mahasiswa\PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan/buat/{jenisSurat}', [Mahasiswa\PengajuanController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan', [Mahasiswa\PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan/{pengajuan}', [Mahasiswa\PengajuanController::class, 'show'])->name('pengajuan.show');
    Route::get('/pengajuan/{pengajuan}/perbaiki', [Mahasiswa\PengajuanController::class, 'edit'])->name('pengajuan.edit');
    Route::put('/pengajuan/{pengajuan}', [Mahasiswa\PengajuanController::class, 'update'])->name('pengajuan.update');
    Route::get('/pengajuan/{pengajuan}/dokumen/{dokumen}', [Mahasiswa\PengajuanController::class, 'downloadDokumen'])->name('pengajuan.dokumen');
    Route::get('/pengajuan/{pengajuan}/surat', [Mahasiswa\PengajuanController::class, 'downloadSurat'])->name('pengajuan.surat.download');

    Route::get('/profil', [Mahasiswa\ProfileController::class, 'show'])->name('profile');
});

/* ------------------------------------------------------------------ */
/* Area Admin / Petugas */
/* ------------------------------------------------------------------ */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', Admin\DashboardController::class)->name('dashboard');

    Route::get('/pengajuan', [Admin\PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/verifikasi', [Admin\PengajuanController::class, 'verifikasiIndex'])->name('pengajuan.verifikasi.index');
    Route::get('/pengajuan/{pengajuan}', [Admin\PengajuanController::class, 'show'])->name('pengajuan.show');
    Route::post('/pengajuan/{pengajuan}/periksa', [Admin\PengajuanController::class, 'periksa'])->name('pengajuan.periksa');
    Route::post('/pengajuan/{pengajuan}/verifikasi', [Admin\PengajuanController::class, 'verifikasi'])->name('pengajuan.verifikasi');
    Route::post('/pengajuan/{pengajuan}/selesai', [Admin\PengajuanController::class, 'selesai'])->name('pengajuan.selesai');
    Route::get('/pengajuan/{pengajuan}/dokumen/{dokumen}', [Admin\PengajuanController::class, 'downloadDokumen'])->name('pengajuan.dokumen');

    Route::get('/jenis-surat', [Admin\JenisSuratController::class, 'index'])->name('jenis-surat.index');
    Route::get('/jenis-surat/tambah', [Admin\JenisSuratController::class, 'create'])->name('jenis-surat.create');
    Route::post('/jenis-surat', [Admin\JenisSuratController::class, 'store'])->name('jenis-surat.store');
    Route::get('/jenis-surat/{jenisSurat}/edit', [Admin\JenisSuratController::class, 'edit'])->name('jenis-surat.edit');
    Route::put('/jenis-surat/{jenisSurat}', [Admin\JenisSuratController::class, 'update'])->name('jenis-surat.update');
    Route::delete('/jenis-surat/{jenisSurat}', [Admin\JenisSuratController::class, 'destroy'])->name('jenis-surat.destroy');

    Route::post('/jenis-surat/{jenisSurat}/persyaratan', [Admin\PersyaratanController::class, 'store'])->name('persyaratan.store');
    Route::put('/jenis-surat/{jenisSurat}/persyaratan/{persyaratan}', [Admin\PersyaratanController::class, 'update'])->name('persyaratan.update');
    Route::delete('/jenis-surat/{jenisSurat}/persyaratan/{persyaratan}', [Admin\PersyaratanController::class, 'destroy'])->name('persyaratan.destroy');

    Route::get('/laporan', Admin\LaporanController::class)->name('laporan');
});
