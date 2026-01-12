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
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kegiatan',
                'satuan_target',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->string('jenis_kegiatan')->after('nama_sub_kegiatan');
            $table->string('satuan_target')->after('jenis_kegiatan');
        });
    }
};
