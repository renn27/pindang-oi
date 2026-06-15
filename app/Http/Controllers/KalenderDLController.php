<?php

namespace App\Http\Controllers;

use App\Models\KalenderDL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // 🔹 INI YANG DITAMBAHKAN: withCount by month (dan filter pegawai aktif di bulan ini)
        $pegawais = Pegawai::activeInMonth((int) $month, (int) $year)
            ->with(['kalenderDls' => function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_dl', [
                    $start->toDateString(),
                    $end->toDateString()
                ])->with(['penugasan', 'agendaPimpinan']);
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
            $penugasan = Penugasan::find($id_penugasan);
            
            // Cek apakah penugasan sudah memiliki CKP sebelum menghapus apapun
            if ($penugasan && $penugasan->ckp()->exists()) {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus! Penugasan ini tidak bisa dicabut karena sudah masuk ke dalam CKP.');
            }

            DB::transaction(function () use ($id_penugasan, $penugasan) {
                KalenderDL::where('id_penugasan', $id_penugasan)->delete();

                if ($penugasan) {
                    $penugasan->forceDelete();
                }
            });
            
            return redirect()
                ->route('master-kegiatan.index_rk_dl')
                ->with('success', 'Berhasil menghapus DL dari Kalender dan penugasan.');
            
        } catch (\Exception $e) {
            Log::error('Gagal hapus data penugasan dan Kalender DL: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan. Gagal mencabut DL dari Kalender dan menghapus data penugasan.');
        }
    }
}
