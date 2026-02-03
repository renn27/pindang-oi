<?php

namespace App\Services;

use App\Models\Penugasan;
use App\Models\Pengiriman;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use Illuminate\Support\Facades\Cache;

class DashboardAnalyticsService
{
    public function rankPegawai(int $perPage = 5)
    {
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

    /**
     * Get statistik lengkap untuk dashboard
     */
    public function getDashboardStats(): array
    {
        // Total Kegiatan
        $totalKegiatan = Kegiatan::count();

        // Total Sub Kegiatan
        $totalSubKegiatan = SubKegiatan::count();

        // Total Penugasan
        $totalPenugasan = Penugasan::count();

        // Total Penugasan Selesai (yang sudah diterima)
        // Menggunakan query manual untuk menghindari masalah relationship
        $penugasanSelesai = Penugasan::whereHas('pengirimans.penerimaan', function ($query) {
            $query->where('status', 'Diterima');
        })->count();

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

    /**
     * Get total kegiatan semua pegawai
     */
    public function totalKegiatan(): int
    {
        return Penugasan::query()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Get jumlah kegiatan selesai semua pegawai
     */
    public function kegiatanSelesai(): int
    {
        // Ambil penugasan dengan pengiriman yang sudah diterima
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
