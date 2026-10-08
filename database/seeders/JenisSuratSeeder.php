<?php

namespace Database\Seeders;

use App\Models\JenisSurat;
use Illuminate\Database\Seeder;

class JenisSuratSeeder extends Seeder
{
    /**
     * Seed data awal jenis surat beserta persyaratannya.
     */
    public function run(): void
    {
        $jenisSurats = [
            [
                'nama_surat' => 'Surat Aktif Kuliah',
                'deskripsi' => 'Surat keterangan yang menyatakan bahwa mahasiswa berstatus aktif sebagai mahasiswa pada program studi dan fakultas tertentu.',
                'persyaratans' => [
                    ['nama_persyaratan' => 'Kartu Tanda Mahasiswa (KTM)', 'wajib' => true],
                    ['nama_persyaratan' => 'KHS semester terakhir', 'wajib' => true],
                ],
            ],
            [
                'nama_surat' => 'Surat Keterangan Mahasiswa',
                'deskripsi' => 'Surat keterangan umum yang memuat identitas mahasiswa untuk keperluan administrasi di luar kampus.',
                'persyaratans' => [
                    ['nama_persyaratan' => 'Kartu Tanda Mahasiswa (KTM)', 'wajib' => true],
                    ['nama_persyaratan' => 'KTP', 'wajib' => false],
                ],
            ],
            [
                'nama_surat' => 'Surat Pengantar Penelitian',
                'deskripsi' => 'Surat pengantar yang ditujukan kepada instansi/lembaga tempat mahasiswa melakukan penelitian atau magang.',
                'persyaratans' => [
                    ['nama_persyaratan' => 'Proposal penelitian', 'wajib' => true],
                    ['nama_persyaratan' => 'Kartu Tanda Mahasiswa (KTM)', 'wajib' => true],
                    ['nama_persyaratan' => 'Surat bimbingan dosen pembimbing', 'wajib' => false],
                ],
            ],
            [
                'nama_surat' => 'Surat Izin',
                'deskripsi' => 'Surat izin ketidakhadiran mahasiswa dalam kegiatan perkuliahan atau kegiatan kampus.',
                'persyaratans' => [
                    ['nama_persyaratan' => 'Surat izin dari pihak berkepentingan', 'wajib' => true],
                    ['nama_persyaratan' => 'Kartu Tanda Mahasiswa (KTM)', 'wajib' => false],
                ],
            ],
        ];

        foreach ($jenisSurats as $jenisSurat) {
            $jenisSuratModel = JenisSurat::firstOrCreate(
                ['nama_surat' => $jenisSurat['nama_surat']],
                [
                    'deskripsi' => $jenisSurat['deskripsi'],
                    'status' => true,
                ],
            );

            foreach ($jenisSurat['persyaratans'] as $persyaratan) {
                $jenisSuratModel->persyaratans()->firstOrCreate(
                    ['nama_persyaratan' => $persyaratan['nama_persyaratan']],
                    ['wajib' => $persyaratan['wajib']],
                );
            }
        }
    }
}
