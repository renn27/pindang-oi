<?php

namespace App\Http\Controllers;

use App\Models\Penugasan;
use App\Models\CkpPegawai;
use Illuminate\Http\Request;

class CkpPegawaiController extends Controller
{
    public function storeFromPenugasan(Request $request, $id)
    {
        // Validasi input dari modal
        $request->validate([
            'uraian' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $penugasan = Penugasan::with(['jenisKegiatan', 'subKegiatan', 'ckp'])
            ->findOrFail($id);

        // CEK SUDAH ADA
        if ($penugasan->ckp) {
            return back()->with('warning', 'Penugasan ini sudah masuk CKP');
        }

        // SIMPAN CKP sesuai kolom yang ada
        CkpPegawai::create([
            'id_pegawai'        => $penugasan->id_anggota,
            'id_penugasan'      => $penugasan->id_penugasan,
            'uraian'            => $request->uraian,
            'jenis_ckp'         => 'utama',
            'satuan'            => $penugasan->satuan_target,
            'target_kuantitas'  => $penugasan->target,
            'kode_butir_kegiatan' => null,
            'angka_kredit'      => null,
            'keterangan'        => $request->keterangan,
        ]);

        return back()->with('success', 'Berhasil dijadikan CKP');
    }
}