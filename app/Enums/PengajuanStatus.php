<?php

namespace App\Enums;

enum PengajuanStatus: string
{
    case Diajukan = 'Diajukan';
    case Diperiksa = 'Diperiksa';
    case PerluPerbaikan = 'Perlu Perbaikan';
    case Diproses = 'Diproses';
    case Selesai = 'Selesai';
    case Ditolak = 'Ditolak';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->value;
        }

        return $options;
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Diajukan => 'bg-sky-100 text-sky-700',
            self::Diperiksa => 'bg-amber-100 text-amber-700',
            self::PerluPerbaikan => 'bg-orange-100 text-orange-700',
            self::Diproses => 'bg-indigo-100 text-indigo-700',
            self::Selesai => 'bg-emerald-100 text-emerald-700',
            self::Ditolak => 'bg-red-100 text-red-700',
        };
    }
}
