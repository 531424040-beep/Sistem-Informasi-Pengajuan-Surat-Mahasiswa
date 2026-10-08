<?php

namespace App\Services;

use App\Enums\PengajuanStatus;
use App\Models\DokumenPengajuan;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PengajuanService
{
    /**
     * Simpan pengajuan baru lengkap dengan dokumen dan riwayat status awal.
     *
     * @param  array<string, mixed>  $data
     */
    public function buatPengajuan(User $mahasiswa, array $data, array $files): Pengajuan
    {
        return DB::transaction(function () use ($mahasiswa, $data, $files) {
            $pengajuan = Pengajuan::create([
                'nomor_pengajuan' => Pengajuan::generateNomor(),
                'user_id' => $mahasiswa->id,
                'jenis_surat_id' => $data['jenis_surat_id'],
                'tanggal_pengajuan' => now()->toDateString(),
                'keperluan' => $data['keperluan'],
                'keterangan' => $data['keterangan'] ?? null,
                'status' => PengajuanStatus::Diajukan,
                'judul_penelitian' => $data['judul_penelitian'] ?? null,
                'instansi_tujuan' => $data['instansi_tujuan'] ?? null,
                'alamat_instansi' => $data['alamat_instansi'] ?? null,
                'tujuan_penelitian' => $data['tujuan_penelitian'] ?? null,
            ]);

            $this->simpanDokumen($pengajuan, $files);
            $this->catatStatus($pengajuan, PengajuanStatus::Diajukan, $mahasiswa, 'Pengajuan dikirim oleh mahasiswa.');

            return $pengajuan;
        });
    }

    /**
     * Ganti/masukkan dokumen pengajuan yang sedang diperbaiki.
     *
     * @param  array<string, UploadedFile>  $files
     */
    public function perbaikiPengajuan(Pengajuan $pengajuan, array $data, array $files): Pengajuan
    {
        return DB::transaction(function () use ($pengajuan, $data, $files) {
            $pengajuan->update([
                'keperluan' => $data['keperluan'],
                'keterangan' => $data['keterangan'] ?? null,
                'judul_penelitian' => $data['judul_penelitian'] ?? null,
                'instansi_tujuan' => $data['instansi_tujuan'] ?? null,
                'alamat_instansi' => $data['alamat_instansi'] ?? null,
                'tujuan_penelitian' => $data['tujuan_penelitian'] ?? null,
                'status' => PengajuanStatus::Diajukan,
                'catatan_admin' => null,
            ]);

            $this->simpanDokumen($pengajuan, $files);

            $this->catatStatus(
                $pengajuan,
                PengajuanStatus::Diajukan,
                $pengajuan->user,
                'Perbaikan dikirim ulang oleh mahasiswa.'
            );

            return $pengajuan;
        });
    }

    /**
     * Simpan file dokumen (ganti file lama bila diunggah ulang).
     *
     * @param  array<string, UploadedFile>  $files
     */
    public function simpanDokumen(Pengajuan $pengajuan, array $files): void
    {
        foreach ($files as $persyaratanId => $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('dokumen', 'local');

            DokumenPengajuan::updateOrCreate(
                [
                    'pengajuan_id' => $pengajuan->id,
                    'persyaratan_id' => $persyaratanId,
                ],
                [
                    'nama_file' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ],
            );
        }
    }

    /**
     * Ubah status pengajuan dan tulis riwayat status.
     */
    public function ubahStatus(
        Pengajuan $pengajuan,
        PengajuanStatus $status,
        User $aktor,
        ?string $keterangan = null,
    ): void {
        $pengajuan->update(['status' => $status]);
        $this->catatStatus($pengajuan, $status, $aktor, $keterangan);
    }

    /**
     * Catat perubahan status pada tabel riwayat_statuses.
     */
    public function catatStatus(
        Pengajuan $pengajuan,
        PengajuanStatus $status,
        User $aktor,
        ?string $keterangan = null,
    ): void {
        $pengajuan->riwayatStatuses()->create([
            'user_id' => $aktor->id,
            'status' => $status->value,
            'keterangan' => $keterangan,
        ]);
    }

    /**
     * Nama file upload yang aman dan unik.
     */
    public static function namaFileUnik(UploadedFile $file): string
    {
        return Str::uuid().'.'.$file->getClientOriginalExtension();
    }
}
