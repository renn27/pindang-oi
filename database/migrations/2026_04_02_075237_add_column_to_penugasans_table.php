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
            $table->boolean('butuh_translok')
                ->default(false)
                ->after('status_dl');

            $table->enum('status_translok', ['Menunggu', 'ACC', 'Ditolak'])
                ->nullable()
                ->after('butuh_translok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasans', function (Blueprint $table) {
            $table->dropColumn(['butuh_translok', 'status_translok']);
        });
    }
};
