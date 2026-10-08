<?php

use App\Models\JenisSurat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change this using the "pest()" function to bind different classes.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Obviously, you can extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may sometimes need to write some testing code
| specific to your project that you don't want to repeat in every file. Here you can also
| expose global testing helpers that will be available to all of your tests.
|
*/

/**
 * Membuat akun mahasiswa untuk testing.
 */
function mahasiswa(array $attributes = []): User
{
    return User::factory()->create($attributes);
}

/**
 * Membuat akun admin untuk testing.
 */
function admin(array $attributes = []): User
{
    return User::factory()->admin()->create($attributes);
}

/**
 * Membuat jenis surat lengkap dengan persyaratan wajib.
 */
function jenisSuratDenganPersyaratan(bool $wajib = true, array $attributes = []): JenisSurat
{
    $jenisSurat = JenisSurat::factory()->create($attributes);
    $jenisSurat->persyaratans()->create([
        'nama_persyaratan' => 'Kartu Tanda Mahasiswa',
        'wajib' => $wajib,
    ]);

    return $jenisSurat;
}

/**
 * File upload dummy PDF untuk testing.
 */
function filePdfDummy(string $name = 'dokumen.pdf'): UploadedFile
{
    return UploadedFile::fake()->create($name, 100, 'application/pdf');
}
