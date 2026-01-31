<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kegiatan;
use App\Models\Bidang;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\RencanaJPT;

class KegiatanSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Seeding Kegiatan BPS (Versi FIX & Aman Relasi)');

        $tahun = date('Y');

        // Ambil pegawai pertama sebagai penanggung jawab default
        $pegawai = Pegawai::first();
        if (!$pegawai) {
            $this->command->error('❌ Pegawai belum tersedia');
            return;
        }

        // Role Ketua Tim
        $roleKetuaTim = Role::where('nama_role', 'Ketua Tim')->first();
        if (!$roleKetuaTim) {
            $this->command->error('❌ Role Ketua Tim belum tersedia');
            return;
        }

        // Ambil rencana JPT beserta indikatornya
        $rencanaJpts = RencanaJPT::with('indikatorjpts')->get();
        if ($rencanaJpts->isEmpty()) {
            $this->command->error('❌ Rencana JPT belum tersedia');
            return;
        }

        // Template nama kegiatan (resmi & umum ala BPS)
        $namaKegiatanTemplate = [
            'Penyusunan dan Perencanaan Kegiatan %s',
            'Pelaksanaan dan Pengumpulan Data %s',
            'Pengolahan, Analisis, dan Diseminasi Data %s',
        ];

        DB::transaction(function () use (
            $pegawai,
            $roleKetuaTim,
            $rencanaJpts,
            $namaKegiatanTemplate,
            $tahun
        ) {

            foreach (Bidang::orderBy('urutan')->get() as $bidang) {

                /**
                 * Cari rencana JPT yang PALING RELEVAN dengan bidang
                 * Jika tidak ketemu, fallback ke rencana pertama (aman)
                 */
                $rencana = $rencanaJpts->firstWhere(function ($r) use ($bidang) {
                    return str_contains(
                        strtolower($r->nama_rencana_jpt),
                        strtolower($bidang->nama_bidang)
                    );
                }) ?? $rencanaJpts->first();

                if ($rencana->indikatorjpts->isEmpty()) {
                    continue;
                }

                foreach ($namaKegiatanTemplate as $index => $template) {

                    // Ambil indikator BERDASARKAN rencana yang sama (tidak nyasar)
                    $indikator = $rencana->indikatorjpts[
                        $index % $rencana->indikatorjpts->count()
                    ];

                    Kegiatan::create([
                        'id_bidang'           => $bidang->id_bidang,
                        'id_penanggung_jawab' => $pegawai->id_pegawai,
                        'tahun_kegiatan'      => $tahun,
                        'rk_jpt'              => $rencana->id,
                        'iki_jpt'             => $indikator->id,
                        'nama_rk_kegiatan'    => sprintf($template, $bidang->nama_bidang),
                    ]);
                }

                // Pastikan penanggung jawab punya role Ketua Tim
                $pegawai->roles()->syncWithoutDetaching([$roleKetuaTim->id]);
            }
        });

        $this->command->info('🎉 Seeder Kegiatan BPS selesai & relasi VALID');
    }
}
