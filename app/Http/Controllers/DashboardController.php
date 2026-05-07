<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penugasan;
use App\Services\DashboardAnalyticsService;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardAnalyticsService $analytics)
    {
        $user = auth()->user();

        $unfinishedTerlewatAsAnggota = null;
        $unfinishedBerjalanAsAnggota = null;
        if ($user && $user->isAnggotaTim()) {
            $baseQuery = Penugasan::with(['subKegiatan.kegiatan.bidang', 'jenisKegiatan', 'anggota'])
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

            // Penugasan yang pengiriman terbarunya sudah ada penerimaan berstatus 'Revisi'
            // (belum kirim ulang setelah revisi)
            $revisiAsAnggota = Penugasan::with(['subKegiatan.kegiatan.bidang', 'jenisKegiatan', 'anggota', 'latestPengiriman.penerimaan'])
                ->where('id_anggota', $user->id_pegawai)
                ->whereHas('pengirimans.penerimaan', function ($q) {
                    $q->where('status', 'Revisi');
                })
                ->whereDoesntHave('pengirimans.penerimaan', function ($q) {
                    $q->where('status', 'Diterima');
                })
                ->get()
                ->filter(function ($penugasan) {
                    // Pastikan pengiriman TERAKHIR memiliki penerimaan berstatus Revisi
                    // (bukan pengiriman ulang yang belum direspons)
                    $latest = $penugasan->latestPengiriman;
                    return $latest && $latest->penerimaan && $latest->penerimaan->status === 'Revisi';
                });
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

            // Penugasan DL / Translok milik anggota tim yg ditolak (Revisi) oleh Pimpinan
            $revisiDlAsKetua = Penugasan::with(['subKegiatan.kegiatan.bidang', 'jenisKegiatan', 'anggota'])
                ->whereHas('subKegiatan.kegiatan', function ($q) use ($user) {
                    $q->where('id_penanggung_jawab', $user->id_pegawai);
                })
                ->where(function ($q) {
                    $q->where('status_dl', 'Ditolak')
                      ->orWhere('status_translok', 'Ditolak');
                })
                ->orderBy('tanggal_selesai', 'asc')
                ->get();
        }

        $selectedMonth = request('month', now()->month);
        $selectedYear = request('year', now()->year);
        $stats = $analytics->getDashboardStats($selectedMonth, $selectedYear);

        // Gunakan rankPegawaiAll() — Collection tanpa paginator, aman untuk client-side pagination Alpine.js
        $rankPegawaiAll = $analytics->rankPegawaiAll($selectedMonth, $selectedYear);
        $bestEmployee   = $rankPegawaiAll->first();

        // Rekap penugasan anggota difilter per bulan yang dipilih
        $rekapAnggota = $analytics->getRekapPenugasanPegawai($selectedMonth, $selectedYear);

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
            'rekapAnggota'                => $rekapAnggota,
        ]);
    }
}
