<?php

namespace App\Http\Controllers;

use App\Models\Penugasan;
use App\Models\Pegawai;
use App\Models\SubKegiatan;
use App\Models\JenisKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PenugasanController extends Controller
{
    public function store(Request $request, SubKegiatan $subKegiatan)
    {
        // dd($request->all());
        $this->authorize('create', [Penugasan::class, $subKegiatan]);

        $validated = $request->validate([
            'id_anggota' => ['required', 'exists:pegawais,id_pegawai'],
            'id_jenis_kegiatan' => ['required'],
            'jenis_kegiatan_baru' => ['nullable', 'string'],
            'target' => ['required', 'integer', 'min:1'],
            'satuan_target' => ['required', 'string', 'max:50'],

            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'butuh_dl' => ['nullable', 'boolean'],
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

        /**
         * 🔐 SERVER-SIDE DL VALIDATION
         * (jangan percaya UI sepenuhnya)
         */
        $jenisKegiatan = JenisKegiatan::find($validated['id_jenis_kegiatan']);

        $wajibDl = in_array($jenisKegiatan->jenis_kegiatan, [
            'Perjalanan Dinas',
            'Supervisi',
            'Pengawasan',
            'Pendataan',
        ]);

        // Ambil input toggle DL (0 / 1)
        $requestButuhDl = (bool) ($validated['butuh_dl'] ?? false);

        $butuhDl = $wajibDl || $requestButuhDl;

        $validated['butuh_dl'] = $butuhDl;
        $validated['status_dl'] = $butuhDl ? 'Menunggu' : null;


        // STATUS AWAL PENUGASAN
        $validated['status'] = 'Belum Dikirim';

        try {
            /**
             * ✅ BUAT PENUGASAN
             */
            $subKegiatan->penugasans()->create($validated);

            /**
             * 🔄 SET ACTIVE ROLE PEGAWAI → ANGGOTA TIM (KONTEKSTUAL)
             */
            $pegawai = Pegawai::find($validated['id_anggota']);

            if ($pegawai && $pegawai->active_role !== 'Anggota Tim') {
                $pegawai->update([
                    'active_role' => 'Anggota Tim'
                ]);
            }

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
            'butuh_dl' => ['nullable', 'boolean'],
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
        }

        unset($validated['jenis_kegiatan_baru']);

        /**
         * 🔐 SERVER-SIDE DL VALIDATION
         */
        $jenisKegiatan = JenisKegiatan::findOrFail($validated['id_jenis_kegiatan']);

        $wajibDl = in_array($jenisKegiatan->jenis_kegiatan, [
            'Perjalanan Dinas',
            'Supervisi',
            'Pengawasan',
            'Pendataan',
        ]);

        $requestButuhDl = (bool) ($validated['butuh_dl'] ?? false);

        // FINAL DECISION
        $butuhDl = $wajibDl || $requestButuhDl;

        $validated['butuh_dl'] = $butuhDl;

        if ($butuhDl) {
            if ($penugasan->status_dl === null) {
                $validated['status_dl'] = 'Menunggu';
            }
        } else {
            $validated['status_dl'] = null;
        }

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

    public function update_rk_dl(Request $request, Penugasan $penugasan)
    {
        // dd($request->all());
        $validated = $request->validate([
            'status_dl' => ['required', 'in:Menunggu,ACC,Ditolak'],
        ]);

        // Cek role aktif
        $role = $request->user()->active_role;

        if ($role === 'Pimpinan' && !in_array($request->status_dl, ['ACC', 'Ditolak'])) {
            return redirect()->back()->with('error', 'Pimpinan hanya boleh menyetujui atau menolak.');
        }

        if ($role === 'Ketua Tim' && $request->status_dl !== 'Menunggu') {
            return redirect()->back()->with('error', 'Ketua Tim hanya boleh mengajukan kembali.');
        }

        try {
            $penugasan->update([
                'status_dl' => $validated['status_dl'],
            ]);

            return redirect()->back()
            ->with('success', 'Status Dinas Luar berhasil diperbarui.');
        } catch (\Exception $e) {
            dd($e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memperbarui data status Dinas Luar. Silakan coba lagi.')
                ->withInput();
        }
    }
}
