<?php

use App\Enums\PengajuanStatus;
use App\Models\DokumenPengajuan;
use App\Models\Pengajuan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
});

test('mahasiswa dapat membuat pengajuan dengan dokumen', function () {
    $user = mahasiswa();
    $jenisSurat = jenisSuratDenganPersyaratan();
    $persyaratan = $jenisSurat->persyaratans->first();

    $response = $this->actingAs($user)->post(route('mahasiswa.pengajuan.store'), [
        'jenis_surat_id' => $jenisSurat->id,
        'keperluan' => 'Persyaratan beasiswa',
        'keterangan' => 'Mohon diproses',
        'dokumen' => [$persyaratan->id => UploadedFile::fake()->create('ktm.pdf', 100, 'application/pdf')],
    ]);

    $pengajuan = Pengajuan::first();

    $this->assertNotNull($pengajuan);
    $response->assertRedirect(route('mahasiswa.pengajuan.show', $pengajuan));

    expect($pengajuan->nomor_pengajuan)->toMatch('/^SP-\d{4}-\d{4}$/');
    expect($pengajuan->status)->toBe(PengajuanStatus::Diajukan);
    expect($pengajuan->user_id)->toBe($user->id);

    // Dokumen tersimpan di disk privat dengan nama unik
    $dokumen = DokumenPengajuan::first();
    Storage::disk('local')->assertExists($dokumen->file_path);
    expect($dokumen->file_path)->toStartWith('dokumen/');
    expect($dokumen->nama_file)->toBe('ktm.pdf');

    // Riwayat status awal tercatat
    expect($pengajuan->riwayatStatuses()->count())->toBe(1);
    expect($pengajuan->riwayatStatuses()->first()->status)->toBe('Diajukan');
});

test('nomor pengajuan tidak duplikat', function () {
    $user = mahasiswa();
    $jenisSurat = jenisSuratDenganPersyaratan();
    $persyaratan = $jenisSurat->persyaratans->first();

    $payload = [
        'jenis_surat_id' => $jenisSurat->id,
        'keperluan' => 'Keperluan A',
        'dokumen' => [$persyaratan->id => UploadedFile::fake()->create('dok.pdf', 50, 'application/pdf')],
    ];

    $this->actingAs($user)->post(route('mahasiswa.pengajuan.store'), $payload);
    $this->actingAs($user)->post(route('mahasiswa.pengajuan.store'), $payload);

    expect(Pengajuan::count())->toBe(2);
    expect(Pengajuan::pluck('nomor_pengajuan')->unique()->count())->toBe(2);
});

test('dokumen wajib harus diunggah', function () {
    $user = mahasiswa();
    $jenisSurat = jenisSuratDenganPersyaratan();

    $response = $this->actingAs($user)->post(route('mahasiswa.pengajuan.store'), [
        'jenis_surat_id' => $jenisSurat->id,
        'keperluan' => 'Tanpa dokumen',
    ]);

    $response->assertSessionHasErrors();
    expect(Pengajuan::count())->toBe(0);
});

test('format file di luar pdf/jpg/jpeg/png ditolak', function () {
    $user = mahasiswa();
    $jenisSurat = jenisSuratDenganPersyaratan();
    $persyaratan = $jenisSurat->persyaratans->first();

    $response = $this->actingAs($user)->post(route('mahasiswa.pengajuan.store'), [
        'jenis_surat_id' => $jenisSurat->id,
        'keperluan' => 'File salah format',
        'dokumen' => [$persyaratan->id => UploadedFile::fake()->create('virus.exe', 10, 'application/x-msdownload')],
    ]);

    $response->assertSessionHasErrors();
});

test('jenis surat nonaktif tidak dapat diajukan', function () {
    $user = mahasiswa();
    $jenisSurat = jenisSuratDenganPersyaratan(false, ['status' => false]);
    $persyaratan = $jenisSurat->persyaratans->first();

    $response = $this->actingAs($user)->post(route('mahasiswa.pengajuan.store'), [
        'jenis_surat_id' => $jenisSurat->id,
        'keperluan' => 'Surat nonaktif',
        'dokumen' => [$persyaratan->id => UploadedFile::fake()->create('d.pdf', 10, 'application/pdf')],
    ]);

    $response->assertSessionHasErrors('jenis_surat_id');
});

test('mahasiswa tidak dapat melihat pengajuan mahasiswa lain', function () {
    $pemilik = mahasiswa();
    $pengajuan = Pengajuan::factory()->for($pemilik)->create();
    $penggunaLain = mahasiswa();

    $this->actingAs($penggunaLain)
        ->get(route('mahasiswa.pengajuan.show', $pengajuan))
        ->assertForbidden();
});

test('mahasiswa hanya dapat mengubah pengajuan berstatus perlu perbaikan', function () {
    $user = mahasiswa();
    $pengajuan = Pengajuan::factory()->for($user)->create();

    // Status Diajukan: tidak boleh diedit
    $this->actingAs($user)
        ->get(route('mahasiswa.pengajuan.edit', $pengajuan))
        ->assertForbidden();

    $pengajuan->update(['status' => PengajuanStatus::PerluPerbaikan]);

    $this->actingAs($user)
        ->get(route('mahasiswa.pengajuan.edit', $pengajuan))
        ->assertOk();
});

test('perbaikan pengajuan mengembalikan status ke diajukan', function () {
    $user = mahasiswa();
    $jenisSurat = jenisSuratDenganPersyaratan();
    $pengajuan = Pengajuan::factory()->for($user)->for($jenisSurat)->create([
        'status' => PengajuanStatus::PerluPerbaikan,
        'catatan_admin' => 'KTM buram, mohon unggah ulang',
    ]);

    $response = $this->actingAs($user)->put(route('mahasiswa.pengajuan.update', $pengajuan), [
        'keperluan' => 'Keperluan hasil perbaikan',
        'dokumen' => [$jenisSurat->persyaratans->first()->id => UploadedFile::fake()->create('ktm-baru.pdf', 50, 'application/pdf')],
    ]);

    $response->assertRedirect(route('mahasiswa.pengajuan.show', $pengajuan));

    $pengajuan->refresh();
    expect($pengajuan->status)->toBe(PengajuanStatus::Diajukan);
    expect($pengajuan->catatan_admin)->toBeNull();
    expect($pengajuan->keperluan)->toBe('Keperluan hasil perbaikan');

    // Riwayat terakhir mencatat pengiriman ulang perbaikan
    $riwayatTerakhir = $pengajuan->riwayatStatuses()->latest('id')->first();
    expect($riwayatTerakhir->status)->toBe('Diajukan');
    expect($riwayatTerakhir->keterangan)->toContain('Perbaikan');
});

test('mahasiswa dapat mengunduh dokumen miliknya sendiri', function () {
    $user = mahasiswa();
    $jenisSurat = jenisSuratDenganPersyaratan();
    $pengajuan = Pengajuan::factory()->for($user)->for($jenisSurat)->create();

    Storage::disk('local')->put('dokumen/uji.pdf', 'isi-dokumen');
    $dokumen = DokumenPengajuan::create([
        'pengajuan_id' => $pengajuan->id,
        'persyaratan_id' => $jenisSurat->persyaratans->first()->id,
        'nama_file' => 'ktm.pdf',
        'file_path' => 'dokumen/uji.pdf',
    ]);

    $this->actingAs($user)
        ->get(route('mahasiswa.pengajuan.dokumen', [$pengajuan, $dokumen]))
        ->assertOk();
});

test('mahasiswa lain tidak dapat mengunduh dokumen pengajuan orang lain', function () {
    $pemilik = mahasiswa();
    $jenisSurat = jenisSuratDenganPersyaratan();
    $pengajuan = Pengajuan::factory()->for($pemilik)->for($jenisSurat)->create();

    Storage::disk('local')->put('dokumen/uji.pdf', 'isi-dokumen');
    $dokumen = DokumenPengajuan::create([
        'pengajuan_id' => $pengajuan->id,
        'persyaratan_id' => $jenisSurat->persyaratans->first()->id,
        'nama_file' => 'ktm.pdf',
        'file_path' => 'dokumen/uji.pdf',
    ]);

    $this->actingAs(mahasiswa())
        ->get(route('mahasiswa.pengajuan.dokumen', [$pengajuan, $dokumen]))
        ->assertForbidden();
});

test('surat hasil belum bisa diunduh sebelum status selesai', function () {
    $user = mahasiswa();
    $pengajuan = Pengajuan::factory()->for($user)->create([
        'status' => PengajuanStatus::Diproses,
        'file_surat' => 'surat/siap.pdf',
    ]);

    Storage::disk('local')->put('surat/siap.pdf', 'isi-surat');

    $this->actingAs($user)
        ->get(route('mahasiswa.pengajuan.surat.download', $pengajuan))
        ->assertForbidden();
});

test('surat hasil dapat diunduh pemilik setelah selesai', function () {
    $user = mahasiswa();
    $pengajuan = Pengajuan::factory()->for($user)->create([
        'status' => PengajuanStatus::Selesai,
        'file_surat' => 'surat/siap.pdf',
        'completed_at' => now(),
    ]);

    Storage::disk('local')->put('surat/siap.pdf', 'isi-surat');

    $this->actingAs($user)
        ->get(route('mahasiswa.pengajuan.surat.download', $pengajuan))
        ->assertOk();
});

test('mahasiswa tidak dapat mengakses route admin', function () {
    $user = mahasiswa();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('mahasiswa.dashboard'));

    $this->actingAs($user)
        ->get(route('admin.pengajuan.index'))
        ->assertRedirect(route('mahasiswa.dashboard'));
});
