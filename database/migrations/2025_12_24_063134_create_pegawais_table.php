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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->uuid('id_pegawai')->primary();
            $table->string('username');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('nama_pegawai');
            $table->string('jabatan');
            $table->text('alamat')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Menambahkan kolom deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
