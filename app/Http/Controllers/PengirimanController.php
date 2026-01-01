<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penugasan;
use App\Models\SubKegiatan;

class PengirimanController extends Controller
{
    public function store(Request $request, SubKegiatan $subKegiatan, Penugasan $penugasan) {
        // dd($request->all());

        $validated = $request->validate([
            'tanggal_pengiriman' => ['required', 'date', 'date_format:Y-m-d'],
            'jumlah_dikirim' => ['required', 'integer', 'max:255'],
            'media_pengiriman' => ['required', 'string', 'max:255'],
            'bukti_dukung' => ['required', 'string', 'max:255'],
        ]);

        try {
            // Simpan
            $penugasan->pengirimans()->create($validated);

            // Redirect dengan flash message
            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan' => $penugasan->subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $penugasan->subKegiatan->id_sub_kegiatan
                ])
                ->with('success', 'Pengiriman hasil kerja berhasil dilakukan.');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal mengirimkan hasil kerja. Silakan coba lagi.')
                ->withInput();
        }
    }
}
