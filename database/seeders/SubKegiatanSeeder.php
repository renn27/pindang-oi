<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubKegiatan;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubKegiatanSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Seeding Sub Kegiatan (BPS Style)');

        $kegiatans = Kegiatan::all();

        if ($kegiatans->isEmpty()) {
            $this->command->error('❌ Data kegiatan belum tersedia');
            return;
        }

        DB::transaction(function () use ($kegiatans) {

            foreach ($kegiatans as $kegiatan) {

                $startDate = Carbon::create($kegiatan->tahun_kegiatan, 1, 1);

                $subKegiatanList = [
                    [
                        'nama'   => 'Persiapan dan Koordinasi ' . $kegiatan->nama_rk_kegiatan,
                        'target' => '3',
                        'satuan_target' => 'Dokumen ',
                        'mulai'  => $startDate,
                        'selesai'=> $startDate->copy()->addDays(14),
                    ],
                    [
                        'nama'   => 'Pelaksanaan ' . $kegiatan->nama_rk_kegiatan,
                        'target' => '40',
                        'satuan_target' => 'Kegiatan Terlaksana',
                        'mulai'  => $startDate->copy()->addDays(15),
                        'selesai'=> $startDate->copy()->addDays(60),
                    ],
                    [
                        'nama'   => 'Monitoring, Evaluasi, dan Pelaporan ' . $kegiatan->nama_rk_kegiatan,
                        'target' => '20',
                        'satuan_target' => 'Laporan Kegiatan',
                        'mulai'  => $startDate->copy()->addDays(61),
                        'selesai'=> $startDate->copy()->addDays(90),
                    ],
                ];

                foreach ($subKegiatanList as $sub) {
                    SubKegiatan::create([
                        'id_kegiatan'        => $kegiatan->id_kegiatan,
                        'nama_sub_kegiatan'  => $sub['nama'],
                        'target'             => $sub['target'],
                        'satuan_target'      => $sub['satuan_target'],
                        'tanggal_mulai'      => $sub['mulai'],
                        'tanggal_selesai'    => $sub['selesai'],
                    ]);
                }
            }
        });

        $this->command->info('🎉 Sub Kegiatan berhasil dibuat (3 per Kegiatan)');
    }
}
