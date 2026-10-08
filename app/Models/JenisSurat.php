<?php

namespace App\Models;

use Database\Factories\JenisSuratFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_surat', 'deskripsi', 'status'])]
class JenisSurat extends Model
{
    /** @use HasFactory<JenisSuratFactory> */
    use HasFactory;

    public function persyaratans(): HasMany
    {
        return $this->hasMany(Persyaratan::class);
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function isActive(): bool
    {
        return (bool) $this->status;
    }
}
