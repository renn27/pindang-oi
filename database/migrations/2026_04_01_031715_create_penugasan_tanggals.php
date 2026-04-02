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
        Schema::create('penugasan_tanggals', function (Blueprint $table) {
            $table->uuid('id_penugasan_tanggal')->primary();
            $table->foreignUuid('id_penugasan')->nullable()->constrained('penugasans', 'id_penugasan')->cascadeOnDelete();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasan_tanggals');
    }
};
