<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penugasan;
use App\Models\SubKegiatan;
use Illuminate\Support\Facades\DB;

class PengirimanController extends Controller
{
    public function store(Request $request, SubKegiatan $subKegiatan, Penugasan $penugasan)
    {
        // dd($request->all());
        // Autorisasi
        $this->authorize('send', $penugasan);

        // Validasi
        $validated = $request->validate([
            'tanggal_pengiriman' => ['required', 'date', 'date_format:Y-m-d'],
            'jumlah_dikirim'     => ['required', 'integer', 'min:1'],
            'media_pengiriman'   => ['required', 'string', 'max:255'],
            'bukti_dukung'       => ['required', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($penugasan, $validated) {

                /* =====================================================
                * 1️⃣ HITUNG RR KIRIM (PERSEN)
                * ===================================================== */
                $target = (int) $penugasan->target; // target penugasan
                $jumlah = (int) $validated['jumlah_dikirim'];

                $rrKirim = $target > 0
                    ? round(($jumlah / $target) * 100, 2)
                    : 0;

                // Anti over 100%
                $rrKirim = min($rrKirim, 100);

                /* =====================================================
                * 2️⃣ HITUNG RATING KIRIM (BINTANG 1–5)
                * ===================================================== */
                $tanggalPengiriman = \Carbon\Carbon::parse($validated['tanggal_pengiriman']);
                $tanggalSelesai    = \Carbon\Carbon::parse($penugasan->tanggal_selesai);

                // Selisih hari (negatif = lebih cepat)
                $selisihHari = $tanggalPengiriman->diffInDays($tanggalSelesai, false);

                if ($selisihHari <= 0) {
                    $ratingKirim = 5;
                } elseif ($selisihHari === 1) {
                    $ratingKirim = 4;
                } elseif ($selisihHari === 2) {
                    $ratingKirim = 3;
                } elseif ($selisihHari === 3) {
                    $ratingKirim = 2;
                } else {
                    $ratingKirim = 1;
                }

                /* =====================================================
                * 3️⃣ SIMPAN DATA PENGIRIMAN
                * ===================================================== */
                $penugasan->pengirimans()->create([
                    'tanggal_pengiriman' => $validated['tanggal_pengiriman'],
                    'jumlah_dikirim'     => $jumlah,
                    'media_pengiriman'   => $validated['media_pengiriman'],
                    'bukti_dukung'       => $validated['bukti_dukung'],
                    'rr_kirim'           => $rrKirim,
                    'rating_kirim'       => $ratingKirim,
                ]);

                /* =====================================================
                * 4️⃣ UPDATE STATUS PENUGASAN
                * ===================================================== */
                $penugasan->update([
                    'status' => 'Sudah Dikirim',
                ]);
            });

            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan'    => $penugasan->subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $penugasan->subKegiatan->id_sub_kegiatan,
                ])
                ->with('success', 'Pengiriman hasil kerja berhasil dilakukan.');

        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengirimkan hasil kerja. Silakan coba lagi.')
                ->withInput();
        }
    }

}
