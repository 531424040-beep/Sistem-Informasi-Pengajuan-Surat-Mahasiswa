<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuans')->cascadeOnDelete();
            $table->foreignId('persyaratan_id')->constrained('persyaratans')->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('file_path');
            $table->timestamps();

            $table->unique(['pengajuan_id', 'persyaratan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_pengajuans');
    }
};
