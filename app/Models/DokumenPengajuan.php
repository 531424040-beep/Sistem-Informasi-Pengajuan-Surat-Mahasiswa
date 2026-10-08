<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pengajuan_id', 'persyaratan_id', 'nama_file', 'file_path'])]
class DokumenPengajuan extends Model
{
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function persyaratan(): BelongsTo
    {
        return $this->belongsTo(Persyaratan::class);
    }
}
