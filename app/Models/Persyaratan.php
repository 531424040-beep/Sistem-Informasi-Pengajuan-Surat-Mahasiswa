<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['jenis_surat_id', 'nama_persyaratan', 'wajib'])]
class Persyaratan extends Model
{
    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function dokumenPengajuans(): HasMany
    {
        return $this->hasMany(DokumenPengajuan::class);
    }
}
