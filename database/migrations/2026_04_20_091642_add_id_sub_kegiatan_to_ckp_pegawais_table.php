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
        Schema::table('ckp_pegawais', function (Blueprint $table) {
            // 1. Tambah kolom id_sub_kegiatan (nullable)
            $table->uuid('id_sub_kegiatan')->nullable()->after('id_penugasan');
            
            // 2. Tambah foreign key constraint ke tabel sub_kegiatans
            $table->foreign('id_sub_kegiatan')
                ->references('id_sub_kegiatan')
                ->on('sub_kegiatans')
                ->onDelete('cascade');
            
            // 3. Ubah kolom id_penugasan menjadi nullable
            //    (karena CKP Ketua Tim tidak memiliki id_penugasan)
            $table->uuid('id_penugasan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ckp_pegawais', function (Blueprint $table) {
            // 1. Hapus foreign key constraint
            $table->dropForeign(['id_sub_kegiatan']);
            
            // 2. Hapus kolom id_sub_kegiatan
            $table->dropColumn('id_sub_kegiatan');
            
            // 3. Kembalikan id_penugasan ke NOT NULL
            //    PERHATIAN: Pastikan tidak ada data dengan id_penugasan = NULL
            //    sebelum menjalankan rollback
            $table->uuid('id_penugasan')->nullable(false)->change();
        });
    }
};