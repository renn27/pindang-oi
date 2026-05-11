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
        Schema::create('ckp_download_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_pegawai');
            $table->string('bulan_ckp', 7); // Format: "YYYY-MM"
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('downloaded_at')->useCurrent();

            // Foreign Key
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawais')->onDelete('cascade');

            // Indexing for performance
            $table->index(['id_pegawai', 'bulan_ckp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ckp_download_logs');
    }
};
