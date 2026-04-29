<?php

namespace App\Services;

use App\Models\Penugasan;
use App\Models\Pengiriman;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Pegawai;
use Carbon\Carbon;

class DashboardAnalyticsService {

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
        $year  = $year  ?? now()->year;

        // Format filter bulan_pengiriman: "2026-04"
        $bulanFilter = sprintf('%04d-%02d', $year, $month);

        /**
         * Subquery: latest pengiriman per (penugasan × bulan_pengiriman)
         * Status penerimaan: 'Diterima'
         * Tipe: 'Cicilan' atau 'Pelunasan' — keduanya menghasilkan nilai
         */
        $latestDiterima = Pengiriman::query()
            ->select('pengirimans.*')
            ->joinSub(
                Pengiriman::selectRaw(
                    'id_penugasan,
                    bulan_pengiriman,
                    MAX(created_at) as latest_created'
                )->groupBy('id_penugasan', 'bulan_pengiriman'),
                'latest',
                function ($join) {
                    $join
                        ->on('pengirimans.id_penugasan',     '=', 'latest.id_penugasan')
                        ->on('pengirimans.bulan_pengiriman', '=', 'latest.bulan_pengiriman')
                        ->on('pengirimans.created_at',      '=', 'latest.latest_created');
                }
            )
            ->join('penerimaans',
                'penerimaans.id_pengiriman', '=', 'pengirimans.id_pengiriman')
            ->where('penerimaans.status', 'Diterima')
            ->whereIn('pengirimans.tipe_pengiriman', ['Cicilan', 'Pelunasan'])
            ->whereNull('pengirimans.deleted_at');

        $query = Penugasan::query()
            ->joinSub($latestDiterima, 'lp', function ($join) {
                $join->on('penugasans.id_penugasan', '=', 'lp.id_penugasan');
            })
            ->join('pegawais',
                'pegawais.id_pegawai', '=', 'penugasans.id_anggota')

            // ✅ Filter bulan_pengiriman sebagai string "YYYY-MM"
            ->where('lp.bulan_pengiriman', $bulanFilter)
            // ✅ Filter bulan/tahun dari bulan_pengiriman
            // ->whereMonth('lp.bulan_pengiriman', $month)
            // ->whereYear('lp.bulan_pengiriman', $year)
            
            ->selectRaw('
                pegawais.id_pegawai,
                pegawais.nama_pegawai,
                COUNT(DISTINCT penugasans.id_penugasan) AS total_penugasan,
                COUNT(lp.id_pengiriman) AS total_kiriman,
                COALESCE(AVG(lp.rr_kirim), 0) as rr_kirim,
                COALESCE(AVG(lp.rating_kirim), 0) as rating_kirim,
                COALESCE(AVG(lp.rating_kirim) * 20, 0) as rating_persen,
                COALESCE(AVG(
                    (1.0 - (
                        CASE 
                            WHEN DATEDIFF(lp.tanggal_pengiriman, penugasans.tanggal_mulai) <= 0 THEN 0.0
                            WHEN DATEDIFF(penugasans.tanggal_selesai, penugasans.tanggal_mulai) <= 0 THEN 0.0
                            ELSE LEAST(CAST(DATEDIFF(lp.tanggal_pengiriman, penugasans.tanggal_mulai) AS DECIMAL(10,4)) / CAST(DATEDIFF(penugasans.tanggal_selesai, penugasans.tanggal_mulai) AS DECIMAL(10,4)), 1.0)
                        END
                    )) * (CAST(lp.jumlah_dikirim AS DECIMAL(10,4)) / COALESCE(NULLIF(CAST(penugasans.target AS DECIMAL(10,4)), 0.0), 1.0)) * 100.0
                ), 0) as avg_skor_cepat,
                (COALESCE(AVG(lp.rr_kirim), 0) * 0.40) +
                (COALESCE(AVG(lp.rating_kirim) * 20, 0) * 0.35) +
                (COALESCE(AVG(
                    (1.0 - (
                        CASE 
                            WHEN DATEDIFF(lp.tanggal_pengiriman, penugasans.tanggal_mulai) <= 0 THEN 0.0
                            WHEN DATEDIFF(penugasans.tanggal_selesai, penugasans.tanggal_mulai) <= 0 THEN 0.0
                            ELSE LEAST(CAST(DATEDIFF(lp.tanggal_pengiriman, penugasans.tanggal_mulai) AS DECIMAL(10,4)) / CAST(DATEDIFF(penugasans.tanggal_selesai, penugasans.tanggal_mulai) AS DECIMAL(10,4)), 1.0)
                        END
                    )) * (CAST(lp.jumlah_dikirim AS DECIMAL(10,4)) / COALESCE(NULLIF(CAST(penugasans.target AS DECIMAL(10,4)), 0.0), 1.0)) * 100.0
                ), 0) * 0.25) as rata_rata,
                STDDEV(lp.rr_kirim) AS stddev_rr
            ')
            ->groupBy('pegawais.id_pegawai', 'pegawais.nama_pegawai')
            ->orderByDesc('rata_rata')
            ->orderByDesc('total_penugasan')
            ->orderByRaw('COALESCE(STDDEV(lp.rr_kirim), 0) ASC')
            ->orderBy('pegawais.nama_pegawai');

        $paginated = $query->paginate($perPage);

        $pegawaiIds = $paginated->pluck('id_pegawai');

        $details = Penugasan::query()
            ->joinSub($latestDiterima, 'lp', function ($join) {
                $join->on('penugasans.id_penugasan', '=', 'lp.id_penugasan');
            })
            ->join('sub_kegiatans', 'sub_kegiatans.id_sub_kegiatan', '=', 'penugasans.id_sub_kegiatan')
            ->whereIn('penugasans.id_anggota', $pegawaiIds)
            ->where('lp.bulan_pengiriman', $bulanFilter)
            ->selectRaw('
                penugasans.id_anggota,
                sub_kegiatans.nama_sub_kegiatan,
                penugasans.tanggal_mulai,
                penugasans.tanggal_selesai,
                penugasans.target,
                lp.tanggal_pengiriman,
                lp.jumlah_dikirim,
                lp.rr_kirim,
                lp.rating_kirim
            ')
            ->get()
            ->map(function ($item) {
                $start = \Carbon\Carbon::parse($item->tanggal_mulai);
                $end = \Carbon\Carbon::parse($item->tanggal_selesai);
                $delivered = \Carbon\Carbon::parse($item->tanggal_pengiriman);

                $diffKirimMulai = $start->diffInDays($delivered, false);
                $diffSelesaiMulai = $start->diffInDays($end, false);
                if ($diffSelesaiMulai == 0) $diffSelesaiMulai = 1;

                $ratio = $diffKirimMulai / $diffSelesaiMulai;
                $clamped = max(0, min($ratio, 1));
                $scoreTime = 1 - $clamped;

                $target = $item->target ?: 1;
                $scoreVol = $item->jumlah_dikirim / $target;

                $item->diffKirimMulai = $diffKirimMulai;
                $item->diffSelesaiMulai = $diffSelesaiMulai;
                $item->ratio = $ratio;
                $item->clamped_ratio = $clamped;
                $item->scoreTime = $scoreTime;
                $item->scoreVol = $scoreVol;
                $item->skor_cepat = $scoreTime * $scoreVol * 100;
                
                return $item;
            })
            ->groupBy('id_anggota');

        return $paginated->through(function ($item) use ($details) {
                $rating = round($item->rating_kirim, 1);
                $full   = floor($rating);
                $half   = ($rating - $full) >= 0.5 ? 1 : 0;
                $empty  = 5 - ($full + $half);

                $item->star_full  = $full;
                $item->star_half  = $half;
                $item->star_empty = $empty;
                $item->rata_rata  = round($item->rata_rata, 2);
                $item->details    = $details->get($item->id_pegawai, collect())->values();

                return $item;
            });
    }

    // public function rankPegawai(int $perPage = 5, $month = null, $year = null) {
    //     $month = $month ?? now()->month;
    //     $year  = $year  ?? now()->year;

    //     $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
    //     $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth();

    //     /**
    //      * Subquery:
    //      * Ambil pengiriman TERAKHIR per penugasan
    //      * yang status penerimaannya sudah 'Diterima'
    //      * Kalau latestPengiriman masih 'Revisi' → tidak masuk
    //      */
    //     $latestPengirimanDiterima = Pengiriman::query()
    //         ->select('pengirimans.*')
    //         ->joinSub(
    //             Pengiriman::selectRaw('id_penugasan, MAX(created_at) as latest_created')
    //                 ->groupBy('id_penugasan'),
    //             'latest',
    //             function ($join) {
    //                 $join->on('pengirimans.id_penugasan', '=', 'latest.id_penugasan')
    //                     ->on('pengirimans.created_at', '=', 'latest.latest_created');
    //             }
    //         )
    //         // ✅ Join ke penerimaans — hanya yang Diterima
    //         ->join('penerimaans', 'penerimaans.id_pengiriman', '=', 'pengirimans.id_pengiriman')
    //         ->where('penerimaans.status', 'Diterima')
    //         ->whereNull('pengirimans.deleted_at');

    //     $query = Penugasan::query()
    //         // ✅ Pakai irisan rentang tanggal, bukan created_at
    //         ->where('penugasans.tanggal_mulai', '<=', $endOfMonth)
    //         ->where('penugasans.tanggal_selesai', '>=', $startOfMonth)

    //         // ✅ Join hanya ke pengiriman yang sudah Diterima
    //         ->joinSub($latestPengirimanDiterima, 'latest_pengiriman', function ($join) {
    //             $join->on('penugasans.id_penugasan', '=', 'latest_pengiriman.id_penugasan');
    //         })
    //         ->join('pegawais', 'pegawais.id_pegawai', '=', 'penugasans.id_anggota')

    //         ->selectRaw('
    //             pegawais.id_pegawai,
    //             pegawais.nama_pegawai,

    //             COUNT(penugasans.id_penugasan)          as total_penugasan,
    //             AVG(latest_pengiriman.rr_kirim)         as rr_kirim,
    //             AVG(latest_pengiriman.rating_kirim)     as rating_kirim,
    //             (AVG(latest_pengiriman.rating_kirim) * 20) as rating_persen,
    //             STDDEV(latest_pengiriman.rr_kirim) as stddev_rr,
    //             (
    //                 AVG(latest_pengiriman.rr_kirim)
    //                 + (AVG(latest_pengiriman.rating_kirim) * 20)
    //             ) / 2 as rata_rata
    //         ')
    //         ->groupBy('pegawais.id_pegawai', 'pegawais.nama_pegawai')
    //         ->orderByDesc('rata_rata')
    //         ->orderByDesc('total_penugasan')   // ← tiebreaker 1
    //         ->orderByRaw('COALESCE(STDDEV(latest_pengiriman.rr_kirim), 0) ASC') // ← tiebreaker 2 (stddev rendah = konsisten)
    //         ->orderBy('pegawais.nama_pegawai'); // ← tiebreaker final (alphabetical);

    //     return $query->paginate($perPage)
    //         ->through(function ($item) {
    //             $rating = round($item->rating_kirim, 1);

    //             $full  = floor($rating);
    //             $half  = ($rating - $full) >= 0.5 ? 1 : 0;
    //             $empty = 5 - ($full + $half);

    //             $item->star_full  = $full;
    //             $item->star_half  = $half;
    //             $item->star_empty = $empty;

    //             return $item;
    //         });
    // }

    // public function rankPegawai(int $perPage = 5, $month = null, $year = null)
    // {
    //     $month = $month ?? now()->month;
    //     $year = $year ?? now()->year;

    //     /**
    //      * Subquery:
    //      * ambil pengiriman TERAKHIR per penugasan
    //      */
    //     $latestPengiriman = Pengiriman::query()
    //         ->select('pengirimans.*')
    //         ->joinSub(
    //             Pengiriman::
    //                 selectRaw('id_penugasan, MAX(created_at) as latest_created')->groupBy('id_penugasan'),
    //                 'latest', function ($join) {
    //                 $join->on('pengirimans.id_penugasan', '=', 'latest.id_penugasan')
    //                     ->on('pengirimans.created_at', '=', 'latest.latest_created');
    //             }
    //         );

    //     $query = Penugasan::query()
    //         ->whereMonth('penugasans.created_at', $month)
    //         ->whereYear('penugasans.created_at', $year)
    //         ->joinSub($latestPengiriman, 'latest_pengiriman', function ($join) {
    //             $join->on('penugasans.id_penugasan', '=', 'latest_pengiriman.id_penugasan');
    //         })
    //         ->join('pegawais', 'pegawais.id_pegawai', '=', 'penugasans.id_anggota')

    //         ->selectRaw('
    //         pegawais.id_pegawai,
    //         pegawais.nama_pegawai,

    //         AVG(latest_pengiriman.rr_kirim)      as rr_kirim,
    //         AVG(latest_pengiriman.rating_kirim) as rating_kirim,

    //         (AVG(latest_pengiriman.rating_kirim) * 20) as rating_persen,

    //         (
    //             AVG(latest_pengiriman.rr_kirim)
    //             + (AVG(latest_pengiriman.rating_kirim) * 20)
    //         ) / 2 as rata_rata
    //     ')
    //         ->groupBy('pegawais.id_pegawai', 'pegawais.nama_pegawai')
    //         ->orderByDesc('rata_rata');

    //     // Return pagination result
    //     return $query->paginate($perPage)
    //         ->through(function ($item) {
    //             $rating = round($item->rating_kirim, 1);

    //             $full  = floor($rating);
    //             $half  = ($rating - $full) >= 0.5 ? 1 : 0;
    //             $empty = 5 - ($full + $half);

    //             $item->star_full  = $full;
    //             $item->star_half  = $half;
    //             $item->star_empty = $empty;

    //             return $item;
    //         });
    // }

    
    
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
        
        $penugasanBerjalan = $totalPenugasan - $penugasanSelesai;
                        
        // Hitung persentase
        $persentaseSelesai = $totalPenugasan > 0
            ? round(($penugasanSelesai / $totalPenugasan) * 100, 1)
            : 0;


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
