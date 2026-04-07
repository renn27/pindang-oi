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
        Schema::table('jenis_kegiatans', function (Blueprint $table) {
            $table->boolean('butuh_dl_atau_translok')
                ->default(false)
                ->after('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_kegiatans', function (Blueprint $table) {
            $table->dropColumn('butuh_dl_atau_translok');
        });
    }
};
