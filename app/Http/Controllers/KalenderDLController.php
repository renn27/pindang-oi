<?php

namespace App\Http\Controllers;

use App\Models\KalenderDL;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class KalenderDLController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        // Generate list tanggal dalam 1 bulan
        $dates = [];
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $dates[] = $d->copy();
        }

        $title = 'Kalender Dinas Luar';

        // 🔹 INI YANG DITAMBAHKAN: withCount by month
        $pegawais = Pegawai::with(['kalenderDls' => function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_dl', [
                    $start->toDateString(),
                    $end->toDateString()
                ])->with('penugasan');
            }])
            ->withCount(['kalenderDls as total_dl_bulan_ini' => function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_dl', [
                    $start->toDateString(),
                    $end->toDateString()
                ]);
            }])
            ->orderBy('nama_pegawai')
            ->get();

        return view("pages.main.pegawai.kalender-dl.index", [
            'pegawais' => $pegawais,
            'dates' => $dates,
            'month' => $month,
            'year' => $year,
            'title' => $title,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'id_pegawai'      => ['required', 'exists:pegawais,id_pegawai'],
            'id_penugasan'    => ['required', 'exists:penugasans,id_penugasan'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            ]);

        try {
            $period = CarbonPeriod::create(
                $validated['tanggal_mulai'],
                $validated['tanggal_selesai']
            );

            foreach ($period as $date) {
                // 🔒 Cek duplikat per tanggal
                $exists = KalenderDL::where('id_pegawai', $validated['id_pegawai'])
                    ->where('tanggal_dl', $date)
                    ->exists();

                if (!$exists) {
                    KalenderDL::create([
                        'id_pegawai' => $validated['id_pegawai'],
                        'id_penugasan' => $validated['id_penugasan'],
                        'tanggal_dl' => $date,
                    ]);
                }
            }

            return redirect()
                ->route('master-kegiatan.index_rk_dl')
                ->with('success', 'Berhasil memasukkan DL sesuai range tanggal.');

        } catch (\Exception $e) {
            Log::error('Gagal simpan Kalender DL: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memasukkan ke Kalender DL.')
                ->withInput();
        }
    }
}
