<?php

namespace App\Models;

use App\Enums\PengajuanStatus;
use Database\Factories\PengajuanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[Fillable([
    'nomor_pengajuan',
    'user_id',
    'jenis_surat_id',
    'tanggal_pengajuan',
    'keperluan',
    'keterangan',
    'status',
    'catatan_admin',
    'file_surat',
    'processed_by',
    'completed_at',
    'judul_penelitian',
    'instansi_tujuan',
    'alamat_instansi',
    'tujuan_penelitian',
])]
class Pengajuan extends Model
{
    /** @use HasFactory<PengajuanFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function dokumenPengajuans(): HasMany
    {
        return $this->hasMany(DokumenPengajuan::class);
    }

    public function riwayatStatuses(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class)->orderBy('created_at')->orderBy('id');
    }

    public function adminPemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'completed_at' => 'datetime',
            'status' => PengajuanStatus::class,
        ];
    }

    /**
     * Membuat nomor pengajuan unik berformat SP-YYYY-NNNN.
     */
    public static function generateNomor(): string
    {
        return DB::transaction(function () {
            $year = date('Y');
            $prefix = 'SP-'.$year.'-';

            $lastNumber = self::where('nomor_pengajuan', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('nomor_pengajuan')
                ->value('nomor_pengajuan');

            $sequence = $lastNumber ? ((int) substr($lastNumber, -4)) + 1 : 1;

            return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
        });
    }

    public function statusIs(PengajuanStatus $status): bool
    {
        return $this->status === $status;
    }

    /**
     * Mahasiswa hanya boleh mengubah pengajuan yang diminta perbaikan.
     */
    public function canBeEdited(): bool
    {
        return $this->statusIs(PengajuanStatus::PerluPerbaikan);
    }
}
