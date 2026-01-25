<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BidangSeeder extends Seeder
{
    /**
     * Data bidang kerja (urut FIX)
     */
    private array $dataBidang = [
        ['urutan' => 1,  'nama_bidang' => 'Subbagian Umum',     'detail_bidang' => 'Subbagian Umum'],
        ['urutan' => 2,  'nama_bidang' => 'Statistik Sosial',   'detail_bidang' => 'Statistik Sosial (Fungsi Statistik Sosial)'],
        ['urutan' => 3,  'nama_bidang' => 'Statistik Produksi', 'detail_bidang' => 'Statistik Produksi (Fungsi Statistik Produksi)'],
        ['urutan' => 4,  'nama_bidang' => 'Statistik Distribusi','detail_bidang' => 'Statistik Distribusi (Fungsi Statistik Distribusi)'],
        ['urutan' => 5,  'nama_bidang' => 'NWAS',               'detail_bidang' => 'NWAS (Neraca Wilayah dan Analisis Statistik)'],
        ['urutan' => 6,  'nama_bidang' => 'SPBE',               'detail_bidang' => 'SPBE (Sistem Pemerintahan Berbasis Elektronik)'],
        ['urutan' => 7,  'nama_bidang' => 'DLS',                'detail_bidang' => 'DLS (Diseminasi dan Layanan Statistik)'],
        ['urutan' => 8,  'nama_bidang' => 'PEKPPP',             'detail_bidang' => 'PEKPPP (Pemantauan dan Evaluasi Kinerja Penyelenggaraan Pelayanan Publik)'],
        ['urutan' => 9,  'nama_bidang' => 'KIP',                'detail_bidang' => 'KIP (Keterbukaan Informasi Publik)'],
        ['urutan' => 10, 'nama_bidang' => 'PSS',                'detail_bidang' => 'PSS (Pembinaan Statistik Sektoral)'],
        ['urutan' => 11, 'nama_bidang' => 'Desa Cantik',        'detail_bidang' => 'Desa Cantik (Pembinaan Program Desa Cantik)'],
        ['urutan' => 12, 'nama_bidang' => 'SBuKB',              'detail_bidang' => 'SBuKB (Statistik Berdampak untuk Kampus Berdampak)'],
        ['urutan' => 13, 'nama_bidang' => 'DTSEN',              'detail_bidang' => 'DTSEN (Data Tunggal Sosial Ekonomi Nasional)'],
        ['urutan' => 14, 'nama_bidang' => 'SAKIP',              'detail_bidang' => 'SAKIP (Sistem Akuntabilitas Kinerja Instansi Pemerintah)'],
        ['urutan' => 15, 'nama_bidang' => 'Reformasi Birokrasi', 'detail_bidang' => 'Reformasi Birokrasi (Pembinaan Zona Integritas)'],
        ['urutan' => 16, 'nama_bidang' => 'Manajemen Risiko',   'detail_bidang' => 'Manajemen Risiko (Implementasi Manajemen Risiko)'],
        ['urutan' => 17, 'nama_bidang' => 'Mitra Statistik',    'detail_bidang' => 'Mitra Statistik (Pengelolaan Mitra Statistik)'],
        ['urutan' => 18, 'nama_bidang' => 'Humas',              'detail_bidang' => 'Humas (Kehumasan)'],
    ];

    public function run(): void
    {
        $this->command->info('🧹 Menghapus seluruh data bidang lama...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('bidangs')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🚀 Memulai seeding data bidang kerja...');

        foreach ($this->dataBidang as $data) {
            Bidang::create([
                'urutan'        => $data['urutan'],
                'nama_bidang'   => $data['nama_bidang'],
                'detail_bidang' => $data['detail_bidang'],
                'slug'          => Str::slug($data['nama_bidang']),
            ]);

            $this->command->info("➕ {$data['urutan']}. {$data['nama_bidang']}");
        }

        $this->command->info('🎉 Seeding bidang kerja selesai & FIX!');
    }
}
