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
        Schema::create('agenda_pimpinans', function (Blueprint $table) {
            $table->id();

            $table->string('nama_kegiatan');

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            // 🔥 IKUTIN STYLE KAMU (id_rencana_jpt)
            $table->foreignId('id_rencana_jpt')
                ->constrained('rencana_jpts')
                ->cascadeOnDelete();

            $table->foreignId('id_indikator_jpt')
                ->constrained('indikator_jpts')
                ->cascadeOnDelete();

            $table->text('link_bukti')->nullable();

            $table->enum('status', ['Selesai', 'Belum Selesai'])
                ->default('Belum Selesai');

            $table->timestamps();
            $table->softDeletes();

            // 🔥 optional tapi bagus buat performa
            $table->index('id_rencana_jpt');
            $table->index('id_indikator_jpt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_pimpinans');
    }
};