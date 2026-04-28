<?php

namespace App\Services;

use App\Models\Penugasan;
use App\Models\Pengiriman;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Pegawai;
use Carbon\Carbon;

class DashboardAnalyticsService
{

    public function getRekapPenugasanPegawai($bulan, $tahun) {
        $pegawai = Pegawai::withCount([
            // 1. Rekap seluruh penugasan dia bulan ini
            'penugasanSebagaiAnggota as total_penugasan' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('created_at', $bulan)
                  ->whereYear('created_at', $tahun);
            },
            
            // 2. Rekap penugasan yang sudah disubmit/dikirim (punya pengiriman)
            'penugasanSebagaiAnggota as total_dikirim' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('created_at', $bulan)
                  ->whereYear('created_at', $tahun)
                  ->has('pengirimans'); // ada datanya di tabel pengiriman
            },

            // 3. Rekap penugasan yang sudah diperiksa & terverifikasi "Diterima"
            'penugasanSebagaiAnggota as total_diterima' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('created_at', $bulan)
                  ->whereYear('created_at', $tahun)
                  ->whereHas('pengirimans.penerimaan', function ($q2) {
                      $q2->where('status', 'Diterima');
                  });
            },

            // 4. Rekap penugasan yang sudah diperiksa & terverifikasi "Diterima"
            'penugasanSebagaiAnggota as total_revisi' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('created_at', $bulan)
                  ->whereYear('created_at', $tahun)
                  ->whereHas('pengirimans.penerimaan', function ($q2) {
                      $q2->where('status', 'Revisi');
                  });
            },

            // 5. Rekap penugasan yang sudah dikirim TAPI belum diterima (Sedang Diperiksa)
            'penugasanSebagaiAnggota as total_diperiksa' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('created_at', $bulan)
                  ->whereYear('created_at', $tahun)
                  ->has('pengirimans')
                  ->whereDoesntHave('pengirimans.penerimaan', function ($q2) {
                      $q2->where('status', 'Diterima');
                  });
            }
        ])
        ->orderBy('nama_pegawai', 'asc')
        ->get();

        return $pegawai;
    }

    public function rankPegawai(int $perPage = 5, $month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        /**
         * Subquery:
         * ambil pengiriman TERAKHIR per penugasan
         */
        $latestPengiriman = Pengiriman::query()
            ->select('pengirimans.*')
            ->joinSub(
                Pengiriman::selectRaw('
                id_penugasan,
                MAX(created_at) as latest_created
            ')
                    ->groupBy('id_penugasan'),
                'latest',
                function ($join) {
                    $join->on('pengirimans.id_penugasan', '=', 'latest.id_penugasan')
                        ->on('pengirimans.created_at', '=', 'latest.latest_created');
                }
            );

        $query = Penugasan::query()
            ->whereMonth('penugasans.created_at', $month)
            ->whereYear('penugasans.created_at', $year)
            ->joinSub($latestPengiriman, 'latest_pengiriman', function ($join) {
                $join->on('penugasans.id_penugasan', '=', 'latest_pengiriman.id_penugasan');
            })
            ->join('pegawais', 'pegawais.id_pegawai', '=', 'penugasans.id_anggota')

            ->selectRaw('
            pegawais.id_pegawai,
            pegawais.nama_pegawai,

            AVG(latest_pengiriman.rr_kirim)      as rr_kirim,
            AVG(latest_pengiriman.rating_kirim) as rating_kirim,

            (AVG(latest_pengiriman.rating_kirim) * 20) as rating_persen,

            (
                AVG(latest_pengiriman.rr_kirim)
                + (AVG(latest_pengiriman.rating_kirim) * 20)
            ) / 2 as rata_rata
        ')
            ->groupBy('pegawais.id_pegawai', 'pegawais.nama_pegawai')
            ->orderByDesc('rata_rata');

        // Return pagination result
        return $query->paginate($perPage)
            ->through(function ($item) {
                $rating = round($item->rating_kirim, 1);

                $full  = floor($rating);
                $half  = ($rating - $full) >= 0.5 ? 1 : 0;
                $empty = 5 - ($full + $half);

                $item->star_full  = $full;
                $item->star_half  = $half;
                $item->star_empty = $empty;

                return $item;
            });
    }

    public function summaryPenugasanAnggota(string $idPegawai): array
    {
        $today = now()->startOfDay();

        // Ambil penugasan + latest pengiriman + penerimaan
        $penugasans = Penugasan::query()
            ->where('id_anggota', $idPegawai)
            ->with([
                'pengirimans' => function ($q) {
                    $q->latest('created_at')
                        ->limit(1)
                        ->with('penerimaan');
                }
            ])
            ->get();

        $belumMulai = 0;
        $sudahSelesai = 0;
        $sedangBerjalan = 0;

        foreach ($penugasans as $penugasan) {

            // 1️⃣ BELUM MULAI
            if (
                $penugasan->tanggal_mulai &&
                $penugasan->tanggal_mulai->startOfDay()->gt($today)
            ) {
                $belumMulai++;
                continue;
            }

            // Ambil latest pengiriman (kalau ada)
            $latestPengiriman = $penugasan->pengirimans->first();

            // Ambil penerimaan dari latest pengiriman
            $statusPenerimaan = optional(
                optional($latestPengiriman)->penerimaan
            )->status;

            // 2️⃣ SUDAH SELESAI
            if ($statusPenerimaan === 'Diterima') {
                $sudahSelesai++;
                continue;
            }

            // 3️⃣ SEDANG BERJALAN
            if (
                $penugasan->tanggal_mulai &&
                $penugasan->tanggal_mulai->startOfDay()->lte($today)
            ) {
                $sedangBerjalan++;
            }
        }

        return [
            'total'           => $penugasans->count(),
            'belum_mulai'     => $belumMulai,
            'sedang_berjalan' => $sedangBerjalan,
            'sudah_selesai'   => $sudahSelesai,
        ];
    }

    public function summaryKegiatanKetua(string $idPegawai): array
    {
        $today = now()->startOfDay();

        // Ambil penugasan + latest pengiriman + penerimaan
        $kegiatans = Kegiatan::query()
        ->where('id_penanggung_jawab', $idPegawai)
        ->get();

        $belumMulai = 0;
        $sudahSelesai = 0;
        $sedangBerjalan = 0;

        // foreach ($kegiatans as $kegiatan) {

        //     // 1️⃣ BELUM MULAI
        //     if (
        //         $kegiatan->tanggal_mulai &&
        //         $kegiatan->tanggal_mulai->startOfDay()->gt($today)
        //     ) {
        //         $belumMulai++;
        //         continue;
        //     }

        //     // Ambil latest pengiriman (kalau ada)
        //     $latestPengiriman = $penugasan->pengirimans->first();

        //     // Ambil penerimaan dari latest pengiriman
        //     $statusPenerimaan = optional(
        //         optional($latestPengiriman)->penerimaan
        //     )->status;

        //     // 2️⃣ SUDAH SELESAI
        //     if ($statusPenerimaan === 'Diterima') {
        //         $sudahSelesai++;
        //         continue;
        //     }

        //     // 3️⃣ SEDANG BERJALAN
        //     if (
        //         $penugasan->tanggal_mulai &&
        //         $penugasan->tanggal_mulai->startOfDay()->lte($today)
        //     ) {
        //         $sedangBerjalan++;
        //     }
        // }

        return [
            'total'           => $kegiatans->count(),
            // 'belum_mulai'     => $belumMulai,
            // 'sedang_berjalan' => $sedangBerjalan,
            // 'sudah_selesai'   => $sudahSelesai,
        ];
    }

    /**
     * Get statistik lengkap untuk dashboard
     */
    public function getDashboardStats($month = null, $year = null): array
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth();

        $totalKegiatan = Kegiatan::whereHas('subKegiatans')
                        ->withMin('subKegiatans', 'tanggal_mulai')
                        ->withMax('subKegiatans', 'tanggal_selesai')
                        ->having('sub_kegiatans_min_tanggal_mulai', '<=', $endOfMonth)
                        ->having('sub_kegiatans_max_tanggal_selesai', '>=', $startOfMonth)
                        ->count();

        $totalSubKegiatan = SubKegiatan::where('tanggal_mulai', '<=', $endOfMonth) 
                        ->where('tanggal_selesai', '>=', $startOfMonth)
                        ->count();

        $totalPenugasan = Penugasan::where('tanggal_mulai', '<=', $endOfMonth) 
                        ->where('tanggal_selesai', '>=', $startOfMonth)
                        ->count();
                        
        $penugasanSelesai = Penugasan::where('tanggal_mulai', '<=', $endOfMonth) 
                        ->where('tanggal_selesai', '>=', $startOfMonth)
                        ->whereHas('latestPengiriman.penerimaan', function ($query) {
                            $query->where('status', 'Diterima');
                        })->count();

        // Total Penugasan
        // $totalPenugasan = Penugasan::whereMonth('created_at', $month)
        //     ->whereYear('created_at', $year)
        //     ->count();

        // Total Penugasan Selesai (yang sudah diterima)
        // Menggunakan query manual untuk menghindari masalah relationship
        // $penugasanSelesai = Penugasan::whereMonth('created_at', $month)
        //     ->whereYear('created_at', $year)
        //     ->whereHas('pengirimans.penerimaan', function ($query) {
        //     $query->where('status', 'Diterima');
        // })->count();

        // Hitung persentase
        $persentaseSelesai = $totalPenugasan > 0
            ? round(($penugasanSelesai / $totalPenugasan) * 100, 1)
            : 0;

        // Penugasan Berjalan
        $penugasanBerjalan = $totalPenugasan - $penugasanSelesai;

        return [
            'total_kegiatan' => $totalKegiatan,
            'total_sub_kegiatan' => $totalSubKegiatan,
            'total_penugasan' => $totalPenugasan,
            'penugasan_selesai' => $penugasanSelesai,
            'persentase_selesai' => $persentaseSelesai,
            'penugasan_berjalan' => $penugasanBerjalan,
        ];
    }

    public function totalKegiatan(): int
    {
        return Penugasan::query()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function kegiatanSelesai(): int
    {
        return Penugasan::query()
            ->whereHas('pengirimans.penerimaan', function ($query) {
                $query->where('status', 'Diterima')
                    ->whereMonth('penerimaans.created_at', now()->month)
                    ->whereYear('penerimaans.created_at', now()->year);
            })
            ->orWhereHas('pengirimans', function ($query) {
                // Jika ada pengiriman terakhir dengan penerimaan diterima
                $query->whereHas('penerimaan', function ($subQuery) {
                    $subQuery->where('status', 'Diterima');
                });
            })
            ->count();
    }
}
