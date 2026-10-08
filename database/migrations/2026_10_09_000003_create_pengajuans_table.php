<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan', 20)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surats')->restrictOnDelete();
            $table->date('tanggal_pengajuan');
            $table->string('keperluan');
            $table->text('keterangan')->nullable();
            $table->string('status', 30)->default('Diajukan')->index();
            $table->text('catatan_admin')->nullable();
            $table->string('file_surat')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();

            // Field tambahan khusus Surat Pengantar Penelitian
            $table->string('judul_penelitian')->nullable();
            $table->string('instansi_tujuan')->nullable();
            $table->string('alamat_instansi')->nullable();
            $table->text('tujuan_penelitian')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['jenis_surat_id', 'status']);
            $table->index('tanggal_pengajuan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
