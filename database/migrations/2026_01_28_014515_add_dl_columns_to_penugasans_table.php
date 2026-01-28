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
        Schema::table('penugasans', function (Blueprint $table) {
            $table->boolean('butuh_dl')
                ->default(false)
                ->after('status');

        $table->enum('status_dl', ['Menunggu', 'ACC', 'Ditolak'])
                ->nullable()
                ->after('butuh_dl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasans', function (Blueprint $table) {
            $table->dropColumn(['butuh_dl', 'status_dl']);
        });
    }
};
