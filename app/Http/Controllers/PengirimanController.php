<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penugasan;
use App\Models\SubKegiatan;
use App\Models\Pengiriman;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\PushNotificationService;

class PengirimanController extends Controller
{
    public function store(Request $request, SubKegiatan $subKegiatan, Penugasan $penugasan)
    {
        // dd($request->all());
        // Autorisasi
        $this->authorize('send', $penugasan);

        // Validasi
        $validated = $request->validate([
            'tanggal_pengiriman' => ['required', 'date'],
            'jumlah_dikirim'     => ['required', 'integer', 'min:1', 'max:' . $penugasan->target],
            'bulan_pengiriman'   => ['required', 'string', 'size:7'],
            'tipe_pengiriman'    => ['required', 'string', 'in:Cicilan,Pelunasan'],
            'media_pengiriman'   => ['required', 'string', 'max:255'],
            'bukti_dukung'       => ['required', 'string', 'max:255'],
            'catatan'            => ['nullable', 'string', 'max:255'],
        ]);

        // ── Guard 1: Cek bulan ini belum punya pengiriman "Diterima" ──────────────
        $sudahAdaDiBulanIni = $penugasan->pengirimans()
            ->where('bulan_pengiriman', $validated['bulan_pengiriman'])
            ->whereHas('penerimaan', fn($q) => $q->where('status', 'Diterima'))
            ->exists();

        if ($sudahAdaDiBulanIni) {
            return back()
                ->with('error', 'Bulan ini sudah memiliki pengiriman yang diterima.')
                ->withInput();
        }

        // ── Guard 2: Cegah Cicilan di bulan terakhir rentang penugasan ──────────
        $bulanTerakhir = Carbon::parse($penugasan->tanggal_selesai)->format('Y-m');
        if ($validated['tipe_pengiriman'] === 'Cicilan' && $validated['bulan_pengiriman'] === $bulanTerakhir) {
            return back()
                ->with('error', 'Tidak bisa memilih Cicilan di bulan terakhir penugasan. Gunakan Pelunasan.')
                ->withInput();
        }

        try {
            DB::transaction(function () use ($penugasan, $validated) {

                // 1️⃣ HITUNG RR KIRIM (PERSEN)
                $targetPenugasan = (int) $penugasan->target;
                $jumlahDikirim = (int) $validated['jumlah_dikirim'];
                $rrKirim = $targetPenugasan > 0
                    ? round(($jumlahDikirim / $targetPenugasan) * 100, 2)
                    : 0;

                // Batasi maksimal 100%
                $rrKirim = min($rrKirim, 100);


                // 2️⃣ HITUNG RATING KIRIM (BINTANG 1–5)
                $tanggalPengiriman = Carbon::parse($validated['tanggal_pengiriman'])->startOfDay();

                // Deadline = TANGGAL selesai (bukan endOfDay)
                $tanggalDeadline = Carbon::parse($penugasan->tanggal_selesai)->startOfDay();
                $deadlineEfektif = $this->hasDeadlineGracePeriod($penugasan)
                    ? $tanggalDeadline->copy()->addDay()
                    : $tanggalDeadline;

                // Hitung hari keterlambatan
                if ($tanggalPengiriman->lte($deadlineEfektif)) {
                    $hariTelat = 0;
                } else {
                    $hariTelat = (int) $deadlineEfektif->diffInDays($tanggalPengiriman);
                    // dd($hariTelat);
                }

                // Mapping hari telat → rating
                $ratingKirim = match (true) {
                    $hariTelat === 0 => 5,
                    $hariTelat === 1 => 4,
                    $hariTelat === 2 => 3,
                    $hariTelat === 3 => 2,
                    default          => 1,
                };

                // 3️⃣ SIMPAN DATA PENGIRIMAN
                $penugasan->pengirimans()->create([
                    'tanggal_pengiriman' => $validated['tanggal_pengiriman'],
                    'bulan_pengiriman'   => $validated['bulan_pengiriman'],
                    'tipe_pengiriman'    => $validated['tipe_pengiriman'],
                    'jumlah_dikirim'     => $jumlahDikirim,
                    'media_pengiriman'   => $validated['media_pengiriman'],
                    'bukti_dukung'       => $validated['bukti_dukung'],
                    'rr_kirim'           => $rrKirim,
                    'rating_kirim'       => $ratingKirim,
                    'catatan'            => $validated['catatan'],
                ]);

                // 4️⃣ UPDATE STATUS PENUGASAN
                $penugasan->update([
                    'status' => 'Sudah Dikirim',
                ]);
            });

            app(PushNotificationService::class)->notifyPengirimanSubmitted($penugasan->fresh());

            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan'    => $penugasan->subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $penugasan->subKegiatan->id_sub_kegiatan,
                ])
                ->with('success', 'Pengiriman hasil kerja berhasil dilakukan.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Store pengiriman error: ' . $e->getMessage());
            return back()
                ->with('error', 'Gagal mengirimkan hasil kerja. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function delete(Request $request, Pengiriman $pengiriman)
    {
        $penugasan = $pengiriman->penugasan;
        $this->authorize('cancelSend', $penugasan);

        // Pastikan pengiriman ini bukan pengiriman yang sudah memiliki penerimaan
        if ($pengiriman->penerimaan) {
            return redirect()->back()->with('error', 'Data pengiriman tidak bisa dibatalkan karena sudah ditanggapi oleh Ketua Tim.');
        }

        try {
            DB::transaction(function () use ($penugasan, $pengiriman) {
                $pengiriman->forceDelete();

                // Cek jika tidak ada pengiriman lagi, kembalikan status penugasan
                $remainingCount = $penugasan->pengirimans()->count();
                if ($remainingCount === 0) {
                    $penugasan->update(['status' => 'Belum Dikirim']);
                }
            });

            app(PushNotificationService::class)->notifyPengirimanCancelled($penugasan->fresh());

            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan'    => $penugasan->subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $penugasan->subKegiatan->id_sub_kegiatan,
                ])
                ->with('success', 'Pengiriman kerja berhasil dibatalkan.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Cancel pengiriman error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Gagal membatalkan pengiriman. Silakan coba lagi.');
        }
    }

    private function hasDeadlineGracePeriod(Penugasan $penugasan): bool
    {
        $jenisKegiatan = strtolower($penugasan->jenisKegiatan?->jenis_kegiatan ?? '');

        return in_array($jenisKegiatan, [
            'pengawasan',
            'supervisi',
            'perjalanan dinas',
        ], true);
    }

}
