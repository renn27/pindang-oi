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

    public function getRekapPenugasanPegawai($bulan, $tahun, bool $excludeSpecial = false) {
        $startOfMonth = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($tahun, $bulan, 1)->endOfMonth();
        $som = $startOfMonth->toDateString();
        $eom = $endOfMonth->toDateString();
        $bf  = sprintf('%04d-%02d', $tahun, $bulan);

        $excludeCompletedBefore = function ($q) use ($bf) {
            $q->whereDoesntHave('pengirimans', function ($qp) use ($bf) {
                $qp->where('tipe_pengiriman', 'Pelunasan')
                   ->where('bulan_pengiriman', '<', $bf)
                   ->whereHas('penerimaan', function ($qrec) {
                       $qrec->where('status', 'Diterima');
                   });
            });
        };

        $query = Pegawai::activeInMonth((int) $bulan, (int) $tahun);

        if ($excludeSpecial) {
            $query->where(function ($q) {
                $q->where('nama_pegawai', '!=', 'Sukendro Suryo Wiguno, SST, M.Ec.Dev')
                  ->orWhere('jabatan', '!=', 'Kepala BPS Ogan Ilir')
                  ->orWhere('nip_bps', '!=', '340017814');
            });
        }

        $pegawai = $query
        ->withCount([
            // 1. Jumlah penugasan (COUNT baris penugasan)
            'penugasanSebagaiAnggota as total_penugasan' => function ($q) use ($som, $eom, $excludeCompletedBefore) {
                $q->where('tanggal_mulai', '<=', $eom)
                  ->where('tanggal_selesai', '>=', $som);
                $excludeCompletedBefore($q);
            },

            // 2. Rekap penugasan yang sudah disubmit/dikirim (punya pengiriman)
            'penugasanSebagaiAnggota as total_dikirim' => function ($q) use ($som, $eom, $excludeCompletedBefore) {
                $q->where('tanggal_mulai', '<=', $eom)
                  ->where('tanggal_selesai', '>=', $som)
                  ->has('pengirimans'); // ada datanya di tabel pengiriman
                $excludeCompletedBefore($q);
            },

            // 2b. Rekap penugasan yang belum dikerjakan (tidak punya pengiriman)
            'penugasanSebagaiAnggota as total_belum_dikerjakan' => function ($q) use ($som, $eom, $excludeCompletedBefore) {
                $q->where('tanggal_mulai', '<=', $eom)
                  ->where('tanggal_selesai', '>=', $som)
                  ->doesntHave('pengirimans');
                $excludeCompletedBefore($q);
            },

            // 3. Rekap penugasan yang sudah diperiksa & terverifikasi "Diterima"
            'penugasanSebagaiAnggota as total_diterima' => function ($q) use ($som, $eom, $excludeCompletedBefore) {
                $q->where('tanggal_mulai', '<=', $eom)
                  ->where('tanggal_selesai', '>=', $som)
                  ->whereHas('pengirimans.penerimaan', function ($q2) {
                      $q2->where('status', 'Diterima');
                  });
                $excludeCompletedBefore($q);
            },

            // 4. Rekap penugasan yang sudah diperiksa & terverifikasi "Revisi"
            'penugasanSebagaiAnggota as total_revisi' => function ($q) use ($som, $eom, $excludeCompletedBefore) {
                $q->where('tanggal_mulai', '<=', $eom)
                  ->where('tanggal_selesai', '>=', $som)
                  ->whereHas('pengirimans.penerimaan', function ($q2) {
                      $q2->where('status', 'Revisi');
                  });
                $excludeCompletedBefore($q);
            },

            // 5. Rekap penugasan yang sudah dikirim TAPI belum diterima (Sedang Diperiksa)
            'penugasanSebagaiAnggota as total_diperiksa' => function ($q) use ($som, $eom, $excludeCompletedBefore) {
                $q->where('tanggal_mulai', '<=', $eom)
                  ->where('tanggal_selesai', '>=', $som)
                  ->has('pengirimans')
                  ->whereDoesntHave('pengirimans.penerimaan', function ($q2) {
                      $q2->where('status', 'Diterima');
                  });
                $excludeCompletedBefore($q);
            }
        ])
        // SUM kolom target → total akumulasi target penugasan (bukan jumlah baris)
        ->withSum(['penugasanSebagaiAnggota as total_target' => function ($q) use ($som, $eom, $excludeCompletedBefore) {
            $q->where('tanggal_mulai', '<=', $eom)
              ->where('tanggal_selesai', '>=', $som);
            $excludeCompletedBefore($q);
        }], 'target')
        ->orderBy('nama_pegawai', 'asc')
        ->get();

        return $pegawai;
    }

    // =========================================================================
    // RANKING ENGINE — F1 Penyelesaian · F2 Kecepatan · F3 RR · F4 Rating
    // =========================================================================

    public function rankPegawaiAll($month = null, $year = null, bool $excludeSpecial = false): Collection
    {
        [$month, $year, $bf, $gs, $startOfMonth, $endOfMonth] = $this->rankInit($month, $year, $excludeSpecial);
        $allData = $this->buildRankBaseQuery($bf, $gs, $startOfMonth, $endOfMonth, $excludeSpecial)->get();
        $details = $this->buildDetailsQuery($bf, $allData->pluck('id_pegawai'), $startOfMonth, $endOfMonth);
        return $allData->map(fn($item) => $this->decorateRankItem($item, $details, $gs));
    }

    public function rankPegawai(int $perPage = 5, $month = null, $year = null, bool $excludeSpecial = true)
    {
        [$month, $year, $bf, $gs, $startOfMonth, $endOfMonth] = $this->rankInit($month, $year, $excludeSpecial);
        $paginated = $this->buildRankBaseQuery($bf, $gs, $startOfMonth, $endOfMonth, $excludeSpecial)->paginate($perPage);
        $details   = $this->buildDetailsQuery($bf, $paginated->pluck('id_pegawai'), $startOfMonth, $endOfMonth);
        return $paginated->through(fn($item) => $this->decorateRankItem($item, $details, $gs));
    }

    private function rankInit($month, $year, bool $excludeSpecial = false): array
    {
        $month        = $month ?? now()->month;
        $year         = $year  ?? now()->year;
        $bf           = sprintf('%04d-%02d', $year, $month);
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth();
        return [$month, $year, $bf, $this->getGlobalStats($bf, $startOfMonth, $endOfMonth, $excludeSpecial), $startOfMonth, $endOfMonth];
    }

    private function getGlobalStats(string $bf, Carbon $startOfMonth, Carbon $endOfMonth, bool $excludeSpecial = false): array
    {
        $inactiveCutoff = $startOfMonth->toDateString();

        // Filter penugasan yang aktif/berlangsung di bulan target menggunakan range tanggal
        // (lebih efisien dari YEAR()*12+MONTH() karena index kolom dapat dipakai)
        $query  = \DB::table('penugasans')
            ->join('pegawais', 'pegawais.id_pegawai', '=', 'penugasans.id_anggota')
            ->whereNull('penugasans.deleted_at')
            ->where(function ($query) use ($inactiveCutoff) {
                $query->whereNull('pegawais.inactive_from_month')
                    ->orWhere('pegawais.inactive_from_month', '>', $inactiveCutoff);
            });

        if ($excludeSpecial) {
            $query->where(function ($q) {
                $q->where('pegawais.nama_pegawai', '!=', 'Sukendro Suryo Wiguno, SST, M.Ec.Dev')
                      ->orWhere('pegawais.jabatan', '!=', 'Kepala BPS Ogan Ilir')
                      ->orWhere('pegawais.nip_bps', '!=', '340017814');
            });
        }

        $r = $query->where('tanggal_mulai', '<=', $endOfMonth->toDateString())
            ->where('tanggal_selesai', '>=', $startOfMonth->toDateString())
            ->whereNotExists(function ($query) use ($bf) {
                $query->select(\DB::raw(1))
                      ->from('pengirimans')
                      ->join('penerimaans', 'penerimaans.id_pengiriman', '=', 'pengirimans.id_pengiriman')
                      ->whereColumn('pengirimans.id_penugasan', 'penugasans.id_penugasan')
                      ->where('pengirimans.tipe_pengiriman', 'Pelunasan')
                      ->where('pengirimans.bulan_pengiriman', '<', $bf)
                      ->where('penerimaans.status', 'Diterima')
                      ->whereNull('pengirimans.deleted_at');
            })
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

    private function buildLatestDiterimaAllMonthsSubquery()
    {
        return Pengiriman::query()
            ->select('pengirimans.*', 'penerimaans.status as status_penerimaan')
            ->joinSub(
                Pengiriman::selectRaw('id_penugasan, MAX(created_at) as lc')
                    ->whereNull('deleted_at')->groupBy('id_penugasan'),
                'lat',
                fn($j) => $j->on('pengirimans.id_penugasan', '=', 'lat.id_penugasan')
                             ->on('pengirimans.created_at',    '=', 'lat.lc')
            )
            ->join('penerimaans', 'penerimaans.id_pengiriman', '=', 'pengirimans.id_pengiriman')
            ->where('penerimaans.status', 'Diterima')
            ->whereIn('pengirimans.tipe_pengiriman', ['Cicilan', 'Pelunasan'])
            ->whereNull('pengirimans.deleted_at');
    }

    private function buildRankBaseQuery(string $bf, array $gs, Carbon $startOfMonth, Carbon $endOfMonth, bool $excludeSpecial = false)
    {
        $c   = (float) $gs['sum_target_semua'];
        $avg = (float) $gs['avg_target_bulan'];
        $ld  = $this->buildLatestDiterimaSubquery($bf);
        $som = $startOfMonth->toDateString();
        $eom = $endOfMonth->toDateString();

        $innerQuery = \DB::table('pegawais')
            ->where(function ($query) use ($som) {
                $query->whereNull('pegawais.inactive_from_month')
                    ->orWhere('pegawais.inactive_from_month', '>', $som);
            });

        if ($excludeSpecial) {
            $innerQuery->where(function ($query) {
                $query->where('pegawais.nama_pegawai', '!=', 'Sukendro Suryo Wiguno, SST, M.Ec.Dev')
                      ->orWhere('pegawais.jabatan', '!=', 'Kepala BPS Ogan Ilir')
                      ->orWhere('pegawais.nip_bps', '!=', '340017814');
            });
        }

        $inner = $innerQuery
            ->leftJoin('penugasans', function($join) use ($som, $eom, $bf) {
                $join->on('pegawais.id_pegawai', '=', 'penugasans.id_anggota')
                     ->whereNull('penugasans.deleted_at')
                     ->where('penugasans.tanggal_mulai', '<=', $eom)
                     ->where('penugasans.tanggal_selesai', '>=', $som)
                     ->whereNotExists(function ($query) use ($bf) {
                          $query->select(\DB::raw(1))
                               ->from('pengirimans')
                               ->join('penerimaans', 'penerimaans.id_pengiriman', '=', 'pengirimans.id_pengiriman')
                               ->whereColumn('pengirimans.id_penugasan', 'penugasans.id_penugasan')
                               ->where('pengirimans.tipe_pengiriman', 'Pelunasan')
                               ->where('pengirimans.bulan_pengiriman', '<', $bf)
                               ->where('penerimaans.status', 'Diterima')
                               ->whereNull('pengirimans.deleted_at');
                     });
            })
            ->leftJoinSub($ld, 'lp', fn($j) => $j->on('penugasans.id_penugasan', '=', 'lp.id_penugasan'))
            ->groupBy('pegawais.id_pegawai', 'pegawais.nama_pegawai', 'pegawais.photo', 'pegawais.nip_bps', 'pegawais.jabatan')
            ->selectRaw("
                pegawais.id_pegawai, pegawais.nama_pegawai, pegawais.photo, pegawais.nip_bps, pegawais.jabatan,
                COUNT(DISTINCT penugasans.id_penugasan) AS total_penugasan,
                COUNT(DISTINCT lp.id_penugasan) AS total_penugasan_dikerjakan,
                COUNT(DISTINCT CASE WHEN lp.tipe_pengiriman='Pelunasan' THEN penugasans.id_penugasan END) AS total_selesai,
                COUNT(DISTINCT CASE WHEN lp.tipe_pengiriman='Cicilan'   THEN penugasans.id_penugasan END) AS total_cicilan_diterima,
                COALESCE(SUM(penugasans.target),0) AS target_pegawai,
                COALESCE(SUM(CASE WHEN lp.tipe_pengiriman='Pelunasan' THEN lp.jumlah_dikirim ELSE 0 END),0) AS progress_pelunasan,
                COALESCE(SUM(CASE WHEN lp.tipe_pengiriman='Cicilan'   THEN lp.jumlah_dikirim ELSE 0 END),0) AS progress_cicilan,
                COALESCE(SUM(CASE
                    WHEN lp.tipe_pengiriman='Cicilan' AND penugasans.target>0
                        THEN CAST(lp.jumlah_dikirim AS DECIMAL(10,4))
                            *CAST(lp.jumlah_dikirim AS DECIMAL(10,4))
                            /CAST(penugasans.target AS DECIMAL(10,4))
                    ELSE 0 END),0) AS b_efektif_cicilan,
                COALESCE(AVG(CASE WHEN lp.tipe_pengiriman IS NOT NULL  THEN lp.rating_kirim  END),0) AS rating_kirim_avg,
                CASE WHEN COALESCE(SUM(penugasans.target),0)=0 OR ?=0 THEN 0.0
                ELSE GREATEST(0.0, ? - COALESCE(SUM(penugasans.target),0) + (
                        COALESCE(SUM(CASE WHEN lp.tipe_pengiriman='Pelunasan' THEN lp.jumlah_dikirim ELSE 0 END),0)
                        +COALESCE(SUM(CASE
                            WHEN lp.tipe_pengiriman='Cicilan' AND penugasans.target>0
                                THEN CAST(lp.jumlah_dikirim AS DECIMAL(10,4))
                                    *CAST(lp.jumlah_dikirim AS DECIMAL(10,4))
                                    /CAST(penugasans.target AS DECIMAL(10,4))
                            ELSE 0 END),0)
                    )) / NULLIF(?,0)
                    *(COALESCE(SUM(CASE WHEN lp.tipe_pengiriman='Pelunasan' THEN lp.jumlah_dikirim ELSE 0 END),0)
                      +COALESCE(SUM(CASE
                            WHEN lp.tipe_pengiriman='Cicilan' AND penugasans.target>0
                                THEN CAST(lp.jumlah_dikirim AS DECIMAL(10,4))
                                    *CAST(lp.jumlah_dikirim AS DECIMAL(10,4))
                                    /CAST(penugasans.target AS DECIMAL(10,4))
                            ELSE 0 END),0))
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
                CASE WHEN ?=0 OR COUNT(DISTINCT penugasans.id_penugasan)=0 THEN 1.0
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
            // Prioritas: pegawai TANPA penugasan aktif selalu di bawah pegawai yang punya penugasan
            ->orderByRaw('CASE WHEN total_penugasan = 0 THEN 1 ELSE 0 END')
            ->orderByDesc('rata_rata_final')
            ->orderByDesc('total_selesai')
            ->orderBy('nama_pegawai');

    }

    private function buildDetailsQuery(string $bf, $ids, Carbon $startOfMonth, Carbon $endOfMonth)
    {
        $ld = $this->buildLatestDiterimaAllMonthsSubquery();
        return \DB::table('penugasans')
            ->join('pegawais',      'pegawais.id_pegawai',           '=', 'penugasans.id_anggota')
            ->join('sub_kegiatans', 'sub_kegiatans.id_sub_kegiatan', '=', 'penugasans.id_sub_kegiatan')
            ->leftJoinSub($ld, 'lp', fn($j) => $j->on('penugasans.id_penugasan', '=', 'lp.id_penugasan'))
            ->whereNull('penugasans.deleted_at')
            ->where('penugasans.tanggal_mulai', '<=', $endOfMonth->toDateString())
            ->where('penugasans.tanggal_selesai', '>=', $startOfMonth->toDateString())
            ->whereNotExists(function ($query) use ($bf) {
                $query->select(\DB::raw(1))
                      ->from('pengirimans')
                      ->join('penerimaans', 'penerimaans.id_pengiriman', '=', 'pengirimans.id_pengiriman')
                      ->whereColumn('pengirimans.id_penugasan', 'penugasans.id_penugasan')
                      ->where('pengirimans.tipe_pengiriman', 'Pelunasan')
                      ->where('pengirimans.bulan_pengiriman', '<', $bf)
                      ->where('penerimaans.status', 'Diterima')
                      ->whereNull('pengirimans.deleted_at');
            })
            ->whereIn('penugasans.id_anggota', $ids)
            ->selectRaw('penugasans.id_anggota, sub_kegiatans.nama_sub_kegiatan,
                penugasans.tanggal_mulai, penugasans.tanggal_selesai, penugasans.target,
                lp.bulan_pengiriman, lp.tanggal_pengiriman, lp.jumlah_dikirim,
                lp.rr_kirim, lp.rating_kirim, lp.tipe_pengiriman, lp.status_penerimaan')
            ->get()
            ->map(function ($row) use ($bf) {
                $isPelunasan = $row->tipe_pengiriman === 'Pelunasan';
                $isCicilan   = $row->tipe_pengiriman === 'Cicilan';
                $target      = max(0, (int)($row->target ?? 0));
                $jml         = max(0, (int)($row->jumlah_dikirim ?? 0));
                
                $isActiveMonth = $row->bulan_pengiriman === $bf;
                
                $bobot = 0.0;
                if ($isActiveMonth) {
                    $bobot = $isPelunasan ? 1.0 : ($isCicilan && $target > 0 ? $jml / $target : 0.0);
                }
                
                $row->is_pelunasan      = $isPelunasan && $isActiveMonth;
                $row->bobot_parsial     = $bobot;
                $row->kontribusi_rr     = $isActiveMonth ? round((float)($row->rr_kirim     ?? 0) * $bobot, 4) : 0.0;
                $row->kontribusi_rating = $isActiveMonth ? round((float)($row->rating_kirim ?? 0) * 20 * $bobot, 4) : 0.0;
                $row->skor_f2 = $row->lama_rentang = $row->lama_pengiriman = null;
                $row->terlambat = false;
                if ($row->is_pelunasan && $row->tanggal_pengiriman) {
                    $lr = max(1, Carbon::parse($row->tanggal_mulai)->diffInDays(Carbon::parse($row->tanggal_selesai)));
                    $lp = max(0, Carbon::parse($row->tanggal_mulai)->diffInDays(Carbon::parse($row->tanggal_pengiriman), false));
                    $row->lama_rentang = $lr; $row->lama_pengiriman = $lp; $row->terlambat = $lp > $lr;
                    $row->skor_f2 = $lp <= $lr
                        ? 80.0 + (($lr-$lp)/$lr)*20.0
                        : max(70.0, 80.0 - min(10.0, (($lp-$lr)/$lr)*10.0));
                }
                
                $row->is_active_month = $isActiveMonth;
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
        $item->has_penugasan_aktif = ((int)($item->total_penugasan ?? 0)) > 0;
        $item->total_penugasan_dikerjakan = (int)($item->total_penugasan_dikerjakan ?? 0);
        $det = $details->get($item->id_pegawai, collect())->values();
        $item->details = $det;
        $a=$c=$pp=$pc=$bec=0.0;
        $a   = (float)($item->target_pegawai      ?? 0);
        $c   = (float) $gs['sum_target_semua'];
        $pp  = (float)($item->progress_pelunasan  ?? 0);
        $pc  = (float)($item->progress_cicilan    ?? 0);
        $bec = (float)($item->b_efektif_cicilan   ?? 0); // SUM(jumlah²/target) per penugasan
        $be  = $pp + $bec;                               // b_efektif proporsional
        $d   = max(0.0, $c - ($a - $be));
        $item->breakdown_formula = [
            'f1' => ['nilai'=>round((float)($item->f1_penyelesaian??0),2),
                'progress_pelunasan'=>$pp,'progress_cicilan'=>$pc,
                'b_efektif_cicilan'=>round($bec,4),
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
                    'kontribusi_rr'=>round($r->kontribusi_rr,4),'tipe_pengiriman'=>$r->tipe_pengiriman,
                    'bulan_pengiriman'=>$r->bulan_pengiriman,'is_active_month'=>$r->is_active_month])->values()],
            'f4' => ['nilai'=>round((float)($item->f4_rating_kirim??0),2),
                'detail'=>$det->map(fn($r)=>['nama_sub_kegiatan'=>$r->nama_sub_kegiatan,
                    'rating_kirim'=>$r->rating_kirim,'bobot_parsial'=>round($r->bobot_parsial,4),
                    'kontribusi_rating'=>round($r->kontribusi_rating,4),'tipe_pengiriman'=>$r->tipe_pengiriman,
                    'bulan_pengiriman'=>$r->bulan_pengiriman,'is_active_month'=>$r->is_active_month])->values()],
            'koefisien_beban'            => round((float)($item->koefisien_beban??1.0),4),
            'target_pegawai'             => $a,
            'avg_target_bulan'           => round($gs['avg_target_bulan'],2),
            'total_penugasan_dia'        => (int)($item->total_penugasan??0),
            'total_penugasan_dikerjakan' => (int)($item->total_penugasan_dikerjakan??0),
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
                return round($base * ($koef - 1.0), 2); // negatif = penalty
            })(),
            'ruang_ke_100'       =>round(max(0.0, 100.0 - (float)($item->rata_rata_base??0)), 2),
            'penentu_bonus'      =>(function() use ($item) {
                $base = (float)($item->rata_rata_base ?? 0);
                $koef = (float)($item->koefisien_beban ?? 1.0);
                if ($koef < 1.0) {
                    return 'penalty';
                }
                $bonusMax   = round($base * ($koef - 1.0), 2);
                $bonusRuang = round(max(0.0, 100.0 - $base), 2);
                return $bonusRuang <= $bonusMax ? 'ruang_ke_100' : 'beban_kerja';
            })(),
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

        $bf = sprintf('%04d-%02d', $year, $month);

        $totalPenugasan = Penugasan::where('tanggal_mulai', '<=', $endOfMonth) 
                        ->where('tanggal_selesai', '>=', $startOfMonth)
                        ->whereDoesntHave('pengirimans', function ($qp) use ($bf) {
                            $qp->where('tipe_pengiriman', 'Pelunasan')
                               ->where('bulan_pengiriman', '<', $bf)
                               ->whereHas('penerimaan', function ($qrec) {
                                   $qrec->where('status', 'Diterima');
                               });
                        })
                        ->count();
                        
        $penugasanSelesai = Penugasan::where('tanggal_mulai', '<=', $endOfMonth) 
                        ->where('tanggal_selesai', '>=', $startOfMonth)
                        ->whereDoesntHave('pengirimans', function ($qp) use ($bf) {
                            $qp->where('tipe_pengiriman', 'Pelunasan')
                               ->where('bulan_pengiriman', '<', $bf)
                               ->whereHas('penerimaan', function ($qrec) {
                                   $qrec->where('status', 'Diterima');
                               });
                        })
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

    public function getRekapSubKegiatanKetua($bulan, $tahun, bool $excludeSpecial = false)
    {
        $startOfMonth = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($tahun, $bulan, 1)->endOfMonth();
        $som = $startOfMonth->toDateString();
        $eom = $endOfMonth->toDateString();
        $bf  = sprintf('%04d-%02d', $tahun, $bulan);

        // Ambil semua SubKegiatan yang aktif di bulan filter beserta relasinya
        $subKegiatans = SubKegiatan::where('tanggal_mulai', '<=', $eom)
            ->where('tanggal_selesai', '>=', $som)
            ->with([
                'kegiatan.penanggungJawab',
                'kegiatan.transfer.fromKetua',
                'penugasans.pengirimans.penerimaan'
            ])
            ->get();

        // Olah di RAM memory
        $rekap = $subKegiatans->groupBy(function ($sub) use ($endOfMonth) {
            if (!$sub->kegiatan) {
                return 'tanpa-ketua';
            }
            if ($sub->kegiatan->transfer) {
                $transferredAt = \Carbon\Carbon::parse($sub->kegiatan->transfer->transferred_at);
                
                $transferredMonth = $transferredAt->format('Y-m');
                $filterMonth = $endOfMonth->format('Y-m');

                // Jika bulan filter sebelum bulan transfer, maka sub kegiatan masih milik ketua lama
                if ($filterMonth < $transferredMonth) {
                    return $sub->kegiatan->transfer->from_ketua_id ?? 'tanpa-ketua';
                }

                // Jika bulan filter adalah bulan transfer atau setelahnya,
                // maka dicek apakah sub kegiatan ini sudah 100% selesai SEBELUM tanggal transfer.
                $totalTargetPenugasan = $sub->penugasans->sum('target');
                $penugasanTargetSelesaiSebelumTransfer = $sub->penugasans->sum(function($p) use ($transferredAt) {
                    $pengirimansSebelumTransfer = $p->pengirimans->filter(function($k) use ($transferredAt) {
                        return $k->penerimaan &&
                               $k->penerimaan->status === 'Diterima' &&
                               \Carbon\Carbon::parse($k->penerimaan->created_at)->lt($transferredAt);
                    });

                    $adaPelunasan = $pengirimansSebelumTransfer->contains(fn($k) =>
                        $k->tipe_pengiriman === 'Pelunasan'
                    );

                    return $pengirimansSebelumTransfer->sum(fn($k) =>
                        $k->tipe_pengiriman === ($adaPelunasan ? 'Pelunasan' : 'Cicilan')
                            ? $k->jumlah_dikirim ?? 0
                            : 0
                    );
                });

                $is100PercentBeforeTransfer = ($totalTargetPenugasan > 0) && ($penugasanTargetSelesaiSebelumTransfer >= $totalTargetPenugasan);
                if ($is100PercentBeforeTransfer) {
                    return $sub->kegiatan->transfer->from_ketua_id ?? 'tanpa-ketua';
                }
            }
            return $sub->kegiatan->id_penanggung_jawab ?? 'tanpa-ketua';
        })
        ->reject(fn($group, $key) => $key === 'tanpa-ketua')
        ->map(function ($group, $ketuaId) use ($excludeSpecial) {
            $ketua = null;
            foreach ($group as $sub) {
                if ($sub->kegiatan->penanggungJawab && $sub->kegiatan->penanggungJawab->id_pegawai === $ketuaId) {
                    $ketua = $sub->kegiatan->penanggungJawab;
                    break;
                }
                if ($sub->kegiatan->transfer && $sub->kegiatan->transfer->fromKetua && $sub->kegiatan->transfer->fromKetua->id_pegawai === $ketuaId) {
                    $ketua = $sub->kegiatan->transfer->fromKetua;
                    break;
                }
            }

            if (!$ketua) {
                $ketua = \App\Models\Pegawai::find($ketuaId);
            }

            if (!$ketua) {
                return null;
            }

            if ($excludeSpecial && $ketua->nip_bps === '340017814') {
                return null;
            }

            $details = $group->map(function ($sub) {
                $totalTargetPenugasan = $sub->penugasans->sum('target');
                $penugasanTargetSelesai = $sub->penugasans->sum(function($p) {
                    $adaPelunasan = $p->pengirimans->contains(fn($k) =>
                        $k->tipe_pengiriman === 'Pelunasan' && $k->penerimaan?->status === 'Diterima'
                    );

                    return $p->pengirimans->sum(fn($k) =>
                        $k->penerimaan?->status === 'Diterima' &&
                        $k->tipe_pengiriman === ($adaPelunasan ? 'Pelunasan' : 'Cicilan')
                            ? $k->jumlah_dikirim ?? 0
                            : 0
                    );
                });

                $progressPercent = $totalTargetPenugasan ? round(($penugasanTargetSelesai / $totalTargetPenugasan) * 100) : 0;

                return [
                    'id_sub_kegiatan' => $sub->id_sub_kegiatan,
                    'nama_sub_kegiatan' => $sub->nama_sub_kegiatan,
                    'tanggal_mulai' => $sub->tanggal_mulai,
                    'tanggal_selesai' => $sub->tanggal_selesai,
                    'tanggal_mulai_formatted' => $sub->tanggal_mulai ? $sub->tanggal_mulai->translatedFormat('d M Y') : '-',
                    'tanggal_selesai_formatted' => $sub->tanggal_selesai ? $sub->tanggal_selesai->translatedFormat('d M Y') : '-',
                    'progress_percent' => $progressPercent,
                    'total_target' => $totalTargetPenugasan,
                    'total_realisasi' => $penugasanTargetSelesai,
                    'id_kegiatan' => $sub->id_kegiatan,
                ];
            });

            $totalSub = $details->count();
            $selesai = $details->filter(fn($d) => $d['progress_percent'] >= 100)->count();
            $belumSelesai = $totalSub - $selesai;
            $avgProgress = $totalSub ? round($details->avg('progress_percent'), 2) : 0;

            return (object) [
                'id_pegawai' => $ketua->id_pegawai,
                'nama_pegawai' => $ketua->nama_pegawai,
                'photo' => $ketua->photo,
                'nip_bps' => $ketua->nip_bps,
                'total_sub_kegiatan' => $totalSub,
                'sub_kegiatan_selesai' => $selesai,
                'sub_kegiatan_belum_selesai' => $belumSelesai,
                'average_progress' => $avgProgress,
                'details' => $details->values()->all(),
            ];
        })
        ->filter()
        ->values();

        return $rekap;
    }

    public function totalKegiatan(): int
    {
        return Penugasan::query()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
    }

    public function kegiatanSelesai(): int
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth   = now()->endOfMonth();

        return Penugasan::query()
            ->whereHas('pengirimans.penerimaan', function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('status', 'Diterima')
                    ->whereBetween('penerimaans.created_at', [$startOfMonth, $endOfMonth]);
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
