<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RencanaJPT;

class RencanaJPTSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Terwujudnya Penyediaan Data dan Insight Statistik Kependudukan dan Ketenagakerjaan yang Berkualitas',
            'Terwujudnya Penyediaan Data dan Insight Statistik Kesejahteraan Rakyat yang Berkualitas',
            'Terwujudnya Penyediaan Data dan Insight Statistik Ketahanan Sosial yang Berkualitas',
            'Terwujudnya Penyediaan Data dan Insight Statistik Tanaman Pangan, Hortikultura, dan Perkebunan yang Berkualitas',
            'Terwujudnya Penyediaan Data dan Insight Statistik Peternakan, Perikanan, dan Kehutanan yang Berkualitas',
            'Terwujudnya Penyediaan Data dan Insight Statistik Industri yang Berkualitas',
            'Terwujudnya Penyediaan Data dan Insight Statistik Distribusi yang Berkualitas',
            'Terwujudnya Penyediaan Data dan Insight Statistik Harga yang Berkualitas',
            'Terwujudnya Penyediaan Data dan Insight Statistik Keuangan, Teknologi Informasi, dan Pariwisata yang Berkualitas',
            'Terwujudnya Penyediaan Data dan Insight Statistik Lintas Sektor yang Berkualitas',
            'Terwujudnya Kapasitas Tata Kelola Pemerintah Desa untuk Menghasilkan Statistik Berkualitas',
            'Terwujudnya Penguatan Penyelenggaraan Pembinaan Statistik Sektoral',
            'Terwujudnya Kemudahan Akses Data BPS',
            'Terwujudnya Dukungan Manajemen pada BPS Provinsi dan Kabupaten/Kota',
        ];

        foreach ($data as $nama) {
            RencanaJPT::firstOrCreate([
                'tahun' => 2026,
                'nama_rencana_jpt' => $nama,
            ]);
        }
    }
}
