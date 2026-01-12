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
        Schema::create('penerimaans', function (Blueprint $table) {
            $table->uuid('id_penerimaan')->primary();
            $table->foreignUuid('id_pengiriman')->constrained('pengirimans', 'id_pengiriman')->restrictOnDelete();
            $table->foreignUuid('id_penerima')->nullable()->constrained('pegawais', 'id_pegawai')->nullOnDelete();
            $table->date('tanggal_penerimaan');
            $table->integer('jumlah_diterima');
            $table->enum('status', ['Diterima', 'Revisi']);
            $table->string('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaans');
    }
};
