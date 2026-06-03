<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardAnalyticsService;
use App\Services\TodoListService;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardAnalyticsService $analytics, TodoListService $todoList)
    {
        $user = auth()->user();
        $rankPegawaiPerPageOptions = [5, 10, 25, 50];
        $rekapPenugasanPerPageOptions = [10, 25, 50, 100];
        $rankPegawaiPerPage = $this->resolvePerPage($request, 'rank_per_page', 5, $rankPegawaiPerPageOptions);
        $rekapPenugasanPerPage = $this->resolvePerPage($request, 'rekap_per_page', 10, $rekapPenugasanPerPageOptions);

        $unfinishedTerlewatAsAnggota = null;
        $unfinishedBerjalanAsAnggota = null;
        if ($user && $user->isAnggotaTim()) {
            $unfinishedTerlewatAsAnggota = $todoList->terlewatAsAnggota($user)
                ->paginate(5, ['*'], 'anggota_terlewat_page');

            $unfinishedBerjalanAsAnggota = $todoList->berjalanAsAnggota($user)
                ->paginate(5, ['*'], 'anggota_berjalan_page');

            $revisiAsAnggota = $todoList->revisiAsAnggota($user);
        }

        $unfinishedTerlewatAsKetua = null;
        $unfinishedBerjalanAsKetua = null;
        if ($user && $user->isKetuaTim()) {
            $unfinishedTerlewatAsKetua = $todoList->terlewatAsKetua($user)
                ->paginate(5, ['*'], 'ketua_terlewat_page');

            $unfinishedBerjalanAsKetua = $todoList->berjalanAsKetua($user)
                ->paginate(5, ['*'], 'ketua_berjalan_page');

            $revisiDlAsKetua = $todoList->revisiAsKetua($user);
        }

        $selectedMonth = request('month', now()->month);
        $selectedYear = request('year', now()->year);
        $stats = $analytics->getDashboardStats($selectedMonth, $selectedYear);

        // Gunakan rankPegawaiAll() — Collection tanpa paginator, aman untuk client-side pagination Alpine.js
        $rankPegawaiAll = $analytics->rankPegawaiAll($selectedMonth, $selectedYear, false);

        // Filter out Sukendro for the public rank table
        $rankPegawaiAllTable = $rankPegawaiAll->reject(function ($item) {
            return $item->nip_bps === '340017814';
        })->values();

        $bestEmployee = $rankPegawaiAllTable->first();

        // Rekap penugasan anggota difilter per bulan yang dipilih
        $rekapAnggota = $analytics->getRekapPenugasanPegawai($selectedMonth, $selectedYear, false);

        // Filter out Sukendro for the public rekap table
        $rekapAnggotaTable = $rekapAnggota->reject(function ($item) {
            return $item->nip_bps === '340017814';
        })->values();

        return view('pages.dashboard', [
            'title'                       => 'Dashboard',
            'unfinishedTerlewatAsAnggota' => $unfinishedTerlewatAsAnggota,
            'unfinishedBerjalanAsAnggota' => $unfinishedBerjalanAsAnggota,
            'revisiAsAnggota'             => $revisiAsAnggota ?? null,
            'unfinishedTerlewatAsKetua'   => $unfinishedTerlewatAsKetua,
            'unfinishedBerjalanAsKetua'   => $unfinishedBerjalanAsKetua,
            'revisiDlAsKetua'             => $revisiDlAsKetua ?? null,
            'stats'                       => $stats,
            'selectedMonth'               => $selectedMonth,
            'selectedYear'                => $selectedYear,
            'bestEmployee'                => $bestEmployee,
            'rankPegawaiAll'              => $rankPegawaiAll,
            'rankPegawaiAllTable'         => $rankPegawaiAllTable,
            'rekapAnggota'                => $rekapAnggota,
            'rekapAnggotaTable'           => $rekapAnggotaTable,
            'rankPegawaiPerPage'          => $rankPegawaiPerPage,
            'rekapPenugasanPerPage'       => $rekapPenugasanPerPage,
            'rankPegawaiPerPageOptions'   => $rankPegawaiPerPageOptions,
            'rekapPerPageOptions'         => $rekapPenugasanPerPageOptions,
        ]);
    }

    private function resolvePerPage(Request $request, string $key, int $default, array $allowedOptions): int
    {
        $perPage = (int) $request->query($key, $default);

        return in_array($perPage, $allowedOptions, true) ? $perPage : $default;
    }
}
