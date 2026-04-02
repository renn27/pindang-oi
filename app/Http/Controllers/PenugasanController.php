<?php

namespace App\Http\Controllers;

use App\Models\JenisKegiatan;
use App\Models\Pegawai;
use App\Models\Penugasan;
use App\Models\SubKegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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

            'butuh_dl' => ['nullable', 'boolean'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],

            // tanggal tambahan (OPSIONAL)
            'tanggal_mulai_list' => ['nullable', 'array'],
            'tanggal_mulai_list.*' => ['nullable', 'date'],
            'tanggal_selesai_list' => ['nullable', 'array'],
            'tanggal_selesai_list.*' => ['nullable', 'date'],
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

        // VALIDASI RANGE SUB KEGIATAN
        $min = $subKegiatan->tanggal_mulai;
        $max = $subKegiatan->tanggal_selesai;

        // Gabungkan tanggal utama dengan tanggal tambahan (jika ada)
        $mulaiList = $request->tanggal_mulai_list ?? [];
        $selesaiList = $request->tanggal_selesai_list ?? [];
        
        $allDates = [];
        
        // Masukkan tanggal utama
        $allDates[] = [
            'mulai' => $validated['tanggal_mulai'],
            'selesai' => $validated['tanggal_selesai']
        ];

        // Masukkan tanggal tambahan
        foreach ($mulaiList as $index => $tglMulai) {
            $tglSelesai = $selesaiList[$index] ?? null;
            if ($tglMulai && $tglSelesai) {
                $allDates[] = [
                    'mulai' => $tglMulai,
                    'selesai' => $tglSelesai
                ];
            }
        }

        $validDatesToSave = [];
        $existingDates = [];

        foreach ($allDates as $tgl) {
            $tglMulai = $tgl['mulai'];
            $tglSelesai = $tgl['selesai'];
            
            // hindari duplikasi tanggal yang persis sama
            $dateKey = $tglMulai . '|' . $tglSelesai;
            if (in_array($dateKey, $existingDates)) continue;
            $existingDates[] = $dateKey;

            // validasi pasangan
            if ($tglSelesai < $tglMulai) {
                throw ValidationException::withMessages([
                    'tanggal_selesai' => 'Tanggal selesai tidak boleh sebelum tanggal mulai'
                ]);
            }

            // validasi range sub kegiatan
            if ($tglMulai < $min || $tglSelesai > $max) {
                throw ValidationException::withMessages([
                    'tanggal_mulai' => 'Tanggal penugasan di luar rentang sub kegiatan'
                ]);
            }

            $validDatesToSave[] = [
                'mulai' => $tglMulai,
                'selesai' => $tglSelesai
            ];
        }

        DB::beginTransaction();
        try {
            foreach ($validDatesToSave as $tgl) {
                // Set array validasi dengan tanggal masing-masing iterasi
                $validated['tanggal_mulai'] = $tgl['mulai'];
                $validated['tanggal_selesai'] = $tgl['selesai'];

                // Create terpisah untuk masing-masing tanggal sesuai request
                $penugasan = $subKegiatan->penugasans()->create($validated);
            }

            DB::commit();

            // /**
            //  * 🔄 SET ACTIVE ROLE PEGAWAI → ANGGOTA TIM (KONTEKSTUAL)
            //  */
            // $pegawai = Pegawai::find($validated['id_anggota']);

            // if ($pegawai && $pegawai->active_role !== 'Anggota Tim') {
            //     $pegawai->update([
            //         'active_role' => 'Anggota Tim'
            //     ]);
            // }

            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan' => $subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $subKegiatan->id_sub_kegiatan
                ])
                ->with('success', 'Penugasan kepada anggota berhasil dilakukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat penugasan: ' . $e->getMessage());
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

            // tanggal tambahan (OPSIONAL)
            'tanggal_mulai_list' => ['nullable', 'array'],
            'tanggal_mulai_list.*' => ['nullable', 'date'],
            'tanggal_selesai_list' => ['nullable', 'array'],
            'tanggal_selesai_list.*' => ['nullable', 'date'],
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

        $updateData = $validated;
        unset($updateData['tanggal_mulai_list']);
        unset($updateData['tanggal_selesai_list']);

        DB::beginTransaction();
        try {
            // Update data parent (utama)
            $penugasan->update($updateData);

            // Jika ada tanggal tambahan, buat row baru
            $mulaiList = $request->tanggal_mulai_list ?? [];
            $selesaiList = $request->tanggal_selesai_list ?? [];

            foreach ($mulaiList as $index => $tglMulai) {
                $tglSelesai = $selesaiList[$index] ?? null;
                if ($tglMulai && $tglSelesai) {
                    
                    // Validasi range sub kegiatan
                    $min = $subKegiatan->tanggal_mulai;
                    $max = $subKegiatan->tanggal_selesai;

                    if ($tglMulai < $min || $tglSelesai > $max) {
                        throw ValidationException::withMessages([
                            'tanggal_mulai_list.' . $index => 'Tanggal penugasan di luar rentang sub kegiatan'
                        ]);
                    }

                    if ($tglSelesai < $tglMulai) {
                        throw ValidationException::withMessages([
                            'tanggal_selesai_list.' . $index => 'Tanggal selesai tidak boleh sebelum tanggal mulai'
                        ]);
                    }

                    Penugasan::create([
                        'id_anggota' => $validated['id_anggota'],
                        'id_sub_kegiatan' => $penugasan->id_sub_kegiatan,
                        'id_jenis_kegiatan' => $validated['id_jenis_kegiatan'],
                        'target' => $validated['target'],
                        'satuan_target' => $validated['satuan_target'],
                        'tanggal_mulai' => $tglMulai,
                        'tanggal_selesai' => $tglSelesai,
                        'status' => $penugasan->status,
                        'status_dl' => $validated['status_dl'] ?? null,
                        'butuh_dl' => $validated['butuh_dl'],
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan' => $subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $subKegiatan->id_sub_kegiatan
                ])
                ->with('success', 'Data Penugasan kepada anggota berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memperbarui data penugasan kepada anggota. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function delete(SubKegiatan $subKegiatan, Penugasan $penugasan)
    {
        // 🔥 samakan dengan tombol penerimaan (policy)
        $this->authorize('delete', $penugasan);

        // 🔒 safety check (opsional tapi bagus)
        if ($penugasan->id_sub_kegiatan !== $subKegiatan->id_sub_kegiatan) {
            abort(403);
        }

        $penugasan->delete();

        return redirect()->back()
            ->with('success', 'Anggota berhasil dihapus');
    }

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
            Log::error('Update error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memperbarui data status Dinas Luar. Silakan coba lagi.')
                ->withInput();
        }
    }
}
