<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubKegiatan;
use App\Models\Pegawai;
use Carbon\Carbon;

class KalenderKegiatanController extends Controller
{

    public function index(Request $request) {
        $pegawaiId = $request->query('pegawai');

        $subKegiatans = SubKegiatan::with([
                'kegiatan:id_kegiatan,nama_rk_kegiatan',
                'penugasans.anggota:id_pegawai,nama_pegawai'
            ])
            ->when(
                $pegawaiId && $pegawaiId !== 'all',
                function ($query) use ($pegawaiId) {
                    $query->whereHas('penugasans', function ($q) use ($pegawaiId) {
                        $q->where('id_pegawai', $pegawaiId);
                    });
                }
            )
            ->orderBy('tanggal_mulai')
            ->get();

        /**
         * =============================
         * TRANSFORM DATA UNTUK KALENDER
         * =============================
         */
        $events = $subKegiatans->map(function ($sub) {

            $today = Carbon::today();

            if ($today->lt($sub->tanggal_mulai)) {
                $status = 'Belum Mulai';
            } elseif ($today->between($sub->tanggal_mulai, $sub->tanggal_selesai)) {
                $status = 'Berjalan';
            } else {
                $status = 'Selesai';
            }

            return [
                'id'          => $sub->id_sub_kegiatan,
                'title'       => $sub->nama_sub_kegiatan,
                'start_date'  => $sub->tanggal_mulai,
                'end_date'    => $sub->tanggal_selesai,
                'status'      => $status,
                'participants'=> $sub->penugasans
                    ->pluck('anggota.nama_pegawai')
                    ->filter()
                    ->values()
                    ->toArray(),
            ];
        })->values();

        /**
         * =============================
         * DATA DROPDOWN FILTER PEGAWAI
         * =============================
         */
        $pegawais = Pegawai::select('id_pegawai', 'nama_pegawai')
            ->orderBy('nama_pegawai')
            ->get();

        /**
         * =============================
         * RETURN VIEW
         * =============================
         */
        return view('pages.main.pegawai.kalender-kegiatan.index', [
            'title'        => 'Kalender Kegiatan',
            'events'       => $events,      // 🔥 DATA SIAP JSON
            'pegawais'     => $pegawais,
            'activeFilter' => $pegawaiId ?? 'all',
        ]);
    }

}
