<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RencanaJPT;
use App\Models\IndikatorJPT;

class IndikatorJPTSeeder extends Seeder
{
    public function run(): void
    {
        $indikatorMap = [
            1 => [
                'Persentase Publikasi/Laporan Statistik Kependudukan dan Ketenagakerjaan yang Berkualitas mencapai 100 persen',
            ],
            2 => [
                'Persentase Publikasi/Laporan Statistik Kesejahteraan Rakyat yang Berkualitas mencapai 100 persen',
            ],
            3 => [
                'Persentase Publikasi/Laporan Statistik Ketahanan Sosial yang Berkualitas mencapai 100 persen',
            ],
            4 => [
                'Persentase Publikasi/Laporan Statistik Tanaman Pangan, Hortikultura, dan Perkebunan yang Berkualitas mencapai 100 persen',
            ],
            5 => [
                'Persentase Publikasi/Laporan Statistik Peternakan, Perikanan, dan Kehutanan yang Berkualitas mencapai 100 persen',
            ],
            6 => [
                'Persentase Publikasi/Laporan Statistik Industri yang Berkualitas mencapai 100 persen',
            ],
            7 => [
                'Persentase Publikasi/Laporan Statistik Distribusi yang Berkualitas mencapai 100 persen',
            ],
            8 => [
                'Persentase Publikasi/Laporan Statistik Harga yang Berkualitas mencapai 100 persen',
            ],
            9 => [
                'Persentase Publikasi/Laporan Statistik Keuangan, Teknologi Informasi, dan Pariwisata yang Berkualitas mencapai 100 persen',
            ],
            10 => [
                'Persentase Publikasi/Laporan Neraca Produksi yang Berkualitas mencapai 100 persen',
                'Persentase Publikasi/Laporan Neraca Pengeluaran yang Berkualitas mencapai 100 persen',
                'Persentase Publikasi/Laporan Analisis dan Pengembangan Statistik yang Berkualitas mencapai 100 persen',
            ],
            11 => [
                'Persentase Kumulatif Desa Berpredikat Desa Cinta Statistik',
            ],
            12 => [
                'Tingkat Penyelenggaraan Pembinaan Statistik Sektoral sesuai Standar',
            ],
            13 => [
                'Indeks Pelayanan Publik - Penilaian Mandiri',
            ],
            14 => [
                'Nilai SAKIP oleh Inspektorat',
                'Indeks Implementasi BerAKHLAK',
            ],
        ];

        foreach ($indikatorMap as $rencanaId => $indikators) {
            $rencana = RencanaJPT::where('id', $rencanaId)->first();

            if (!$rencana) {
                continue;
            }

            foreach ($indikators as $nama) {
                IndikatorJPT::firstOrCreate([
                    'id_rencana_jpt'     => $rencana->id,
                    'nama_indikator_jpt' => $nama,
                ]);
            }
        }
    }
}
