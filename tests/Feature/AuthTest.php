<?php

test('halaman utama mengarahkan guest ke login', function () {
    $this->get('/')->assertRedirect(route('login'));
});

test('guest diarahkan ke login saat mengakses halaman terproteksi', function () {
    $this->get(route('mahasiswa.dashboard'))->assertRedirect(route('login'));
});

test('mahasiswa dapat login dan diarahkan ke dashboard', function () {
    $user = mahasiswa(['password' => 'rahasia123']);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'rahasia123',
    ]);

    $response->assertRedirect(route('mahasiswa.dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('admin dapat login dan diarahkan ke dashboard admin', function () {
    $user = admin(['password' => 'rahasia123']);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'rahasia123',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
});

test('login dengan password salah ditolak', function () {
    $user = mahasiswa();

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'salah',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('logout menghapus sesi', function () {
    $user = mahasiswa();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
