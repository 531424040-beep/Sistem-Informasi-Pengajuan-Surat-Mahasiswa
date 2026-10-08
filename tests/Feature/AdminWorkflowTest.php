<?php

use App\Enums\PengajuanStatus;
use App\Models\Pengajuan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    $this->petugas = admin();
});

test('admin dapat melihat semua pengajuan', function () {
    Pengajuan::factory()->count(3)->create();

    $this->actingAs($this->petugas)
        ->get(route('admin.pengajuan.index'))
        ->assertOk();
});

test('pengajuan diajukan dapat ditandai diperiksa', function () {
    $pengajuan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diajukan]);

    $this->actingAs($this->petugas)
        ->post(route('admin.pengajuan.periksa', $pengajuan))
        ->assertRedirect();

    expect($pengajuan->refresh()->status)->toBe(PengajuanStatus::Diperiksa);
    expect($pengajuan->riwayatStatuses()->where('status', 'Diperiksa')->count())->toBe(1);
});

test('pengajuan tidak berstatus diajukan tidak bisa ditandai diperiksa', function () {
    $pengajuan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diproses]);

    $this->actingAs($this->petugas)
        ->post(route('admin.pengajuan.periksa', $pengajuan))
        ->assertSessionHas('error');

    expect($pengajuan->refresh()->status)->toBe(PengajuanStatus::Diproses);
});

test('pengajuan yang disetujui berubah menjadi diproses', function () {
    $pengajuan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diperiksa]);

    $this->actingAs($this->petugas)
        ->post(route('admin.pengajuan.verifikasi', $pengajuan), ['action' => 'setujui'])
        ->assertSessionHas('success');

    $pengajuan->refresh();
    expect($pengajuan->status)->toBe(PengajuanStatus::Diproses);
    expect($pengajuan->processed_by)->toBe($this->petugas->id);
    expect($pengajuan->riwayatStatuses()->where('status', 'Diproses')->count())->toBe(1);
});

test('verifikasi hanya boleh pada pengajuan berstatus diperiksa', function () {
    $pengajuan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diajukan]);

    $this->actingAs($this->petugas)
        ->post(route('admin.pengajuan.verifikasi', $pengajuan), ['action' => 'setujui'])
        ->assertSessionHas('error');

    expect($pengajuan->refresh()->status)->toBe(PengajuanStatus::Diajukan);
});

test('meminta perbaikan wajib menyertakan catatan', function () {
    $pengajuan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diperiksa]);

    $this->actingAs($this->petugas)
        ->post(route('admin.pengajuan.verifikasi', $pengajuan), ['action' => 'perbaikan'])
        ->assertSessionHasErrors('catatan');

    expect($pengajuan->refresh()->status)->toBe(PengajuanStatus::Diperiksa);
});

test('meminta perbaikan menyimpan catatan admin', function () {
    $pengajuan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diperiksa]);

    $this->actingAs($this->petugas)
        ->post(route('admin.pengajuan.verifikasi', $pengajuan), [
            'action' => 'perbaikan',
            'catatan' => 'KTM tidak terbaca, unggah ulang.',
        ])
        ->assertSessionHas('success');

    $pengajuan->refresh();
    expect($pengajuan->status)->toBe(PengajuanStatus::PerluPerbaikan);
    expect($pengajuan->catatan_admin)->toBe('KTM tidak terbaca, unggah ulang.');
});

test('menolak pengajuan wajib menyertakan alasan', function () {
    $pengajuan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diperiksa]);

    $this->actingAs($this->petugas)
        ->post(route('admin.pengajuan.verifikasi', $pengajuan), ['action' => 'tolak'])
        ->assertSessionHasErrors('catatan');
});

test('menolak pengajuan mengubah status menjadi ditolak', function () {
    $pengajuan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diperiksa]);

    $this->actingAs($this->petugas)
        ->post(route('admin.pengajuan.verifikasi', $pengajuan), [
            'action' => 'tolak',
            'catatan' => 'Persyaratan tidak lengkap.',
        ])
        ->assertSessionHas('success');

    $pengajuan->refresh();
    expect($pengajuan->status)->toBe(PengajuanStatus::Ditolak);
    expect($pengajuan->catatan_admin)->toBe('Persyaratan tidak lengkap.');
});

test('mengunggah surat hasil menyelesaikan pengajuan', function () {
    $pengajuan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diproses]);

    $this->actingAs($this->petugas)
        ->post(route('admin.pengajuan.selesai', $pengajuan), [
            'file_surat' => UploadedFile::fake()->create('surat.pdf', 200, 'application/pdf'),
        ])
        ->assertSessionHas('success');

    $pengajuan->refresh();
    expect($pengajuan->status)->toBe(PengajuanStatus::Selesai);
    expect($pengajuan->file_surat)->not->toBeNull();
    expect($pengajuan->completed_at)->not->toBeNull();
    Storage::disk('local')->assertExists($pengajuan->file_surat);
});

test('surat hasil harus berformat PDF', function () {
    $pengajuan = Pengajuan::factory()->create(['status' => PengajuanStatus::Diproses]);

    $this->actingAs($this->petugas)
        ->post(route('admin.pengajuan.selesai', $pengajuan), [
            'file_surat' => UploadedFile::fake()->create('surat.docx', 200, 'application/msword'),
        ])
        ->assertSessionHasErrors('file_surat');

    expect($pengajuan->refresh()->status)->toBe(PengajuanStatus::Diproses);
});

test('alur lengkap pengajuan hingga selesai dan unduh mahasiswa', function () {
    $mahasiswa = mahasiswa();
    $jenisSurat = jenisSuratDenganPersyaratan();
    $persyaratan = $jenisSurat->persyaratans->first();

    // 1. Mahasiswa mengajukan
    $this->actingAs($mahasiswa)->post(route('mahasiswa.pengajuan.store'), [
        'jenis_surat_id' => $jenisSurat->id,
        'keperluan' => 'Surat aktif kuliah untuk beasiswa',
        'dokumen' => [$persyaratan->id => UploadedFile::fake()->create('ktm.pdf', 100, 'application/pdf')],
    ]);
    $pengajuan = Pengajuan::first();
    expect($pengajuan->status)->toBe(PengajuanStatus::Diajukan);

    // 2. Admin memeriksa lalu menyetujui
    $this->actingAs($this->petugas)->post(route('admin.pengajuan.periksa', $pengajuan));
    $this->actingAs($this->petugas)->post(route('admin.pengajuan.verifikasi', $pengajuan), ['action' => 'setujui']);
    expect($pengajuan->refresh()->status)->toBe(PengajuanStatus::Diproses);

    // 3. Admin mengunggah surat hasil
    $this->actingAs($this->petugas)->post(route('admin.pengajuan.selesai', $pengajuan), [
        'file_surat' => UploadedFile::fake()->create('surat-akhir.pdf', 200, 'application/pdf'),
    ]);
    expect($pengajuan->refresh()->status)->toBe(PengajuanStatus::Selesai);

    // 4. Mahasiswa mengunduh surat hasil
    $this->actingAs($mahasiswa)
        ->get(route('mahasiswa.pengajuan.surat.download', $pengajuan))
        ->assertOk();

    // 5. Riwayat lengkap
    expect($pengajuan->riwayatStatuses()->pluck('status')->all())
        ->toBe(['Diajukan', 'Diperiksa', 'Diproses', 'Selesai']);
});

test('admin tidak dapat mengakses route mahasiswa', function () {
    $this->actingAs($this->petugas)
        ->get(route('mahasiswa.dashboard'))
        ->assertRedirect(route('admin.dashboard'));
});

test('file privat tidak dapat diakses langsung melalui route storage', function () {
    Storage::disk('local')->put('dokumen/rahasia.pdf', 'isi-rahasia');

    $this->get('/storage/dokumen/rahasia.pdf')->assertNotFound();
});
