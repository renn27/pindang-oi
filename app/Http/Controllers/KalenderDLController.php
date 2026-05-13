<?php

namespace App\Http\Controllers;

use App\Models\KalenderDL;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Penugasan;
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


    public function delete($id_penugasan)
    {
        try {
            KalenderDL::where('id_penugasan', $id_penugasan)->delete();
            
            $penugasan = Penugasan::find($id_penugasan);
            if ($penugasan) {
                $updateData = [];
                if ($penugasan->status_dl === 'ACC') {
                    $updateData['status_dl'] = 'Menunggu';
                }
                if ($penugasan->status_translok === 'ACC') {
                    $updateData['status_translok'] = 'Menunggu';
                }
                if (!empty($updateData)) {
                    $penugasan->update($updateData);
                }
            }
            
            return redirect()
                ->route('master-kegiatan.index_rk_dl')
                ->with('success', 'Berhasil mencabut DL dari Kalender dan status dikembalikan ke Menunggu.');
        } catch (\Exception $e) {
            Log::error('Gagal hapus Kalender DL: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal mencabut DL dari Kalender.');
        }
    }
}
