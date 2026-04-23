<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penugasan;
use App\Models\SubKegiatan;
use App\Models\Pengiriman;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Models\Penerimaan;

class PenerimaanController extends Controller
{
    public function store(Request $request, SubKegiatan $subKegiatan, Penugasan $penugasan, Pengiriman $pengirimans) {
        $this->authorize('receive', $penugasan);

        // Validasi
        $validated = $request->validate([
            'tanggal_penerimaan' => ['required', 'date', 'date_format:Y-m-d'],
            'jumlah_diterima'    => ['required', 'integer', 'min:1'],
            'status'             => ['required', 'in:Diterima,Revisi'],
            'catatan'            => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($validated, $pengirimans, $penugasan) {

                /* =====================================================
                * 1️⃣ HITUNG RR TERIMA (PERSEN)
                * ===================================================== */

                // Target dari penugasan
                $targetPenugasan = (int) $penugasan->target;

                // Jumlah diterima (input user)
                $jumlahDiterima = (int) $validated['jumlah_diterima'];

                $rrTerima = $targetPenugasan > 0
                    ? round(($jumlahDiterima / $targetPenugasan) * 100, 2)
                    : 0;

                // Anti over 100%
                $rrTerima = min($rrTerima, 100);


                /* =====================================================
                * 2️⃣ HITUNG RATING TERIMA (BINTANG 1–5)
                * ===================================================== */

                // Waktu penerimaan (dibulatkan ke awal hari)
                $tanggalPenerimaan = Carbon::parse($validated['tanggal_penerimaan'])->startOfDay();

                // Deadline penugasan (tanggal selesai, awal hari)
                $tanggalDeadline = Carbon::parse($penugasan->tanggal_selesai)->startOfDay();

                /*
                |--------------------------------------------------------------------------
                | Hitung hari keterlambatan penerimaan (INTEGER)
                |--------------------------------------------------------------------------
                | - Terima <= deadline → 0 hari telat
                | - Terima H+1 → 1 hari telat
                */
                if ($tanggalPenerimaan->lte($tanggalDeadline)) {
                    $hariTelat = 0;
                } else {
                    $hariTelat = (int) $tanggalDeadline->diffInDays($tanggalPenerimaan);
                }

                /*
                |--------------------------------------------------------------------------
                | Mapping hari telat → rating penerimaan
                |--------------------------------------------------------------------------
                */
                $ratingTerima = match (true) {
                    $hariTelat === 0 => 5,
                    $hariTelat === 1 => 4,
                    $hariTelat === 2 => 3,
                    $hariTelat === 3 => 2,
                    default          => 1,
                };

                /* =====================================================
                * 3️⃣ SIMPAN DATA PENERIMAAN
                * ===================================================== */
                $pengirimans->penerimaan()->create([
                    'tanggal_penerimaan' => $validated['tanggal_penerimaan'],
                    'jumlah_diterima'    => $jumlahDiterima,
                    'status'             => $validated['status'],
                    'catatan'            => $validated['catatan'],
                    'rr_terima'          => $rrTerima,
                    'rating_terima'      => $ratingTerima,

                    // ✅ ID PENERIMA (USER LOGIN)
                    'id_penerima'        => Auth::id(),
                ]);
            });

            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan'    => $penugasan->subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $subKegiatan->id_sub_kegiatan,
                ])
                ->with('success', 'Penerimaan hasil kerja berhasil dilakukan.');

        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Gagal melakukan penerimaan hasil kerja. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function delete(Penerimaan $penerimaan)
    {
        $pengiriman = $penerimaan->pengiriman;
        $penugasan  = $pengiriman->penugasan;

        // Otorisasi: hanya Ketua Tim yang mengelola penugasan ini
        $this->authorize('cancelReceive', $penugasan);

        /*
        |--------------------------------------------------------------------------
        | Cek apakah session masih valid untuk pembatalan
        |--------------------------------------------------------------------------
        | Penerimaan ke-N hanya bisa dibatalkan jika BELUM ada pengiriman ke-(N+1).
        | Artinya: tidak boleh ada pengiriman yang dibuat SETELAH pengiriman
        | yang menjadi milik penerimaan ini.
        */
        $adaPengirimanLebihBaru = $penugasan->pengirimans()
            ->where('created_at', '>', $pengiriman->created_at)
            ->exists();

        if ($adaPengirimanLebihBaru) {
            return redirect()
                ->back()
                ->with('error', 'Penerimaan tidak bisa dibatalkan karena anggota sudah mengirimkan pengiriman berikutnya.');
        }

        try {
            DB::transaction(function () use ($penerimaan) {
                $penerimaan->forceDelete();
            });

            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan'    => $penugasan->subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $penugasan->subKegiatan->id_sub_kegiatan,
                ])
                ->with('success', 'Penerimaan berhasil dibatalkan. Silakan lakukan penerimaan ulang.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Cancel penerimaan error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Gagal membatalkan penerimaan. Silakan coba lagi.');
        }
    }
}
