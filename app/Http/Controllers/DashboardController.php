<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();

        $unfinishedAsAnggota = null;
        if ($user && $user->isAnggotaTim()) {
            $unfinishedAsAnggota = \App\Models\Penugasan::with(['subKegiatan.kegiatan.bidang', 'jenisKegiatan', 'anggota'])
                ->where('id_anggota', $user->id_pegawai)
                ->whereDoesntHave('pengirimans.penerimaan', function ($q) {
                    $q->where('status', 'Diterima');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'anggota_page');
        }

        $unfinishedAsKetua = null;
        if ($user && $user->isKetuaTim()) {
            $unfinishedAsKetua = \App\Models\Penugasan::with(['subKegiatan.kegiatan.bidang', 'jenisKegiatan', 'anggota'])
                ->whereHas('subKegiatan.kegiatan', function ($q) use ($user) {
                    $q->where('id_penanggung_jawab', $user->id_pegawai);
                })
                ->whereDoesntHave('pengirimans.penerimaan', function ($q) {
                    $q->where('status', 'Diterima');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'ketua_page');
        }

        return view('pages.dashboard', [
            'title'               => 'Dashboard',
            // 'unfinishedAsAnggota' => $unfinishedAsAnggota,
            // 'unfinishedAsKetua'   => $unfinishedAsKetua,
        ]);
    }
}
