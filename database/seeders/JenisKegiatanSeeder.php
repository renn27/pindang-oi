<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisKegiatan;

class JenisKegiatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['jenis_kegiatan' => 'Pengolahan',        'kategori' => 'Utama'],
            ['jenis_kegiatan' => 'Pemeriksaan',       'kategori' => 'Utama'],
            ['jenis_kegiatan' => 'Pendataan',         'kategori' => 'Utama'],
            ['jenis_kegiatan' => 'Pengawasan',        'kategori' => 'Utama'],
            ['jenis_kegiatan' => 'Supervisi',         'kategori' => 'Utama'],
            ['jenis_kegiatan' => 'Perjalanan Dinas',  'kategori' => 'Utama'],
        ];

        foreach ($data as $item) {
            JenisKegiatan::firstOrCreate(
                ['jenis_kegiatan' => $item['jenis_kegiatan']],
                ['kategori' => $item['kategori']]
            );
        }
    }
}
