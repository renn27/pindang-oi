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
        Schema::create('pengirimans', function (Blueprint $table) {
            $table->uuid('id_pengiriman')->primary();
            $table->foreignUuid('id_penugasan')->constrained('penugasans', 'id_penugasan')->cascadeOnDelete();
            $table->date('tanggal_pengiriman');
            $table->integer('jumlah_dikirim');
            $table->string('media_pengiriman');
            $table->text('bukti_dukung');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengirimans');
    }
};
