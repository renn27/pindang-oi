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
        Schema::dropIfExists('penugasan_tanggals');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('penugasan_tanggals', function (Blueprint $table) {
            $table->uuid('id_penugasan_tanggal')->primary();
            $table->uuid('id_penugasan');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
            
            $table->foreign('id_penugasan')->references('id_penugasan')->on('penugasans')->onDelete('cascade');
        });
    }
};
