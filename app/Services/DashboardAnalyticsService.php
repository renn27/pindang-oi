<?php

namespace App\Services;

use App\Models\Penugasan;
use App\Models\Pengiriman;
use Illuminate\Support\Facades\Cache;

class DashboardAnalyticsService
{
    public function rankPegawai(int $limit = 5)
    {
        return Cache::remember(
            "dashboard_rank_pegawai_{$limit}",
            now()->addMinutes(10),

            function () use ($limit) {

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

                // Ambil max RR dari latest saja
                $maxRR = (clone $latestPengiriman)->max('rr_kirim') ?? 100;

                return Penugasan::query()

                    ->joinSub($latestPengiriman, 'latest_pengiriman', function ($join) {
                        $join->on('penugasans.id_penugasan', '=', 'latest_pengiriman.id_penugasan');
                    })

                    ->join('pegawais', 'pegawais.id_pegawai', '=', 'penugasans.id_anggota')

                    ->selectRaw("
                        pegawais.id_pegawai,
                        pegawais.nama_pegawai,

                        SUM(latest_pengiriman.rr_kirim) as total_rr,
                        AVG(latest_pengiriman.rating_kirim) as avg_rating,

                        (
                            ((SUM(latest_pengiriman.rr_kirim) / ?) * 5) * 0.7 +
                            AVG(latest_pengiriman.rating_kirim) * 0.3
                        ) as score
                    ", [$maxRR])

                    ->groupBy('pegawais.id_pegawai', 'pegawais.nama_pegawai')
                    ->orderByDesc('score')
                    ->limit($limit)
                    ->get();
            }
        );
    }

}
