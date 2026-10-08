<?php

use App\Models\User;

test('halaman registrasi dapat dibuka oleh guest', function () {
    $this->get(route('register'))->assertOk();
});

test('user login tidak dapat membuka halaman registrasi', function () {
    $this->actingAs(mahasiswa())
        ->get(route('register'))
        ->assertRedirect(url('/'));
});

test('mahasiswa baru dapat mendaftar dan langsung masuk', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Rina Amelia',
        'nim' => '20239999',
        'email' => 'rina@mahasiswa.ac.id',
        'username' => 'rina.amelia',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ]);

    $user = User::where('email', 'rina@mahasiswa.ac.id')->first();

    expect($user)->not->toBeNull();
    expect($user->role)->toBe(User::ROLE_MAHASISWA);
    expect($user->username)->toBe('rina.amelia');
    expect($user->nim)->toBe('20239999');

    $response->assertRedirect(route('mahasiswa.dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('registrasi memvalidasi field wajib', function () {
    $this->post(route('register.store'), [])->assertSessionHasErrors([
        'name', 'nim', 'email', 'username', 'password',
    ]);

    expect(User::count())->toBe(0);
});

test('registrasi menolak email dan NIM yang sudah terdaftar', function () {
    $terdaftar = mahasiswa();

    $this->post(route('register.store'), [
        'name' => 'Pengguna Baru',
        'nim' => $terdaftar->nim,
        'email' => $terdaftar->email,
        'username' => 'username.baru',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertSessionHasErrors(['nim', 'email']);
});

test('registrasi menolak username yang sudah dipakai', function () {
    $terdaftar = mahasiswa(['username' => 'sudah.pakai']);

    $this->post(route('register.store'), [
        'name' => 'Pengguna Baru',
        'nim' => '20248888',
        'email' => 'baru@mahasiswa.ac.id',
        'username' => $terdaftar->username,
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertSessionHasErrors('username');
});

test('konfirmasi password harus cocok dan password minimal 8 karakter', function () {
    $this->post(route('register.store'), [
        'name' => 'Tes Singkat',
        'nim' => '20247777',
        'email' => 'singkat@mahasiswa.ac.id',
        'username' => 'tes.singkat',
        'password' => 'abc',
        'password_confirmation' => 'abcd',
    ])->assertSessionHasErrors(['password']);

    $this->post(route('register.store'), [
        'name' => 'Tes Cocok',
        'nim' => '20247778',
        'email' => 'cocok@mahasiswa.ac.id',
        'username' => 'tes.cocok',
        'password' => 'rahasia123',
        'password_confirmation' => 'salah1234',
    ])->assertSessionHasErrors('password');
});

test('NIM harus berisi angka saja', function () {
    $this->post(route('register.store'), [
        'name' => 'Tes NIM',
        'nim' => 'abc123',
        'email' => 'nim@mahasiswa.ac.id',
        'username' => 'tes.nim',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertSessionHasErrors('nim');
});

test('password registrasi di-hash dan akun bisa login', function () {
    $this->post(route('register.store'), [
        'name' => 'Hash Test',
        'nim' => '20246666',
        'email' => 'hash@mahasiswa.ac.id',
        'username' => 'hash.test',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ]);

    $this->post(route('logout'));

    $this->post(route('login.store'), [
        'email' => 'hash@mahasiswa.ac.id',
        'password' => 'rahasia123',
    ])->assertRedirect(route('mahasiswa.dashboard'));
});
