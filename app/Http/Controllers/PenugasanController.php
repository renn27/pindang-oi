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
            'butuh_translok' => ['nullable', 'boolean'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],

            'tanggal_mulai_list' => ['nullable', 'array'],
            'tanggal_mulai_list.*' => ['nullable', 'date'],
            'tanggal_selesai_list' => ['nullable', 'array'],
            'tanggal_selesai_list.*' => ['nullable', 'date'],
        ]);

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

        $jenisKegiatan = JenisKegiatan::find($validated['id_jenis_kegiatan']);
        $wajibDl = $jenisKegiatan->butuh_dl_atau_translok == 1;

        $requestButuhDl = (bool) ($validated['butuh_dl'] ?? false);
        $requestButuhTranslok = (bool) ($validated['butuh_translok'] ?? false);

        $butuhDl = false;
        $butuhTranslok = false;

        if ($wajibDl) {
            if ($requestButuhDl) {
                $butuhDl = true;
            } elseif ($requestButuhTranslok) {
                $butuhTranslok = true;
            } else {
                $butuhDl = true; // default ke DL (sesuai UI kamu)
            }
        } else {
            $butuhDl = false;
            $butuhTranslok = false;
        }

        $validated['butuh_dl'] = $butuhDl;
        $validated['status_dl'] = $butuhDl ? 'Menunggu' : null;

        $validated['butuh_translok'] = $butuhTranslok;
        $validated['status_translok'] = $butuhTranslok ? 'Menunggu' : null;

        $validated['status'] = 'Belum Dikirim';

        $validDatesToSave = $this->extractAndValidateDates(
            $request,
            $validated,
            $subKegiatan->tanggal_mulai,
            $subKegiatan->tanggal_selesai
        );

        DB::beginTransaction();

        try {
            foreach ($validDatesToSave as $tgl) {
                $validated['tanggal_mulai'] = $tgl['mulai'];
                $validated['tanggal_selesai'] = $tgl['selesai'];

                // Create terpisah untuk masing-masing tanggal sesuai request
                $penugasan = $subKegiatan->penugasans()->create($validated);
            }

            DB::commit();

            $isSelfAssign = $validated['id_anggota'] == auth()->user()?->id_pegawai;
            
            $response = redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan' => $subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $subKegiatan->id_sub_kegiatan
                ])
                ->with('success', 'Penugasan kepada anggota berhasil dilakukan.');
                
            if ($isSelfAssign) {
                $response->with('info', 'Anda menambahkan diri anda sendiri di penugasan sub kegiatan ini.');
            }
            
            return $response;
        } catch (\Exception $e) {
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
            'butuh_translok' => ['nullable', 'boolean'],

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

        $wajibDl = $jenisKegiatan->butuh_dl_atau_translok == 1;

        $requestButuhDl = (bool) ($validated['butuh_dl'] ?? false);
        $requestButuhTranslok = (bool) ($validated['butuh_translok'] ?? false);

        // DEFAULT
        $butuhDl = false;
        $butuhTranslok = false;

        if ($wajibDl) {
            // hanya boleh pilih salah satu
            if ($requestButuhDl) {
                $butuhDl = true;
            } elseif ($requestButuhTranslok) {
                $butuhTranslok = true;
            } else {
                // fallback kalau kosong (biar aman)
                $butuhDl = true;
            }
        } else {
            // selain 4 jenis → tidak boleh keduanya
            $butuhDl = false;
            $butuhTranslok = false;
        }

        $validated['butuh_dl'] = $butuhDl;
        $validated['butuh_translok'] = $butuhTranslok;

        // HANDLE STATUS DL
        $validated['status_dl'] = $validated['butuh_dl']
            ? ($penugasan->status_dl ?? 'Menunggu')
            : null;

        // HANDLE STATUS TRANSLOK
        $validated['status_translok'] = $validated['butuh_translok']
            ? ($penugasan->status_translok ?? 'Menunggu')
            : null;

        $updateData = $validated;
        unset($updateData['tanggal_mulai_list']);
        unset($updateData['tanggal_selesai_list']);

        DB::beginTransaction();
        try {
            // Update data parent (utama)
            $penugasan->update($updateData);

            // Jika ada tanggal tambahan, buat row baru
            $validDatesToSave = $this->extractAndValidateDates(
                $request,
                $validated,
                $subKegiatan->tanggal_mulai,
                $subKegiatan->tanggal_selesai,
                true // skip main date extraction because updateData already handles main date update
            );

            foreach ($validDatesToSave as $tgl) {
                if (!empty($tgl['mulai']) || !empty($tgl['selesai'])) {
                    Penugasan::create([
                        'id_anggota' => $validated['id_anggota'],
                        'id_sub_kegiatan' => $penugasan->id_sub_kegiatan,
                        'id_jenis_kegiatan' => $validated['id_jenis_kegiatan'],
                        'target' => $validated['target'],
                        'satuan_target' => $validated['satuan_target'],
                        'tanggal_mulai' => $tgl['mulai'],
                        'tanggal_selesai' => $tgl['selesai'],
                        'status' => $penugasan->status,
                        'status_dl' => $validated['status_dl'] ?? null,
                        'butuh_dl' => $validated['butuh_dl'],
                        'butuh_translok' => $validated['butuh_translok'],
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
        $this->authorize('delete', $penugasan);

        try {
            $penugasan->forceDelete();

            return redirect()->back()
                ->with('success', 'Penugasan Anggota berhasil dihapus');
        } catch (\Exception $e) {
            dd($e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal menghapus Penugasan Anggota. Silakan coba lagi.');
        }
    }

    public function update_rk_dl(Request $request, Penugasan $penugasan)
    {
        $this->authorize('acceptDL', $penugasan);
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

    public function update_rk_translok(Request $request, Penugasan $penugasan)
    {
        $this->authorize('acceptTranslok', $penugasan);
        $validated = $request->validate([
            'status_translok' => ['required', 'in:Menunggu,ACC,Ditolak'],
        ]);

        // Cek role aktif
        $role = $request->user()->active_role;

        if ($role === 'Pimpinan' && !in_array($request->status_translok, ['ACC', 'Ditolak'])) {
            return redirect()->back()->with('error', 'Pimpinan hanya boleh menyetujui atau menolak.');
        }

        if ($role === 'Ketua Tim' && $request->status_translok !== 'Menunggu') {
            return redirect()->back()->with('error', 'Ketua Tim hanya boleh mengajukan kembali.');
        }

        try {
            $penugasan->update([
                'status_translok' => $validated['status_translok'],
            ]);

            return redirect()->back()
                ->with('success', 'Status Translok berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memperbarui data status Translok. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function checkDuplicateDates(Request $request)
    {
        $request->validate([
            'id_anggota' => 'required',
            'dates' => 'required|array',
            'exclude_id' => 'nullable'
        ]);

        $idAnggota = $request->id_anggota;
        $excludeId = $request->exclude_id;
        $dates = $request->dates;

        // Ambil nama anggota sekali saja
        $anggota = \App\Models\Pegawai::where('id_pegawai', $idAnggota)->first();
        $namaAnggota = $anggota ? $anggota->nama_pegawai : 'anggota terpilih';

        $duplicates = [];

        foreach ($dates as $index => $date) {
            $mulai = $date['tanggal_mulai'] ?? null;
            $selesai = $date['tanggal_selesai'] ?? null;

            if (!$mulai || !$selesai) continue;

            $query = Penugasan::where('id_anggota', $idAnggota)
                ->where(function($q) {
                    $q->where('butuh_dl', 1)
                    ->orWhere('butuh_translok', 1);
                })
                ->where('tanggal_mulai', '<=', $selesai)
                ->where('tanggal_selesai', '>=', $mulai);

            if (!empty($excludeId)) {
                $query->where('id_penugasan', '!=', $excludeId);
            }

            $conflict = $query->first(['tanggal_mulai', 'tanggal_selesai']);

            if ($conflict) {
                $cMulai = $conflict->tanggal_mulai;
                $cSelesai = $conflict->tanggal_selesai;

                $reqM = \Carbon\Carbon::parse($mulai)->startOfDay();
                $reqS = \Carbon\Carbon::parse($selesai)->startOfDay();
                $dbM = \Carbon\Carbon::parse($cMulai)->startOfDay();
                $dbS = \Carbon\Carbon::parse($cSelesai)->startOfDay();

                $dbMulaiStr = $dbM->translatedFormat('d M Y');
                $dbSelesaiStr = $dbS->translatedFormat('d M Y');

                $isMulaiHit = $reqM->greaterThanOrEqualTo($dbM) && $reqM->lessThanOrEqualTo($dbS);
                $isSelesaiHit = $reqS->greaterThanOrEqualTo($dbM) && $reqS->lessThanOrEqualTo($dbS);

                // Penentuan elemen form mana yang akan difokuskan
                if (!$isMulaiHit && $isSelesaiHit) {
                    $focusEl = ($index === 0) ? 'tanggal_selesai' : "tanggal_selesai_$index";
                } else {
                    $focusEl = ($index === 0) ? 'tanggal_mulai' : "tanggal_mulai_$index";
                }

                // Penentuan string pesan error (berdasarkan DB range 1 hari atau rentang hari)
                if ($dbMulaiStr === $dbSelesaiStr) {
                    if ($isMulaiHit && !$isSelesaiHit) {
                        $msg = "Tanggal mulai {$dbMulaiStr} untuk {$namaAnggota} sudah ada di penugasan lain.";
                    } elseif ($isSelesaiHit && !$isMulaiHit) {
                        $msg = "Tanggal selesai {$dbSelesaiStr} untuk {$namaAnggota} sudah ada di penugasan lain.";
                    } else {
                        $msg = "Tanggal {$dbMulaiStr} untuk {$namaAnggota} sudah ada di penugasan lain.";
                    }
                } else {
                    $msg = "Tanggal mulai {$dbMulaiStr} sampai dengan Tanggal selesai {$dbSelesaiStr} untuk {$namaAnggota} sudah ada di penugasan lain.";
                }

                $duplicates[] = [
                    'message' => $msg,
                    'focus_el' => $focusEl,
                    'index' => $index,
                    'requested_mulai' => $mulai,
                    'requested_selesai' => $selesai,
                    'is_selesai' => (!$isMulaiHit && $isSelesaiHit) // flag to determine which side of the date triggered it
                ];
            }
        }

        return response()->json([
            'has_duplicate' => count($duplicates) > 0,
            'duplicates' => $duplicates
        ]);
    }

    /**
     * Helper to extract flat dates array from main and additional dates,
     * and validate them against SubKegiatan bounds.
     */
    private function extractAndValidateDates(Request $request, array $validated, $subKegiatanMulai, $subKegiatanSelesai, $skipMain = false): array
    {
        $allDates = [];

        if (!$skipMain) {
            $allDates[] = [
                'mulai' => $validated['tanggal_mulai'] ?? null,
                'selesai' => $validated['tanggal_selesai'] ?? null
            ];
        }

        $mulaiList = $request->tanggal_mulai_list ?? [];
        $selesaiList = $request->tanggal_selesai_list ?? [];
        foreach ($mulaiList as $index => $tglMulai) {
            $tglSelesai = $selesaiList[$index] ?? null;
            if ($tglMulai && $tglSelesai) {
                $allDates[] = ['mulai' => $tglMulai, 'selesai' => $tglSelesai];
            }
        }

        $min = $subKegiatanMulai ? \Carbon\Carbon::parse((string) $subKegiatanMulai)->startOfDay() : null;
        $max = $subKegiatanSelesai ? \Carbon\Carbon::parse((string) $subKegiatanSelesai)->startOfDay() : null;

        $validDatesToSave = [];
        $existing = [];

        foreach ($allDates as $tgl) {
            $m = $tgl['mulai'];
            $s = $tgl['selesai'];

            if (empty($m) && empty($s)) {
                $validDatesToSave[] = ['mulai' => null, 'selesai' => null];
                continue;
            }

            // Hindari duplikat
            $key = $m . '|' . $s;
            if (in_array($key, $existing))
                continue;
            $existing[] = $key;

            $cm = \Carbon\Carbon::parse($m)->startOfDay();
            $cs = \Carbon\Carbon::parse($s)->startOfDay();

            if ($cs->lt($cm) || ($min && $cm->lt($min)) || ($max && $cs->gt($max))) {
                throw ValidationException::withMessages([
                    'tanggal_mulai' => 'Tanggal penugasan tidak valid atau di luar rentang sub kegiatan.'
                ]);
            }

            $validDatesToSave[] = ['mulai' => $m, 'selesai' => $s];
        }

        return $validDatesToSave;
    }
}
