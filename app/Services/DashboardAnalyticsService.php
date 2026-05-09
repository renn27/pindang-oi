<?php

namespace App\Services;

use App\Models\Penugasan;
use App\Models\Pengiriman;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardAnalyticsService {

    public function getRekapPenugasanPegawai($bulan, $tahun) {
        $pegawai = Pegawai::withCount([
            // 1. Jumlah penugasan (COUNT baris penugasan)
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

            // 4. Rekap penugasan yang sudah diperiksa & terverifikasi "Revisi"
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
        // SUM kolom target → total akumulasi target penugasan (bukan jumlah baris)
        ->withSum(['penugasanSebagaiAnggota as total_target' => function ($q) use ($bulan, $tahun) {
            $q->whereMonth('created_at', $bulan)
              ->whereYear('created_at', $tahun);
        }], 'target')
        ->orderBy('nama_pegawai', 'asc')
        ->get();

        return $pegawai;
    }

    // =========================================================================
    // RANKING ENGINE — F1 Penyelesaian · F2 Kecepatan · F3 RR · F4 Rating
    // =========================================================================

    public function rankPegawaiAll($month = null, $year = null): Collection
    {
        [$month, $year, $bf, $gs] = $this->rankInit($month, $year);
        $allData = $this->buildRankBaseQuery($bf, $year, $month, $gs)->get();
        $details = $this->buildDetailsQuery($bf, $year, $month, $allData->pluck('id_pegawai'));
        return $allData->map(fn($item) => $this->decorateRankItem($item, $details, $gs));
    }

    public function rankPegawai(int $perPage = 5, $month = null, $year = null)
    {
        [$month, $year, $bf, $gs] = $this->rankInit($month, $year);
        $paginated = $this->buildRankBaseQuery($bf, $year, $month, $gs)->paginate($perPage);
        $details   = $this->buildDetailsQuery($bf, $year, $month, $paginated->pluck('id_pegawai'));
        return $paginated->through(fn($item) => $this->decorateRankItem($item, $details, $gs));
    }

    private function rankInit($month, $year): array
    {
        $month = $month ?? now()->month;
        $year  = $year  ?? now()->year;
        $bf    = sprintf('%04d-%02d', $year, $month);
        return [$month, $year, $bf, $this->getGlobalStats($bf, $year, $month)];
    }

    private function getGlobalStats(string $bf, int $year, int $month): array
    {
        $am = $year * 12 + $month;
        $r  = \DB::table('penugasans')
            ->whereNull('deleted_at')
            ->whereRaw('(YEAR(tanggal_mulai)*12+MONTH(tanggal_mulai)) <= ?', [$am])
            ->whereRaw('(YEAR(tanggal_selesai)*12+MONTH(tanggal_selesai)) >= ?', [$am])
            ->selectRaw('
                COUNT(DISTINCT id_penugasan) as tot,
                COALESCE(SUM(target), 0) as sumT,
                COUNT(DISTINCT id_anggota) as tot_pegawai
            ')
            ->first();

        $tot        = (int)($r->tot         ?? 0);
        $sum        = (float)($r->sumT      ?? 0);
        $totPegawai = (int)($r->tot_pegawai ?? 0);

        // avg = total target seluruh tim / jumlah pegawai aktif
        // sehingga koefisien = target_pegawai / avg = perbandingan apple-to-apple
        $avg = $totPegawai > 0 ? max(1.0, $sum / $totPegawai) : 1.0;

        return [
            'total_penugasan_semua' => $tot,
            'sum_target_semua'      => $sum,
            'avg_target_bulan'      => $avg,
        ];
    }

    private function buildLatestDiterimaSubquery(string $bf)
    {
        return Pengiriman::query()
            ->select('pengirimans.*', 'penerimaans.status as status_penerimaan')
            ->joinSub(
                Pengiriman::selectRaw('id_penugasan, bulan_pengiriman, MAX(created_at) as lc')
                    ->whereNull('deleted_at')->groupBy('id_penugasan', 'bulan_pengiriman'),
                'lat',
                fn($j) => $j->on('pengirimans.id_penugasan',     '=', 'lat.id_penugasan')
                             ->on('pengirimans.bulan_pengiriman', '=', 'lat.bulan_pengiriman')
                             ->on('pengirimans.created_at',       '=', 'lat.lc')
            )
            ->join('penerimaans', 'penerimaans.id_pengiriman', '=', 'pengirimans.id_pengiriman')
            ->where('penerimaans.status', 'Diterima')
            ->whereIn('pengirimans.tipe_pengiriman', ['Cicilan', 'Pelunasan'])
            ->where('pengirimans.bulan_pengiriman', $bf)
            ->whereNull('pengirimans.deleted_at');
    }

    private function buildRankBaseQuery(string $bf, int $year, int $month, array $gs)
    {
        $am  = $year * 12 + $month;
        $c   = (float) $gs['sum_target_semua'];
        $avg = (float) $gs['avg_target_bulan'];
        $ld  = $this->buildLatestDiterimaSubquery($bf);

        $inner = \DB::table('penugasans')
            ->join('pegawais', 'pegawais.id_pegawai', '=', 'penugasans.id_anggota')
            ->leftJoinSub($ld, 'lp', fn($j) => $j->on('penugasans.id_penugasan', '=', 'lp.id_penugasan'))
            ->whereNull('penugasans.deleted_at')
            ->whereRaw('(YEAR(penugasans.tanggal_mulai)*12+MONTH(penugasans.tanggal_mulai)) <= ?', [$am])
            ->whereRaw('(YEAR(penugasans.tanggal_selesai)*12+MONTH(penugasans.tanggal_selesai)) >= ?', [$am])
            ->groupBy('pegawais.id_pegawai', 'pegawais.nama_pegawai', 'pegawais.photo')
            ->selectRaw("
                pegawais.id_pegawai, pegawais.nama_pegawai, pegawais.photo,
                COUNT(DISTINCT penugasans.id_penugasan) AS total_penugasan,
                COUNT(DISTINCT CASE WHEN lp.tipe_pengiriman='Pelunasan' THEN penugasans.id_penugasan END) AS total_selesai,
                COUNT(DISTINCT CASE WHEN lp.tipe_pengiriman='Cicilan'   THEN penugasans.id_penugasan END) AS total_cicilan_diterima,
                COALESCE(SUM(penugasans.target),0) AS target_pegawai,
                COALESCE(SUM(CASE WHEN lp.tipe_pengiriman='Pelunasan' THEN lp.jumlah_dikirim ELSE 0 END),0) AS progress_pelunasan,
                COALESCE(SUM(CASE WHEN lp.tipe_pengiriman='Cicilan'   THEN lp.jumlah_dikirim ELSE 0 END),0) AS progress_cicilan,
                COALESCE(AVG(CASE WHEN lp.tipe_pengiriman IS NOT NULL  THEN lp.rating_kirim  END),0) AS rating_kirim_avg,
                CASE WHEN COALESCE(SUM(penugasans.target),0)=0 OR ?=0 THEN 0.0
                ELSE GREATEST(0.0, ? - COALESCE(SUM(penugasans.target),0) + (
                        COALESCE(SUM(CASE WHEN lp.tipe_pengiriman='Pelunasan' THEN lp.jumlah_dikirim ELSE 0 END),0)
                        +COALESCE(SUM(CASE WHEN lp.tipe_pengiriman='Cicilan' THEN lp.jumlah_dikirim ELSE 0 END),0)*0.5
                    )) / NULLIF(?,0)
                    *(COALESCE(SUM(CASE WHEN lp.tipe_pengiriman='Pelunasan' THEN lp.jumlah_dikirim ELSE 0 END),0)
                      +COALESCE(SUM(CASE WHEN lp.tipe_pengiriman='Cicilan' THEN lp.jumlah_dikirim ELSE 0 END),0)*0.5)
                    / NULLIF(COALESCE(SUM(penugasans.target),0),0) * 100.0
                END AS f1_penyelesaian,
                COALESCE(AVG(CASE WHEN lp.tipe_pengiriman='Pelunasan' THEN
                    CASE WHEN GREATEST(0,DATEDIFF(lp.tanggal_pengiriman,penugasans.tanggal_mulai))
                              <=GREATEST(1,DATEDIFF(penugasans.tanggal_selesai,penugasans.tanggal_mulai))
                    THEN 80.0+CAST(GREATEST(1,DATEDIFF(penugasans.tanggal_selesai,penugasans.tanggal_mulai))
                                 -GREATEST(0,DATEDIFF(lp.tanggal_pengiriman,penugasans.tanggal_mulai)) AS DECIMAL(10,4))
                             /GREATEST(1,DATEDIFF(penugasans.tanggal_selesai,penugasans.tanggal_mulai))*20.0
                    ELSE GREATEST(70.0,80.0-LEAST(10.0,
                           CAST(GREATEST(0,DATEDIFF(lp.tanggal_pengiriman,penugasans.tanggal_mulai))
                              -GREATEST(1,DATEDIFF(penugasans.tanggal_selesai,penugasans.tanggal_mulai)) AS DECIMAL(10,4))
                           /GREATEST(1,DATEDIFF(penugasans.tanggal_selesai,penugasans.tanggal_mulai))*10.0))
                    END
                ELSE NULL END),0) AS f2_kecepatan,
                CASE WHEN COUNT(DISTINCT penugasans.id_penugasan)=0 THEN 0.0
                ELSE COALESCE(SUM(CASE
                    WHEN lp.tipe_pengiriman='Pelunasan' THEN lp.rr_kirim*1.0
                    WHEN lp.tipe_pengiriman='Cicilan'   THEN lp.rr_kirim
                        *CASE WHEN penugasans.target=0 THEN 0.0
                          ELSE CAST(lp.jumlah_dikirim AS DECIMAL(10,4))/CAST(penugasans.target AS DECIMAL(10,4)) END
                    ELSE 0.0 END),0)/COUNT(DISTINCT penugasans.id_penugasan) END AS f3_rr_kirim,
                CASE WHEN COUNT(DISTINCT penugasans.id_penugasan)=0 THEN 0.0
                ELSE COALESCE(SUM(CASE
                    WHEN lp.tipe_pengiriman='Pelunasan' THEN lp.rating_kirim*20.0*1.0
                    WHEN lp.tipe_pengiriman='Cicilan'   THEN lp.rating_kirim*20.0
                        *CASE WHEN penugasans.target=0 THEN 0.0
                          ELSE CAST(lp.jumlah_dikirim AS DECIMAL(10,4))/CAST(penugasans.target AS DECIMAL(10,4)) END
                    ELSE 0.0 END),0)/COUNT(DISTINCT penugasans.id_penugasan) END AS f4_rating_kirim,
                CASE WHEN ?=0 THEN 1.0
                ELSE LEAST(1.15,GREATEST(0.85,COALESCE(SUM(penugasans.target),0)/?))
                END AS koefisien_beban
            ", [$c, $c, $c, $avg, $avg]);

        return \DB::query()->fromSub($inner, 'ranked')
            ->selectRaw('ranked.*,
                (f1_penyelesaian+f2_kecepatan+f3_rr_kirim+f4_rating_kirim)/4.0 AS rata_rata_base,
                CASE WHEN koefisien_beban >= 1.0
                    THEN (f1_penyelesaian+f2_kecepatan+f3_rr_kirim+f4_rating_kirim)/4.0
                         + LEAST(
                             (f1_penyelesaian+f2_kecepatan+f3_rr_kirim+f4_rating_kirim)/4.0 * (koefisien_beban - 1.0),
                             GREATEST(0.0, 100.0 - (f1_penyelesaian+f2_kecepatan+f3_rr_kirim+f4_rating_kirim)/4.0)
                           )
                    ELSE GREATEST(0.0, (f1_penyelesaian+f2_kecepatan+f3_rr_kirim+f4_rating_kirim)/4.0 * koefisien_beban)
                END AS rata_rata_final')
            ->orderByDesc('rata_rata_final')->orderByDesc('total_selesai')->orderBy('nama_pegawai');

    }

    private function buildDetailsQuery(string $bf, int $year, int $month, $ids)
    {
        $am = $year * 12 + $month;
        $ld = $this->buildLatestDiterimaSubquery($bf);
        return \DB::table('penugasans')
            ->join('pegawais',      'pegawais.id_pegawai',           '=', 'penugasans.id_anggota')
            ->join('sub_kegiatans', 'sub_kegiatans.id_sub_kegiatan', '=', 'penugasans.id_sub_kegiatan')
            ->leftJoinSub($ld, 'lp', fn($j) => $j->on('penugasans.id_penugasan', '=', 'lp.id_penugasan'))
            ->whereNull('penugasans.deleted_at')
            ->whereRaw('(YEAR(penugasans.tanggal_mulai)*12+MONTH(penugasans.tanggal_mulai)) <= ?', [$am])
            ->whereRaw('(YEAR(penugasans.tanggal_selesai)*12+MONTH(penugasans.tanggal_selesai)) >= ?', [$am])
            ->whereIn('penugasans.id_anggota', $ids)
            ->selectRaw('penugasans.id_anggota, sub_kegiatans.nama_sub_kegiatan,
                penugasans.tanggal_mulai, penugasans.tanggal_selesai, penugasans.target,
                lp.bulan_pengiriman, lp.tanggal_pengiriman, lp.jumlah_dikirim,
                lp.rr_kirim, lp.rating_kirim, lp.tipe_pengiriman, lp.status_penerimaan')
            ->get()
            ->map(function ($row) {
                $isPelunasan = $row->tipe_pengiriman === 'Pelunasan';
                $isCicilan   = $row->tipe_pengiriman === 'Cicilan';
                $target      = max(0, (int)($row->target ?? 0));
                $jml         = max(0, (int)($row->jumlah_dikirim ?? 0));
                $bobot       = $isPelunasan ? 1.0 : ($isCicilan && $target > 0 ? $jml / $target : 0.0);
                $row->is_pelunasan      = $isPelunasan;
                $row->bobot_parsial     = $bobot;
                $row->kontribusi_rr     = round((float)($row->rr_kirim     ?? 0) * $bobot, 4);
                $row->kontribusi_rating = round((float)($row->rating_kirim ?? 0) * 20 * $bobot, 4);
                $row->skor_f2 = $row->lama_rentang = $row->lama_pengiriman = null;
                $row->terlambat = false;
                if ($isPelunasan && $row->tanggal_pengiriman) {
                    $lr = max(1, Carbon::parse($row->tanggal_mulai)->diffInDays(Carbon::parse($row->tanggal_selesai)));
                    $lp = max(0, Carbon::parse($row->tanggal_mulai)->diffInDays(Carbon::parse($row->tanggal_pengiriman), false));
                    $row->lama_rentang = $lr; $row->lama_pengiriman = $lp; $row->terlambat = $lp > $lr;
                    $row->skor_f2 = $lp <= $lr
                        ? 80.0 + (($lr-$lp)/$lr)*20.0
                        : max(70.0, 80.0 - min(10.0, (($lp-$lr)/$lr)*10.0));
                }
                return $row;
            })->groupBy('id_anggota');
    }

    private function decorateRankItem($item, $details, array $gs)
    {
        $rAvg = round((float)($item->rating_kirim_avg ?? 0), 1);
        $full = (int) floor($rAvg);
        $half = ($rAvg - $full) >= 0.5 ? 1 : 0;
        $item->star_full      = $full;
        $item->star_half      = $half;
        $item->star_empty     = 5 - $full - $half;
        $item->rr_kirim       = round((float)($item->f3_rr_kirim    ?? 0), 2);
        $item->rating_kirim   = $rAvg;
        $item->rating_persen  = round((float)($item->f4_rating_kirim ?? 0), 2);
        $item->avg_skor_cepat = round((float)($item->f2_kecepatan    ?? 0), 2);
        $item->rata_rata      = round((float)($item->rata_rata_final  ?? 0), 2);
        $det = $details->get($item->id_pegawai, collect())->values();
        $item->details = $det;
        $a=$c=$pp=$pc=0.0;
        $a  = (float)($item->target_pegawai    ?? 0);
        $c  = (float) $gs['sum_target_semua'];
        $pp = (float)($item->progress_pelunasan ?? 0);
        $pc = (float)($item->progress_cicilan   ?? 0);
        $be = $pp + $pc * 0.5;
        $d  = max(0.0, $c - ($a - $be));
        $item->breakdown_formula = [
            'f1' => ['nilai'=>round((float)($item->f1_penyelesaian??0),2),
                'progress_pelunasan'=>$pp,'progress_cicilan'=>$pc,
                'b_efektif'=>round($be,2),'a'=>$a,'c'=>$c,'d'=>round($d,2)],
            'f2' => ['nilai'=>round((float)($item->f2_kecepatan??0),2),
                'detail'=>$det->filter(fn($r)=>$r->is_pelunasan&&$r->skor_f2!==null)
                    ->map(fn($r)=>['nama_sub_kegiatan'=>$r->nama_sub_kegiatan,
                        'tanggal_mulai'=>$r->tanggal_mulai,'tanggal_selesai'=>$r->tanggal_selesai,
                        'tanggal_pengiriman'=>$r->tanggal_pengiriman,
                        'lama_rentang'=>$r->lama_rentang,'lama_pengiriman'=>$r->lama_pengiriman,
                        'terlambat'=>$r->terlambat,'score_f2'=>round($r->skor_f2,2)])->values()],
            'f3' => ['nilai'=>round((float)($item->f3_rr_kirim??0),2),
                'detail'=>$det->map(fn($r)=>['nama_sub_kegiatan'=>$r->nama_sub_kegiatan,
                    'rr_kirim'=>$r->rr_kirim,'bobot_parsial'=>round($r->bobot_parsial,4),
                    'kontribusi_rr'=>round($r->kontribusi_rr,4),'tipe_pengiriman'=>$r->tipe_pengiriman])->values()],
            'f4' => ['nilai'=>round((float)($item->f4_rating_kirim??0),2),
                'detail'=>$det->map(fn($r)=>['nama_sub_kegiatan'=>$r->nama_sub_kegiatan,
                    'rating_kirim'=>$r->rating_kirim,'bobot_parsial'=>round($r->bobot_parsial,4),
                    'kontribusi_rating'=>round($r->kontribusi_rating,4),'tipe_pengiriman'=>$r->tipe_pengiriman])->values()],
            'koefisien_beban'    =>round((float)($item->koefisien_beban??1.0),4),
            'target_pegawai'     =>$a,
            'avg_target_bulan'   =>round($gs['avg_target_bulan'],2),
            'total_penugasan_dia'=>(int)($item->total_penugasan??0),
            'rata_rata_base'     =>round((float)($item->rata_rata_base??0),2),
            'rata_rata_final'    =>round((float)($item->rata_rata_final??0),2),
            'bonus_aktual'       =>(function() use ($item) {
                $base = (float)($item->rata_rata_base ?? 0);
                $koef = (float)($item->koefisien_beban ?? 1.0);
                if ($koef >= 1.0) {
                    $bonusMax   = $base * ($koef - 1.0);
                    $bonusRuang = max(0.0, 100.0 - $base);
                    return round(min($bonusMax, $bonusRuang), 2);
                }
                return round($base * $koef - $base, 2); // negatif = penalty
            })(),
            'ruang_ke_100'       =>round(max(0.0, 100.0 - (float)($item->rata_rata_base??0)), 2),
        ];
        return $item;
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
