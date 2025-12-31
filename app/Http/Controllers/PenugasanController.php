<?php

namespace App\Http\Controllers;

use App\Models\SubKegiatan;
use Illuminate\Http\Request;

class PenugasanController extends Controller
{
    public function store(Request $request, SubKegiatan $subKegiatan) {
        // dd($request->all());

        $validated = $request->validate([
            'id_anggota' => ['required', 'exists:pegawais,id_pegawai',],
            'target' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['required',
                                    'date',
                                    'date_format:Y-m-d',
                                    'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:Belum Dikirim,Sudah Dikirim,Masih Revisi,Sudah Diterima'],
        ]);

        try {
            // Simpan
            $subKegiatan->penugasans()->create($validated);

            // Redirect dengan flash message
            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan' => $subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $subKegiatan->id_sub_kegiatan
                ])
                ->with('success', 'Penugasan kepada anggota berhasil dilakukan.');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal membuat penugasan kepada anggota. Silakan coba lagi.')
                ->withInput();
        }
    }
    public function update() {}
    public function delete() {}
}
