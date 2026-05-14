<?php

namespace App\Http\Controllers;

use App\Models\JenisKegiatan;
use App\Models\Pegawai;
use App\Models\Penugasan;
use App\Models\SubKegiatan;
use App\Models\KalenderDL;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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

            // butuh_dl dan butuh_translok harus 0 atau 1 (boolean)
            'butuh_dl' => ['nullable', 'boolean'],
            'butuh_translok' => ['nullable', 'boolean'],

            // Tanggal WAJIB ada saat create
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
                $butuhDl = true;
            }
        } else {
            $butuhDl = false;
            $butuhTranslok = false;
        }

        // Pastikan nilai butuh_dl/translok selalu integer 0 atau 1 (tinyint safe)
        $validated['butuh_dl'] = $butuhDl ? 1 : 0;
        $validated['status_dl'] = $butuhDl ? 'Menunggu' : null;

        $validated['butuh_translok'] = $butuhTranslok ? 1 : 0;
        $validated['status_translok'] = $butuhTranslok ? 'Menunggu' : null;

        $validated['status'] = 'Belum Dikirim';

        $validDatesToSave = $this->extractAndValidateDates(
            $request,
            $validated,
            $subKegiatan->tanggal_mulai,
            $subKegiatan->tanggal_selesai
        );

        try {
            DB::transaction(function () use ($subKegiatan, $validated, $validDatesToSave) {
                foreach ($validDatesToSave as $tgl) {
                    $validated['tanggal_mulai'] = $tgl['mulai'];
                    $validated['tanggal_selesai'] = $tgl['selesai'];

                    // Create terpisah untuk masing-masing tanggal sesuai request
                    $subKegiatan->penugasans()->create($validated);
                }
            });

            $isSelfAssign = $validated['id_anggota'] == auth()->user()?->id_pegawai;

            $response = redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan' => $subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $subKegiatan->id_sub_kegiatan,
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

            // Tanggal WAJIB ada saat update juga (penugasan harus punya rentang waktu)
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],

            // butuh_dl dan butuh_translok harus 0 atau 1 (boolean)
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

        // Pastikan nilai butuh_dl/translok selalu integer 0 atau 1 (tinyint safe)
        $validated['butuh_dl'] = $butuhDl ? 1 : 0;
        $validated['butuh_translok'] = $butuhTranslok ? 1 : 0;

        // HANDLE STATUS DL — preserve nilai yang sudah ada; jika baru aktif → 'Menunggu'; jika dimatikan → null
        $validated['status_dl'] = $validated['butuh_dl']
            ? (in_array($penugasan->status_dl, ['Menunggu', 'ACC', 'Ditolak']) ? $penugasan->status_dl : 'Menunggu')
            : null;

        // HANDLE STATUS TRANSLOK — idem
        $validated['status_translok'] = $validated['butuh_translok']
            ? (in_array($penugasan->status_translok, ['Menunggu', 'ACC', 'Ditolak']) ? $penugasan->status_translok : 'Menunggu')
            : null;

        $updateData = $validated;
        unset($updateData['tanggal_mulai_list']);
        unset($updateData['tanggal_selesai_list']);

        // Hitung tanggal tambahan di LUAR transaction — agar ValidationException ditangani Laravel secara alami
        $validDatesToSave = $this->extractAndValidateDates(
            $request,
            $validated,
            $subKegiatan->tanggal_mulai,
            $subKegiatan->tanggal_selesai,
            true, // skip main date — updateData sudah menangani tanggal utama
            $penugasan->id_penugasan
        );

        try {
            DB::transaction(function () use ($penugasan, $updateData, $validated, $validDatesToSave) {
                // Update data parent (utama)
                $penugasan->update($updateData);

                // Jika ada tanggal tambahan, buat row baru
                foreach ($validDatesToSave as $tgl) {
                    // Keduanya harus ada (&&) — mencegah penugasan dengan tanggal null
                    if (!empty($tgl['mulai']) && !empty($tgl['selesai'])) {
                        Penugasan::create([
                            'id_anggota' => $validated['id_anggota'],
                            'id_sub_kegiatan' => $penugasan->id_sub_kegiatan,
                            'id_jenis_kegiatan' => $validated['id_jenis_kegiatan'],
                            'target' => $validated['target'],
                            'satuan_target' => $validated['satuan_target'],
                            'tanggal_mulai' => $tgl['mulai'],
                            'tanggal_selesai' => $tgl['selesai'],
                            'status' => $penugasan->status,
                            'butuh_dl' => $validated['butuh_dl'],
                            'status_dl' => $validated['butuh_dl'] ? 'Menunggu' : null,
                            'butuh_translok' => $validated['butuh_translok'],
                            'status_translok' => $validated['butuh_translok'] ? 'Menunggu' : null,
                        ]);
                    }
                }
            });

            return redirect()
                ->route('sub.kegiatan.show', [
                    'kegiatan' => $subKegiatan->kegiatan->id_kegiatan,
                    'subKegiatan' => $subKegiatan->id_sub_kegiatan,
                ])
                ->with('success', 'Data Penugasan kepada anggota berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data penugasan kepada anggota. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function updateJenisKegiatan(Request $request, Penugasan $penugasan)
    {
        $this->authorize('updateJenisKegiatan', $penugasan);

        $validated = $request->validate([
            'id_jenis_kegiatan' => ['required'],
            'jenis_kegiatan_baru' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validated['id_jenis_kegiatan'] === 'LAINNYA') {
            if (empty($validated['jenis_kegiatan_baru'])) {
                return back()->with('error', 'Jenis kegiatan baru wajib diisi.')->withInput();
            }

            $jenis = JenisKegiatan::create([
                'jenis_kegiatan' => $validated['jenis_kegiatan_baru'],
                'kategori' => 'Tambahan',
            ]);

            $validated['id_jenis_kegiatan'] = $jenis->id;
        }

        try {
            $penugasan->update([
                'id_jenis_kegiatan' => $validated['id_jenis_kegiatan']
            ]);

            return redirect()->back()->with('success', 'Jenis kegiatan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Update Jenis Kegiatan error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui jenis kegiatan.')->withInput();
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
            DB::transaction(function () use ($penugasan, $validated) {
                $penugasan->update([
                    'status_dl' => $validated['status_dl'],
                ]);

                if ($validated['status_dl'] === 'ACC') {
                    $this->insertKalenderDL($penugasan);
                }
            });

            return redirect()->back()
                ->with('success', 'Status Dinas Luar berhasil diperbarui dan dimasukkan ke Kalender.');
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memperbarui data status Dinas Luar: ' . $e->getMessage())
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
            DB::transaction(function () use ($penugasan, $validated) {
                $penugasan->update([
                    'status_translok' => $validated['status_translok'],
                ]);

                if ($validated['status_translok'] === 'ACC') {
                    $this->insertKalenderDL($penugasan);
                }
            });

            return redirect()->back()
                ->with('success', 'Status Translok berhasil diperbarui dan dimasukkan ke Kalender.');
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memperbarui data status Translok: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function insertKalenderDL(Penugasan $penugasan)
    {
        $period = CarbonPeriod::create(
            $penugasan->tanggal_mulai,
            $penugasan->tanggal_selesai
        );

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        // Cek Bentrok
        $adaBentrok = KalenderDL::where('id_pegawai', $penugasan->id_anggota)
            ->whereIn('tanggal_dl', $dates)
            ->exists();

        if ($adaBentrok) {
            throw new \Exception('Pegawai sudah memiliki jadwal Kalender DL pada rentang tanggal tersebut.');
        }

        $dataToInsert = [];
        foreach ($dates as $d) {
            $dataToInsert[] = [
                'id_pegawai' => $penugasan->id_anggota,
                'id_penugasan' => $penugasan->id_penugasan,
                'tanggal_dl' => $d,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($dataToInsert)) {
            KalenderDL::insert($dataToInsert);
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

        // Bangun 1 Query raksasa untuk mengecek semua rentang tanggal sekaligus (Mencegah N+1)
        $query = Penugasan::where('id_anggota', $idAnggota)
            ->where(function ($q) {
                $q->where('butuh_dl', 1)->orWhere('butuh_translok', 1);
            });

        if (!empty($excludeId)) {
            $query->where('id_penugasan', '!=', $excludeId);
        }

        $query->where(function ($q) use ($dates) {
            foreach ($dates as $date) {
                $m = $date['tanggal_mulai'] ?? null;
                $s = $date['tanggal_selesai'] ?? null;
                if ($m && $s) {
                    $q->orWhere(function ($subQ) use ($m, $s) {
                        $subQ->where('tanggal_mulai', '<=', $s)
                            ->where('tanggal_selesai', '>=', $m);
                    });
                }
            }
        });

        $conflictRecords = $query->get(['tanggal_mulai', 'tanggal_selesai']);

        if ($conflictRecords->isNotEmpty()) {
            foreach ($dates as $index => $date) {
                $mulai = $date['tanggal_mulai'] ?? null;
                $selesai = $date['tanggal_selesai'] ?? null;
                if (!$mulai || !$selesai)
                    continue;

                $reqM = \Carbon\Carbon::parse($mulai)->startOfDay();
                $reqS = \Carbon\Carbon::parse($selesai)->startOfDay();

                foreach ($conflictRecords as $conflict) {
                    $dbM = \Carbon\Carbon::parse($conflict->tanggal_mulai)->startOfDay();
                    $dbS = \Carbon\Carbon::parse($conflict->tanggal_selesai)->startOfDay();

                    // Cek Irisan (Overlap) di RAM
                    if ($reqM->lessThanOrEqualTo($dbS) && $reqS->greaterThanOrEqualTo($dbM)) {
                        $dbMulaiStr = $dbM->translatedFormat('d M Y');
                        $dbSelesaiStr = $dbS->translatedFormat('d M Y');

                        $isMulaiHit = $reqM->greaterThanOrEqualTo($dbM) && $reqM->lessThanOrEqualTo($dbS);
                        $isSelesaiHit = $reqS->greaterThanOrEqualTo($dbM) && $reqS->lessThanOrEqualTo($dbS);

                        if (!$isMulaiHit && $isSelesaiHit) {
                            $focusEl = ($index === 0) ? 'tanggal_selesai' : "tanggal_selesai_$index";
                        } else {
                            $focusEl = ($index === 0) ? 'tanggal_mulai' : "tanggal_mulai_$index";
                        }

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
                            'is_selesai' => (!$isMulaiHit && $isSelesaiHit)
                        ];

                        break; // Lanjut ke rentang input berikutnya
                    }
                }
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
    private function extractAndValidateDates(Request $request, array $validated, $subKegiatanMulai, $subKegiatanSelesai, $skipMain = false, $penugasanIdToExclude = null): array
    {
        $allDates = [];

        // Selalu ambil main date untuk dievaluasi
        $mainMulai = $validated['tanggal_mulai'] ?? null;
        $mainSelesai = $validated['tanggal_selesai'] ?? null;

        if ($mainMulai && $mainSelesai) {
            $allDates[] = ['mulai' => $mainMulai, 'selesai' => $mainSelesai, 'is_main' => true];
        }

        $mulaiList = $request->tanggal_mulai_list ?? [];
        $selesaiList = $request->tanggal_selesai_list ?? [];
        foreach ($mulaiList as $index => $tglMulai) {
            $tglSelesai = $selesaiList[$index] ?? null;
            if ($tglMulai && $tglSelesai) {
                $allDates[] = ['mulai' => $tglMulai, 'selesai' => $tglSelesai, 'is_main' => false];
            }
        }

        $min = $subKegiatanMulai ? \Carbon\Carbon::parse((string) $subKegiatanMulai)->startOfDay() : null;
        $max = $subKegiatanSelesai ? \Carbon\Carbon::parse((string) $subKegiatanSelesai)->startOfDay() : null;

        $validDatesToSave = [];
        $existing = [];

        $idAnggota = $validated['id_anggota'] ?? null;
        $butuhDl = $validated['butuh_dl'] ?? 0;
        $butuhTranslok = $validated['butuh_translok'] ?? 0;
        $wajibCekBentrok = ($butuhDl == 1 || $butuhTranslok == 1);

        // 0. Cek Bentrok Backend Sekaligus (Mencegah N+1 Loop)
        if ($wajibCekBentrok && $idAnggota && count($allDates) > 0) {
            $query = \App\Models\Penugasan::where('id_anggota', $idAnggota)
                ->where(function ($q) {
                    $q->where('butuh_dl', 1)->orWhere('butuh_translok', 1);
                });

            if ($penugasanIdToExclude) {
                $query->where('id_penugasan', '!=', $penugasanIdToExclude);
            }

            $query->where(function ($q) use ($allDates) {
                foreach ($allDates as $tgl) {
                    $m = $tgl['mulai'] ?? null;
                    $s = $tgl['selesai'] ?? null;
                    if ($m && $s) {
                        $q->orWhere(function ($subQ) use ($m, $s) {
                            $subQ->where('tanggal_mulai', '<=', $s)
                                ->where('tanggal_selesai', '>=', $m);
                        });
                    }
                }
            });

            if ($query->exists()) {
                throw ValidationException::withMessages([
                    'tanggal_mulai' => 'Gagal: Pegawai sudah memiliki jadwal DL/Translok di penugasan lain pada rentang tanggal tersebut.'
                ]);
            }
        }

        foreach ($allDates as $tgl) {
            $m = $tgl['mulai'];
            $s = $tgl['selesai'];
            $isMain = $tgl['is_main'];

            if (empty($m) || empty($s)) {
                continue;
            }

            // Hindari duplikat input array
            $key = $m . '|' . $s;
            if (in_array($key, $existing)) {
                continue;
            }
            $existing[] = $key;

            $cm = \Carbon\Carbon::parse($m)->startOfDay();
            $cs = \Carbon\Carbon::parse($s)->startOfDay();

            // 1. Validasi Rentang Sub Kegiatan
            if ($cs->lt($cm) || ($min && $cm->lt($min)) || ($max && $cs->gt($max))) {
                throw ValidationException::withMessages([
                    'tanggal_mulai' => 'Tanggal penugasan tidak valid atau di luar rentang sub kegiatan.'
                ]);
            }

            if (!$skipMain || !$isMain) {
                $validDatesToSave[] = ['mulai' => $m, 'selesai' => $s];
            }
        }

        return $validDatesToSave;
    }
}
