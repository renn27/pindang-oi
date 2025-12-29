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
        Schema::create('penugasans', function (Blueprint $table) {
            $table->uuid('id_penugasan')->primary();
            $table->foreignUuid('id_anggota')->nullable()->constrained('pegawais', 'id_pegawai')->nullOnDelete();
            $table->foreignUuid('id_sub_kegiatan')->constrained('sub_kegiatans', 'id_sub_kegiatan')->cascadeOnDelete();
            $table->integer('target');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['Belum Dikirim', 'Sudah Dikirim', 'Masih Revisi', 'Sudah Diterima'])->default('Belum Dikirim');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasans');
    }
};
