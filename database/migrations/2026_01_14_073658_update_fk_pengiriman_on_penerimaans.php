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
        Schema::table('penerimaans', function (Blueprint $table) {
            // drop FK lama
            $table->dropForeign(['id_pengiriman']);

            // buat ulang dengan cascade
            $table->foreign('id_pengiriman')
                ->references('id_pengiriman')
                ->on('pengirimans')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penerimaans', function (Blueprint $table) {
            $table->dropForeign(['id_pengiriman']);

            $table->foreign('id_pengiriman')
                ->references('id_pengiriman')
                ->on('pengirimans')
                ->restrictOnDelete();
        });
    }
};
