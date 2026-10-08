<?php

namespace Database\Factories;

use App\Enums\PengajuanStatus;
use App\Models\JenisSurat;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pengajuan>
 */
class PengajuanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_pengajuan' => 'SP-'.now()->year.'-'.str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'jenis_surat_id' => JenisSurat::factory(),
            'tanggal_pengajuan' => now()->toDateString(),
            'keperluan' => fake()->sentence(6),
            'keterangan' => fake()->sentence(4),
            'status' => PengajuanStatus::Diajukan,
        ];
    }

    /**
     * Konfigurasi status tertentu.
     */
    public function status(PengajuanStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
