<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->uuid('id_kegiatan')->primary();
            $table->foreignId('id_bidang')->constrained('bidangs', 'id_bidang')->cascadeOnDelete();
            $table->foreignUuid('id_penanggung_jawab')->constrained('pegawais', 'id_pegawai')->cascadeOnDelete();
            $table->year('tahun_kegiatan');
            $table->string('rk_jpt');
            $table->string('iki_jpt');
            $table->string('nama_rk_kegiatan');
            $table->timestamps();
            $table->softDeletes(); // Menambahkan kolom deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};
