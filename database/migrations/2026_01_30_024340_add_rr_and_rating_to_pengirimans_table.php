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
        Schema::table('pengirimans', function (Blueprint $table) {
            $table->integer('rr_kirim')
                ->nullable()
                ->default(0)
                ->after('bukti_dukung');
            $table->tinyInteger('rating_kirim')
                ->nullable()
                ->default(0)
                ->after('rr_kirim');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengirimans', function (Blueprint $table) {
            $table->dropColumn([
                'rr_kirim',
                'rating_kirim',
            ]);
        });
    }
};
