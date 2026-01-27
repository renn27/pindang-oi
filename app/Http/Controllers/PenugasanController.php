<?php

namespace App\Http\Controllers;

use App\Models\Penugasan;
use App\Models\SubKegiatan;
use App\Models\JenisKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PenugasanController extends Controller
{
    public function store(Request $request, SubKegiatan $subKegiatan)
    {
        $this->authorize('create', [Penugasan::class, $subKegiatan]);

        $validated = $request->validate([
            'id_anggota' => ['required', 'exists:pegawais,id_pegawai'],
            'id_jenis_kegiatan' => ['required'],
            'jenis_kegiatan_baru' => ['nullable', 'string', 'max:100'],
            'target' => ['required', 'integer', 'min:1'],
            'satuan_target' => ['required', 'string', 'max:50'],

            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        /**
         * 🔥 HANDLE JENIS KEGIATAN (LAINNYA / EXISTING)
         */
        if ($validated['id_jenis_kegiatan'] === 'LAINNYA') {
            if (empty($validated['jenis_kegiatan_baru'])) {
                return back()->withErrors([
                    'jenis_kegiatan_baru' => 'Jenis kegiatan wajib diisi'
                ])->withInput();
            }

            $jenis = JenisKegiatan::create([
                'jenis_kegiatan' => $validated['jenis_kegiatan_baru'],
                'kategori' => 'Tambahan',
            ]);

            $validated['id_jenis_kegiatan'] = $jenis->id;
        } else {
            if (!JenisKegiatan::where('id', $validated['id_jenis_kegiatan'])->exists()) {
                abort(422, 'Jenis kegiatan tidak valid');
            }
        }

        unset($validated['jenis_kegiatan_baru']);

        // STATUS AWAL
        $validated['status'] = 'Belum Dikirim';

        try {
            $subKegiatan->penugasans()->create($validated);

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

    public function update(Request $request, SubKegiatan $subKegiatan, Penugasan $penugasan)
    {
        // dd($request->all());
        $this->authorize('update', $penugasan);

        $validated = $request->validate([
            'id_anggota' => ['required', 'exists:pegawais,id_pegawai'],
            'id_jenis_kegiatan' => ['required'],
            'jenis_kegiatan_baru' => ['nullable', 'string', 'max:100'],
            'target' => ['required', 'integer', 'min:1'],
            'satuan_target' => ['required', 'string', 'max:50'],

            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        /**
         * 🔥 DETEKSI JENIS KEGIATAN
         */
        $jenisKegiatan = JenisKegiatan::find($validated['id_jenis_kegiatan']);

        try {
            $penugasan->update($validated);

            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan' => $subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $subKegiatan->id_sub_kegiatan
                ])
                ->with('success', 'Data Penugasan kepada anggota berhasil diperbarui.');
        } catch (\Exception $e) {
            dd($e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memperbarui data penugasan kepada anggota. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function delete() {}
}
