<?php

use App\Enums\PengajuanStatus;
use App\Models\Pengajuan;

beforeEach(function () {
    $this->petugas = admin();
});

test('admin dapat membuka halaman verifikasi', function () {
    Pengajuan::factory()->create(['status' => PengajuanStatus::Diajukan]);

    $this->actingAs($this->petugas)
        ->get(route('admin.pengajuan.verifikasi.index'))
        ->assertOk()
        ->assertSee('Verifikasi Surat Pengajuan');
});

test('halaman verifikasi hanya menampilkan pengajuan yang menunggu keputusan', function () {
    $menungguDiajukan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diajukan]);
    $menungguDiperiksa = Pengajuan::factory()->create(['status' => PengajuanStatus::Diperiksa]);
    Pengajuan::factory()->create(['status' => PengajuanStatus::Selesai]);
    Pengajuan::factory()->create(['status' => PengajuanStatus::Ditolak]);

    $this->actingAs($this->petugas)
        ->get(route('admin.pengajuan.verifikasi.index'))
        ->assertOk()
        ->assertSee($menungguDiajukan->nomor_pengajuan)
        ->assertSee($menungguDiperiksa->nomor_pengajuan)
        ->assertDontSee(Pengajuan::where('status', PengajuanStatus::Selesai)->first()->nomor_pengajuan)
        ->assertDontSee(Pengajuan::where('status', PengajuanStatus::Ditolak)->first()->nomor_pengajuan);
});

test('pengajuan diajukan di halaman verifikasi menampilkan tombol periksa', function () {
    $pengajuan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diajukan]);

    $this->actingAs($this->petugas)
        ->get(route('admin.pengajuan.verifikasi.index'))
        ->assertSee('Tandai Diperiksa')
        ->assertDontSee('name="action" value="setujui"', false);
});

test('pengajuan diperiksa di halaman verifikasi menampilkan tombol setujui dan tolak', function () {
    Pengajuan::factory()->create(['status' => PengajuanStatus::Diperiksa]);

    $this->actingAs($this->petugas)
        ->get(route('admin.pengajuan.verifikasi.index'))
        ->assertSee('Setujui')
        ->assertSee('Perlu Perbaikan')
        ->assertSee('Tolak');
});

test('aksi verifikasi cepat dari halaman verifikasi berfungsi', function () {
    $pengajuan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diperiksa]);

    $this->actingAs($this->petugas)
        ->post(route('admin.pengajuan.verifikasi', $pengajuan), ['action' => 'setujui'])
        ->assertSessionHas('success');

    expect($pengajuan->refresh()->status)->toBe(PengajuanStatus::Diproses);
});

test('mahasiswa tidak dapat membuka halaman verifikasi', function () {
    $this->actingAs(mahasiswa())
        ->get(route('admin.pengajuan.verifikasi.index'))
        ->assertRedirect(route('mahasiswa.dashboard'));
});
