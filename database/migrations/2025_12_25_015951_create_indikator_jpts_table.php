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
        Schema::create('indikator_jpts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rencana_jpt')->constrained('rencana_jpts')->onDelete('cascade');
            $table->string('nama_indikator_jpt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_jpts');
    }
};
