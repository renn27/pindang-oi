<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardAnalyticsService;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardAnalyticsService $analytics)
    {
        $user = auth()->user();

        $unfinishedTerlewatAsAnggota = null;
        $unfinishedBerjalanAsAnggota = null;
        if ($user && $user->isAnggotaTim()) {
            $baseQuery = \App\Models\Penugasan::with(['subKegiatan.kegiatan.bidang', 'jenisKegiatan', 'anggota'])
                ->where('id_anggota', $user->id_pegawai)
                ->whereDoesntHave('pengirimans.penerimaan', function ($q) {
                    $q->where('status', 'Diterima');
                });
                
            $unfinishedTerlewatAsAnggota = (clone $baseQuery)
                ->where('tanggal_selesai', '<', now()->format('Y-m-d'))
                ->orderBy('tanggal_selesai', 'asc')
                ->paginate(5, ['*'], 'anggota_terlewat_page');

            $unfinishedBerjalanAsAnggota = (clone $baseQuery)
                ->where('tanggal_selesai', '>=', now()->format('Y-m-d'))
                ->orderBy('tanggal_selesai', 'asc')
                ->paginate(5, ['*'], 'anggota_berjalan_page');
        }

        $unfinishedTerlewatAsKetua = null;
        $unfinishedBerjalanAsKetua = null;
        if ($user && $user->isKetuaTim()) {
            $baseQueryKetua = \App\Models\Penugasan::with(['subKegiatan.kegiatan.bidang', 'jenisKegiatan', 'anggota'])
                ->whereHas('subKegiatan.kegiatan', function ($q) use ($user) {
                    $q->where('id_penanggung_jawab', $user->id_pegawai);
                })
                ->whereDoesntHave('pengirimans.penerimaan', function ($q) {
                    $q->where('status', 'Diterima');
                });

            $unfinishedTerlewatAsKetua = (clone $baseQueryKetua)
                ->where('tanggal_selesai', '<', now()->format('Y-m-d'))
                ->orderBy('tanggal_selesai', 'asc')
                ->paginate(5, ['*'], 'ketua_terlewat_page');

            $unfinishedBerjalanAsKetua = (clone $baseQueryKetua)
                ->where('tanggal_selesai', '>=', now()->format('Y-m-d'))
                ->orderBy('tanggal_selesai', 'asc')
                ->paginate(5, ['*'], 'ketua_berjalan_page');
        }

        $selectedMonth = request('month', now()->month);
        $selectedYear = request('year', now()->year);
        $stats = $analytics->getDashboardStats($selectedMonth, $selectedYear);

        $bestEmployee = $analytics->rankPegawai(1, $selectedMonth, $selectedYear)->first();
        $rankPegawai = $analytics->rankPegawai(5, $selectedMonth, $selectedYear);

        return view('pages.dashboard', [
            'title'                       => 'Dashboard',
            'unfinishedTerlewatAsAnggota' => $unfinishedTerlewatAsAnggota,
            'unfinishedBerjalanAsAnggota' => $unfinishedBerjalanAsAnggota,
            'unfinishedTerlewatAsKetua'   => $unfinishedTerlewatAsKetua,
            'unfinishedBerjalanAsKetua'   => $unfinishedBerjalanAsKetua,
            'stats'                       => $stats,
            'selectedMonth'               => $selectedMonth,
            'selectedYear'                => $selectedYear,
            'bestEmployee'                => $bestEmployee,
            'rankPegawai'                 => $rankPegawai,
        ]);
    }
}
