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
        Schema::create('ckp_pegawais', function (Blueprint $table) {
            $table->uuid('id_ckp')->primary();

            $table->uuid('id_pegawai');
            $table->uuid('id_penugasan');

            $table->text('uraian');
            $table->enum('jenis_ckp', ['utama', 'tambahan']);
            $table->string('satuan');
            $table->integer('target_kuantitas');

            $table->string('kode_butir_kegiatan')->nullable();
            $table->decimal('angka_kredit', 8, 2)->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // FK
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawais')->cascadeOnDelete();
            $table->foreign('id_penugasan')->references('id_penugasan')->on('penugasans')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ckp_pegawai');
    }
};
