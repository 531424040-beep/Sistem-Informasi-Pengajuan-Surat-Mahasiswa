<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        User::firstOrCreate(
            ['email' => 'admin@kampus.ac.id'],
            [
                'name' => 'Admin Akademik',
                'username' => 'admin.akademik',
                'nim' => null,
                'password' => $password,
                'program_studi' => null,
                'fakultas' => null,
                'role' => User::ROLE_ADMIN,
            ],
        );

        $mahasiswas = [
            ['name' => 'Budi Santoso', 'username' => 'budi.santoso', 'nim' => '20210001', 'email' => 'budi@mahasiswa.ac.id', 'program_studi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik'],
            ['name' => 'Siti Aminah', 'username' => 'siti.aminah', 'nim' => '20210002', 'email' => 'siti@mahasiswa.ac.id', 'program_studi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik'],
            ['name' => 'Andi Wijaya', 'username' => 'andi.wijaya', 'nim' => '20220003', 'email' => 'andi@mahasiswa.ac.id', 'program_studi' => 'Manajemen', 'fakultas' => 'Fakultas Ekonomi dan Bisnis'],
        ];

        foreach ($mahasiswas as $mahasiswa) {
            User::firstOrCreate(
                ['email' => $mahasiswa['email']],
                array_merge($mahasiswa, [
                    'password' => $password,
                    'role' => User::ROLE_MAHASISWA,
                ]),
            );
        }

        $this->call(JenisSuratSeeder::class);
    }
}
