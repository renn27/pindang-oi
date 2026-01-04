<?php

namespace App\Http\Controllers;

use App\Models\Penugasan;
use App\Models\SubKegiatan;
use Illuminate\Http\Request;

class PenugasanController extends Controller
{
    public function store(Request $request, SubKegiatan $subKegiatan) {
        // dd($request->all());

        $validated = $request->validate([
            'id_anggota' => ['required', 'exists:pegawais,id_pegawai',],
            'target' => ['required', 'integer', 'min:1'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required',
                                    'date',
                                    'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:Belum Dikirim,Sudah Dikirim,Masih Revisi,Sudah Diterima'],
        ]);

        $validated['tanggal_mulai'] = now()->format('Y-m-d');

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

    public function update(Request $request, SubKegiatan $subKegiatan, Penugasan $penugasan) {
        // dd($request->all());

        $validated = $request->validate([
            'id_anggota' => ['required', 'exists:pegawais,id_pegawai',],
            'target' => ['required', 'integer', 'min:1'],
            'tanggal_mulai' => ['required', 'date', 'before_or_equal:today'],
            'tanggal_selesai' => ['required',
                                    'date',
                                    'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:Belum Dikirim,Sudah Dikirim,Masih Revisi,Sudah Diterima'],
        ]);

        try {
            $penugasan->update($validated);
            // Redirect dengan flash message
            return redirect()
                    ->route('sub.kegiatan.show', [
                    'kegiatan' => $subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $subKegiatan->id_sub_kegiatan
                ])
                ->with('success', 'Data Penugasan kepada anggota berhasil diperbarui.');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data penugasan kepada anggota. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function delete() {}
}
