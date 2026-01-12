<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Pastikan tabel bidangs ada
        if (!Schema::hasTable('bidangs')) {
            Schema::create('bidangs', function (Blueprint $table) {
                $table->id('id_bidang');
                $table->string('nama_bidang');
                $table->text('detail_bidang')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Tambah kolom slug jika belum ada
        if (!Schema::hasColumn('bidangs', 'slug')) {
            Schema::table('bidangs', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('nama_bidang');
            });
        }

        // 3. Tambah kolom route_name jika belum ada
        if (!Schema::hasColumn('bidangs', 'route_name')) {
            Schema::table('bidangs', function (Blueprint $table) {
                $table->string('route_name')->nullable()->after('detail_bidang');
            });
        }

        // 4. Update data existing
        $this->updateExistingData();
    }

    public function down()
    {
        // Untuk rollback, hapus kolom
        Schema::table('bidangs', function (Blueprint $table) {
            $table->dropColumn(['slug', 'route_name']);
        });
    }

    private function updateExistingData()
    {
        // Hanya update jika ada data
        if (Schema::hasTable('bidangs')) {
            $bidangs = DB::table('bidangs')->whereNull('deleted_at')->get();
            
            foreach ($bidangs as $bidang) {
                // Buat slug
                $slug = strtolower($bidang->nama_bidang);
                $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
                $slug = preg_replace('/\s+/', '-', $slug);
                $slug = preg_replace('/-+/', '-', $slug);
                $slug = trim($slug, '-');
                
                // Update database
                DB::table('bidangs')
                    ->where('id_bidang', $bidang->id_bidang)
                    ->update([
                        'slug' => $slug,
                        'route_name' => 'tagihan-kerja.' . $slug,
                        'updated_at' => now()
                    ]);
            }
        }
    }
};