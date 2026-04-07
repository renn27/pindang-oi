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
        Schema::table('ckp_pegawais', function (Blueprint $table) {
            $table->integer('realisasi')->nullable();
            $table->integer('persentase_realisasi')->nullable();
            $table->integer('tingkat_kualitas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ckp_pegawais', function (Blueprint $table) {
            //
        });
    }
};
