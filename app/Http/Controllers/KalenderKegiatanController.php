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
        
        // Menangani Parameter Bulan dan Tahun dari URL
        $month = $request->query('month', date('n'));
        $year = $request->query('year', date('Y'));
        
        // Filter range bulan ini
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $subKegiatans = SubKegiatan::with([
                'kegiatan.bidang:id_bidang,nama_bidang', // Narik relasi ke bidang
                'kegiatan:id_kegiatan,id_bidang,nama_rk_kegiatan', 
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
            ->where(function ($q) use ($startDate, $endDate) {
                // Filter sub kegiatan yang pelaksanaannya melintasi / ada di dalam bulan ini
                $q->where('tanggal_mulai', '<=', $endDate->format('Y-m-d'))
                  ->where('tanggal_selesai', '>=', $startDate->format('Y-m-d'));
            })
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
                'bidang'      => $sub->kegiatan->bidang->nama_bidang ?? 'Umum',
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
            'events'       => $events,
            'pegawais'     => $pegawais,
            'activeFilter' => $pegawaiId ?? 'all',
            'currentMonth' => $month, // Kirim parameter ke view
            'currentYear'  => $year,  // Kirim parameter ke view
        ]);
    }

}
