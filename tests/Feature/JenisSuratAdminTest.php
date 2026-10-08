<?php

use App\Models\JenisSurat;
use App\Models\Pengajuan;

beforeEach(function () {
    $this->petugas = admin();
});

test('admin dapat menambah jenis surat', function () {
    $this->actingAs($this->petugas)->post(route('admin.jenis-surat.store'), [
        'nama_surat' => 'Surat Cuti Kuliah',
        'deskripsi' => 'Surat izin tidak mengikuti kuliah satu semester.',
        'status' => '1',
    ])->assertSessionHas('success');

    expect(JenisSurat::where('nama_surat', 'Surat Cuti Kuliah')->exists())->toBeTrue();
});

test('nama surat tidak boleh duplikat', function () {
    JenisSurat::factory()->create(['nama_surat' => 'Surat Aktif Kuliah']);

    $this->actingAs($this->petugas)->post(route('admin.jenis-surat.store'), [
        'nama_surat' => 'Surat Aktif Kuliah',
        'deskripsi' => 'Duplikat',
        'status' => '1',
    ])->assertSessionHasErrors('nama_surat');
});

test('admin dapat mengubah jenis surat termasuk status aktif', function () {
    $jenisSurat = JenisSurat::factory()->create();

    $this->actingAs($this->petugas)->put(route('admin.jenis-surat.update', $jenisSurat), [
        'nama_surat' => $jenisSurat->nama_surat.' (Diubah)',
        'deskripsi' => 'Deskripsi baru',
        'status' => '0',
    ])->assertSessionHas('success');

    $jenisSurat->refresh();
    expect($jenisSurat->status)->toBeFalse();
    expect($jenisSurat->deskripsi)->toBe('Deskripsi baru');
});

test('jenis surat nonaktif tidak ditampilkan ke mahasiswa', function () {
    JenisSurat::factory()->inactive()->create(['nama_surat' => 'Surat Nonaktif']);
    JenisSurat::factory()->create(['nama_surat' => 'Surat Aktif']);

    $mahasiswa = mahasiswa();

    $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.surat.index'));

    $response->assertOk();
    $response->assertSee('Surat Aktif');
    $response->assertDontSee('Surat Nonaktif');
});

test('admin dapat menghapus jenis surat yang belum digunakan', function () {
    $jenisSurat = JenisSurat::factory()->create();

    $this->actingAs($this->petugas)
        ->delete(route('admin.jenis-surat.destroy', $jenisSurat))
        ->assertRedirect(route('admin.jenis-surat.index'));

    expect(JenisSurat::find($jenisSurat->id))->toBeNull();
});

test('jenis surat yang sudah digunakan pengajuan tidak dapat dihapus', function () {
    $jenisSurat = JenisSurat::factory()->create();
    Pengajuan::factory()->for($jenisSurat)->create();

    $this->actingAs($this->petugas)
        ->delete(route('admin.jenis-surat.destroy', $jenisSurat))
        ->assertSessionHas('error');

    expect(JenisSurat::find($jenisSurat->id))->not->toBeNull();
});

test('admin dapat menambah dan menghapus persyaratan', function () {
    $jenisSurat = JenisSurat::factory()->create();

    $this->actingAs($this->petugas)
        ->post(route('admin.persyaratan.store', $jenisSurat), [
            'nama_persyaratan' => 'KHS terakhir',
            'wajib' => '1',
        ])
        ->assertSessionHas('success');

    $persyaratan = $jenisSurat->persyaratans()->first();
    expect($persyaratan->nama_persyaratan)->toBe('KHS terakhir');

    $this->actingAs($this->petugas)
        ->delete(route('admin.persyaratan.destroy', [$jenisSurat, $persyaratan]))
        ->assertSessionHas('success');

    expect($jenisSurat->persyaratans()->count())->toBe(0);
});

test('mahasiswa tidak dapat mengelola jenis surat', function () {
    $mahasiswa = mahasiswa();

    $this->actingAs($mahasiswa)
        ->get(route('admin.jenis-surat.index'))
        ->assertRedirect(route('mahasiswa.dashboard'));
});
