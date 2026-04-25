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
            $table->string('bulan_pengiriman', 7)->nullable()->after('tanggal_pengiriman');
            $table->enum('tipe_pengiriman', ['Cicilan', 'Pelunasan'])->nullable()->after('bulan_pengiriman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengirimans', function (Blueprint $table) {
            $table->dropColumn(['bulan_pengiriman', 'tipe_pengiriman']);
        });
    }
};
