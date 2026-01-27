<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penugasan;
use App\Models\SubKegiatan;
use App\Models\Pengiriman;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    public function store(Request $request, SubKegiatan $subKegiatan, Penugasan $penugasan, Pengiriman $pengirimans) {
        // dd($request->all());

        $this->authorize('receive', $penugasan);

        $validated = $request->validate([
            'tanggal_penerimaan' => ['required', 'date'],
            'jumlah_diterima' => ['required', 'integer', 'max:255'],
            'status' => ['required', 'in:Sedang Diperiksa,Diterima,Revisi'],
            'catatan' => ['required', 'string', 'max:255'],
        ]);

        try {
            $pengirimans->penerimaan()->create($validated);

            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan'     => $penugasan->subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan'  => $subKegiatan->id_sub_kegiatan,
                ])
                ->with('success', 'Penerimaan hasil kerja berhasil dilakukan.');

        } catch (\Exception $e) {
            dd($e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal melakukan penerimaan hasil kerja. Silakan coba lagi.')
                ->withInput();
        }
    }
}
