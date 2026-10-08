<?php

namespace App\Policies;

use App\Models\Pengajuan;
use App\Models\User;

class PengajuanPolicy
{
    /**
     * Mahasiswa hanya boleh melihat pengajuan miliknya; Admin boleh melihat semua.
     */
    public function view(User $user, Pengajuan $pengajuan): bool
    {
        return $user->isAdmin() || $pengajuan->user_id === $user->id;
    }

    /**
     * Mahasiswa hanya boleh mengubah pengajuan miliknya yang berstatus "Perlu Perbaikan".
     */
    public function update(User $user, Pengajuan $pengajuan): bool
    {
        return $pengajuan->user_id === $user->id && $pengajuan->canBeEdited();
    }

    /**
     * Mengunduh dokumen persyaratan: pemilik pengajuan dan Admin.
     */
    public function downloadDokumen(User $user, Pengajuan $pengajuan): bool
    {
        return $user->isAdmin() || $pengajuan->user_id === $user->id;
    }

    /**
     * Mengunduh surat hasil: pemilik pengajuan (setelah Selesai) dan Admin.
     */
    public function downloadSurat(User $user, Pengajuan $pengajuan): bool
    {
        return $user->isAdmin() || $pengajuan->user_id === $user->id;
    }
}
