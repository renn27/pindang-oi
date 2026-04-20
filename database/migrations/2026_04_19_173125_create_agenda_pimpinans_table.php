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
            $table->uuid('id_agenda')->primary();
            $table->string('nama_agenda');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->foreignId('rk_jpt')->nullable()->constrained('rencana_jpts')->nullOnDelete();
            $table->foreignId('iki_jpt')->nullable()->constrained('indikator_jpts')->nullOnDelete();
            $table->integer('target')->nullable();
            $table->string('satuan_target')->nullable();
            $table->integer('realisasi')->nullable();
            $table->text('link_bukti')->nullable();
            $table->enum('status', ['Selesai', 'Belum Selesai'])
                ->default('Belum Selesai');
            $table->timestamps();

            $table->index('rk_jpt');
            $table->index('iki_jpt');
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
