<?php

namespace App\Http\Controllers;

use App\Models\KalenderDL;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
                ]);
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


    public function store(Request $request) {
        // dd($request->all());
        $validated = $request->validate([
            'id_pegawai' => ['required', 'exists:pegawais,id_pegawai'],
            'tanggal_dl' => ['required', 'date',]
        ]);

        try {
            // Simpan
            KalenderDL::create($validated);

            // Redirect dengan flash message
            return redirect()
                ->route('kalenderDL.index')
                ->with('success', 'Berhasil Memasukkan ke dalam Kalender DL.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            Log::error('Gagal simpan Kalender DL: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memasukkan ke dalam Kalender DL. Silakan coba lagi.')
                ->withInput();
        }
    }   
}
