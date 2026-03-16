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
        Schema::table('indikator_jpts', function (Blueprint $table) {
            $table->string('satuan')->nullable();
            $table->integer('target')->nullable();
            $table->integer('realisasi')->nullable();
            $table->enum('status', ['Selesai', 'Belum Selesai'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indikator_jpts', function (Blueprint $table) {
            $table->dropColumn(['satuan', 'target', 'realisasi', 'status']);
        });
    }
};
