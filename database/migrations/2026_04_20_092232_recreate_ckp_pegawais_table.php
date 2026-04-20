<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::dropIfExists('ckp_pegawais');

        Schema::create('ckp_pegawais', function (Blueprint $table) {
            $table->uuid('id_ckp')->primary();
            $table->foreignUuid('id_pegawai')->constrained('pegawais', 'id_pegawai')->cascadeOnDelete();

            $table->uuidMorphs('ckpable');
            $table->unique(['ckpable_type', 'ckpable_id']); // prevent duplikasi data di DB

            $table->enum('tipe_ckp', ['Anggota Tim', 'Ketua Tim', 'Pimpinan']);            $table->text('uraian');
            $table->enum('jenis_ckp', ['Utama', 'Tambahan']);
            $table->integer('target_kuantitas');
            $table->string('satuan');

            $table->string('kode_butir_kegiatan')->nullable();
            $table->decimal('angka_kredit', 8, 2)->nullable();
            $table->text('keterangan')->nullable();

            $table->integer('realisasi')->nullable();
            $table->decimal('persentase_realisasi', 5, 2)->nullable();
            $table->integer('tingkat_kualitas')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ckp_pegawais');
    }
};
