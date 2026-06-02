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
        Schema::create('kegiatan_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kegiatan_id')->unique();
            $table->uuid('from_ketua_id');
            $table->uuid('to_ketua_id');
            $table->date('transferred_at');
            $table->timestamps();

            $table->foreign('kegiatan_id')->references('id_kegiatan')->on('kegiatans')->onDelete('cascade');
            $table->foreign('from_ketua_id')->references('id_pegawai')->on('pegawais')->onDelete('cascade');
            $table->foreign('to_ketua_id')->references('id_pegawai')->on('pegawais')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_transfers');
    }
};
