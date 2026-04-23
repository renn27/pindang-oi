<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penugasan;
use App\Models\SubKegiatan;
use App\Models\Pengiriman;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengirimanController extends Controller
{
    // public function store(Request $request, SubKegiatan $subKegiatan, Penugasan $penugasan)
    // {
    //     // dd($request->all());
    //     // Autorisasi
    //     $this->authorize('send', $penugasan);

    //     // Validasi
    //     $validated = $request->validate([
    //         'tanggal_pengiriman' => ['required', 'date'],
    //         'jumlah_dikirim'     => ['required', 'integer', 'min:1'],
    //         'media_pengiriman'   => ['required', 'string', 'max:255'],
    //         'bukti_dukung'       => ['required', 'string', 'max:255'],
    //     ]);

    //     try {
    //         DB::transaction(function () use ($penugasan, $validated) {

    //             /* =====================================================
    //             * 1️⃣ HITUNG RR KIRIM (PERSEN)
    //             * ===================================================== */

    //             // Target penugasan
    //             $targetPenugasan = (int) $penugasan->target;

    //             // Jumlah yang dikirim
    //             $jumlahDikirim = (int) $validated['jumlah_dikirim'];

    //             $rrKirim = $targetPenugasan > 0
    //                 ? round(($jumlahDikirim / $targetPenugasan) * 100, 2)
    //                 : 0;

    //             // Batasi maksimal 100%
    //             $rrKirim = min($rrKirim, 100);


    //             /* =====================================================
    //             * 2️⃣ HITUNG RATING KIRIM (BINTANG 1–5)
    //             * ===================================================== */

    //             // Waktu pengiriman (real)
    //             $tanggalPengiriman = Carbon::parse($validated['tanggal_pengiriman'])->startOfDay();

    //             // Deadline = TANGGAL selesai (bukan endOfDay)
    //             $tanggalDeadline = Carbon::parse($penugasan->tanggal_selesai)->startOfDay();

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Hitung hari keterlambatan
    //             |--------------------------------------------------------------------------
    //             | - Kirim di tanggal selesai → 0 hari telat
    //             | - Kirim H+1 → 1 hari telat
    //             | - Kirim H+2 → 2 hari telat
    //             */
    //             if ($tanggalPengiriman->lte($tanggalDeadline)) {
    //                 $hariTelat = 0;
    //             } else {
    //                 $hariTelat = (int) $tanggalDeadline->diffInDays($tanggalPengiriman);
    //                 // dd($hariTelat);
    //             }

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Mapping hari telat → rating
    //             |--------------------------------------------------------------------------
    //             */
    //             $ratingKirim = match (true) {
    //                 $hariTelat === 0 => 5,
    //                 $hariTelat === 1 => 4,
    //                 $hariTelat === 2 => 3,
    //                 $hariTelat === 3 => 2,
    //                 default          => 1,
    //             };

    //             /* =====================================================
    //             * 3️⃣ SIMPAN DATA PENGIRIMAN
    //             * ===================================================== */
    //             $penugasan->pengirimans()->create([
    //                 'tanggal_pengiriman' => $validated['tanggal_pengiriman'],
    //                 'jumlah_dikirim'     => $jumlahDikirim,
    //                 'media_pengiriman'   => $validated['media_pengiriman'],
    //                 'bukti_dukung'       => $validated['bukti_dukung'],
    //                 'rr_kirim'           => $rrKirim,
    //                 'rating_kirim'       => $ratingKirim,
    //             ]);

    //             /* =====================================================
    //             * 4️⃣ UPDATE STATUS PENUGASAN
    //             * ===================================================== */
    //             $penugasan->update([
    //                 'status' => 'Sudah Dikirim',
    //             ]);
    //         });

    //         return redirect()
    //             ->route('sub.kegiatan.show', [
    //                 'kegiatan'    => $penugasan->subKegiatan->kegiatan->id_kegiatan,
    //                 'subKegiatan' => $penugasan->subKegiatan->id_sub_kegiatan,
    //             ])
    //             ->with('success', 'Pengiriman hasil kerja berhasil dilakukan.');

    //     } catch (\Exception $e) {
    //         dd($e->getMessage());
    //         return redirect()
    //             ->back()
    //             ->with('error', 'Gagal mengirimkan hasil kerja. Silakan coba lagi.')
    //             ->withInput();
    //     }
    // }

    // public function delete(Request $request, Pengiriman $pengiriman)
    // {
    //     $penugasan = $pengiriman->penugasan;
    //     $this->authorize('cancelSend', $penugasan);

    //     // Pastikan pengiriman ini bukan pengiriman yang sudah memiliki penerimaan
    //     if ($pengiriman->penerimaan) {
    //         return redirect()->back()->with('error', 'Data pengiriman tidak bisa dibatalkan karena sudah ditanggapi oleh Ketua Tim.');
    //     }

    //     try {
    //         DB::transaction(function () use ($penugasan, $pengiriman) {
    //             $pengiriman->forceDelete();

    //             // Cek jika tidak ada pengiriman lagi, kembalikan status penugasan
    //             $remainingCount = $penugasan->pengirimans()->count();
    //             if ($remainingCount === 0) {
    //                 $penugasan->update(['status' => 'Belum Dikirim']);
    //             }
    //         });

    //         return redirect()
    //             ->route('sub.kegiatan.show', [
    //                 'kegiatan'    => $penugasan->subKegiatan->kegiatan->id_kegiatan,
    //                 'subKegiatan' => $penugasan->subKegiatan->id_sub_kegiatan,
    //             ])
    //             ->with('success', 'Pengiriman kerja berhasil dibatalkan.');

    //     } catch (\Exception $e) {
    //         \Illuminate\Support\Facades\Log::error('Cancel pengiriman error: ' . $e->getMessage());
    //         return redirect()
    //             ->back()
    //             ->with('error', 'Gagal membatalkan pengiriman. Silakan coba lagi.');
    //     }
    // }

}
