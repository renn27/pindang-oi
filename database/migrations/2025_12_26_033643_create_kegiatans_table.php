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
            $table->foreignUuid('id_penanggung_jawab')->nullable()->constrained('pegawais', 'id_pegawai')->nullOnDelete();
            $table->year('tahun_kegiatan');
            $table->foreignId('rk_jpt')->nullable()->constrained('rencana_jpts')->nullOnDelete();
            $table->foreignId('iki_jpt')->nullable()->constrained('indikator_jpts')->nullOnDelete();
            $table->string('nama_rk_kegiatan');
            $table->timestamps();
            $table->softDeletes(); // Menambahkan kolom deleted_at

            $table->index(['rk_jpt']);
            $table->index(['iki_jpt']);
            $table->index(['id_penanggung_jawab']);

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
