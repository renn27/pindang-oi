<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidang;
use App\Models\Kegiatan;
use App\Models\RencanaJPT;
use App\Models\Pegawai;
use App\Models\JenisKegiatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Exports\MphAllExport;
use App\Models\Penugasan;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\PushNotificationService;


class MasterKegiatanController extends Controller
{
    public function index()
    {
        // Data referensi untuk dropdown modal
        $pegawais = Pegawai::active()->orderBy('nama_pegawai')->get(['id_pegawai', 'nama_pegawai']);
        $rkJpts = RencanaJPT::orderBy('nama_rencana_jpt')->get(['id', 'nama_rencana_jpt']);
        $jenisKegiatans = JenisKegiatan::query()
            ->orderByRaw("
                CASE
                    WHEN kategori = 'Utama' THEN 1
                    WHEN kategori = 'Tambahan' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('jenis_kegiatan', 'asc')
            ->get();
        $ketuaTims = Pegawai::join('pegawai_role', 'pegawais.id_pegawai', '=', 'pegawai_role.pegawai_id')
            ->join('roles', 'pegawai_role.role_id', '=', 'roles.id')
            ->where('roles.nama_role', 'Ketua Tim')
            ->where('pegawais.is_active', true)
            ->orderBy('pegawais.nama_pegawai')
            ->get([
                'pegawais.id_pegawai',
                'pegawais.nama_pegawai',
                'roles.nama_role',
            ]);

        // Ambil semua kegiatan beserta relasinya untuk setiap bidang tanpa filter role
        $bidangs = Bidang::with([
            'kegiatans' => function ($query) {
                $query->with([
                    'subKegiatans' => function ($subQuery) {
                        $subQuery->with([
                            'penugasans' => function ($penugasanQuery) {
                                $penugasanQuery->with(['anggota', 'jenisKegiatan']);
                            }
                        ]);
                    },
                    'rencanaJpt',
                    'indikatorJpt',
                    'penanggungJawab' // asumsi ada relasi
                ]);
            }
        ])->orderBy('nama_bidang')->get();

        return view('pages.main.pegawai.rencana-kerja.master-kegiatan', [
            'title' => "Master Kegiatan",
            'bidangs' => $bidangs,
            'pegawais' => $pegawais,
            'rkJpts' => $rkJpts,
            'jenisKegiatans' => $jenisKegiatans,
            'ketuaTims' => $ketuaTims
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Kegiatan::class);
        try {
            $user = $request->user();
            if ($user?->isKetuaTim()) {
                $request->merge([
                    'id_penanggung_jawab' => $user->id_pegawai,
                ]);
            }

            $validated = $request->validate([
                'id_bidang' => ['required', 'exists:bidangs,id_bidang'],
                'nama_rk_kegiatan' => ['required', 'string'],
                'rk_jpt' => ['required', 'exists:rencana_jpts,id'],
                'iki_jpt' => ['required'],
                'id_penanggung_jawab' => [
                    'required',
                    Rule::exists('pegawais', 'id_pegawai')->where('is_active', true),
                ],
                'tahun_kegiatan' => ['required', 'digits:4'],

                'rk_anggota' => ['required', 'array', 'min:1'],
                'rk_anggota.*' => ['required', 'string'],

                'target' => ['array'],
                'satuan_target' => ['array'],
                'tanggal_mulai' => ['array'],
                'tanggal_akhir' => ['array'],

                'detail_id_anggota' => ['required', 'array'],
                'detail_id_anggota.*' => ['array'],
                'detail_id_anggota.*.*' => [
                    'required',
                    Rule::exists('pegawais', 'id_pegawai')->where('is_active', true),
                ],

                'detail_id_jenis_kegiatan' => ['required', 'array'],
                'detail_id_jenis_kegiatan.*.*' => ['required'],

                'detail_jenis_kegiatan_baru' => ['nullable', 'array'],
                'detail_jenis_kegiatan_baru.*.*' => ['nullable', 'string'],

                'detail_butuh_dl' => ['nullable', 'array'],
                'detail_butuh_dl.*' => ['array'],
                'detail_butuh_dl.*.*' => ['nullable', 'boolean'],

                'detail_butuh_translok' => ['nullable', 'array'],
                'detail_butuh_translok.*' => ['array'],
                'detail_butuh_translok.*.*' => ['nullable', 'boolean'],

                'detail_target' => ['array'],
                'detail_satuan_target' => ['array'],
                'detail_tanggal_mulai' => ['array'],
                'detail_tanggal_selesai' => ['array'],
            ]);

            $hasSelfAssign = false;
            $currentUserId = auth()->user()?->id_pegawai;
            if ($request->detail_id_anggota && $currentUserId) {
                foreach ($request->detail_id_anggota as $sectionId => $anggotaIds) {
                    if (is_array($anggotaIds) && in_array($currentUserId, $anggotaIds)) {
                        $hasSelfAssign = true;
                        break;
                    }
                }
            }

            $createdPenugasans = collect();

            DB::transaction(function () use ($request, $validated, $createdPenugasans) {
                $kegiatan = Kegiatan::create([
                    'id_bidang' => $request->id_bidang,
                    'nama_rk_kegiatan' => $request->nama_rk_kegiatan,
                    'rk_jpt' => $request->rk_jpt,
                    'iki_jpt' => $request->iki_jpt,
                    'id_penanggung_jawab' => $validated['id_penanggung_jawab'],
                    'tahun_kegiatan' => $request->tahun_kegiatan,
                ]);

                $rkAnggotas = $request->rk_anggota ?? [];

                foreach ($rkAnggotas as $index => $rkAnggota) {

                    $subKegiatan = $kegiatan->subKegiatans()->create([
                        'nama_sub_kegiatan' => $rkAnggota,
                        'target' => $request->target[$index] ?? null,
                        'satuan_target' => $request->satuan_target[$index] ?? null,
                        'tanggal_mulai' => $request->tanggal_mulai[$index] ?? null,
                        'tanggal_selesai' => $request->tanggal_selesai[$index] ?? null,
                        'status' => 'Berjalan', // ✅ DEFAULT
                    ]);

                    // Ambil key section (rk-anggota-1, dst)
                    $sectionKey = $request->rk_section_keys[$index] ?? null;

                    if (!$sectionKey)
                        continue;

                    $anggotaIds = $request->detail_id_anggota[$sectionKey] ?? [];
                    $jenisKegiatans = $request->detail_id_jenis_kegiatan[$sectionKey] ?? [];
                    $butuhDlInputs = $request->detail_butuh_dl[$sectionKey] ?? [];
                    $butuhTranslokInputs = $request->detail_butuh_translok[$sectionKey] ?? [];
                    $targets = $request->detail_target[$sectionKey] ?? [];
                    $satuanTargets = $request->detail_satuan_target[$sectionKey] ?? [];
                    $tglMulais = $request->detail_tanggal_mulai[$sectionKey] ?? [];
                    $tglSelesais = $request->detail_tanggal_selesai[$sectionKey] ?? [];

                    foreach ($anggotaIds as $i => $idAnggota) {
                        if (!$idAnggota)
                            continue;

                        $idJenisKegiatan = $jenisKegiatans[$i] ?? null;

                        if ($idJenisKegiatan === 'LAINNYA') {

                            $namaBaru = $request->detail_jenis_kegiatan_baru[$sectionKey][$i] ?? null;

                            if (!$namaBaru) {
                                throw new \Exception('Jenis kegiatan baru wajib diisi');
                            }

                            $jenis = JenisKegiatan::create([
                                'jenis_kegiatan' => $namaBaru,
                                'kategori' => 'Tambahan',
                            ]);

                            $idJenisKegiatan = $jenis->id;
                        } else {
                            // validasi FK existing
                            if (!$idJenisKegiatan || !JenisKegiatan::where('id', $idJenisKegiatan)->exists()) {
                                throw new \Exception('Jenis kegiatan tidak valid');
                            }
                        }

                        $jenisKegiatan = JenisKegiatan::find($idJenisKegiatan);

                        $wajibDl = $jenisKegiatan->butuh_dl_atau_translok == 1;

                        $requestButuhDl = (bool) ($butuhDlInputs[$i] ?? false);
                        $requestButuhTranslok = (bool) ($butuhTranslokInputs[$i] ?? false);

                        $butuhDlFinal = false;
                        $butuhTranslokFinal = false;

                        if ($wajibDl) {
                            if ($requestButuhDl) {
                                $butuhDlFinal = true;
                            } elseif ($requestButuhTranslok) {
                                $butuhTranslokFinal = true;
                            } else {
                                $butuhDlFinal = true; // fallback
                            }
                        }

                        $createdPenugasans->push($subKegiatan->penugasans()->create([
                            'id_anggota' => $idAnggota,
                            'id_jenis_kegiatan' => $idJenisKegiatan,
                            'target' => $targets[$i] ?? null,
                            'satuan_target' => $satuanTargets[$i] ?? null,
                            'tanggal_mulai' => $tglMulais[$i] ?? null,
                            'tanggal_selesai' => $tglSelesais[$i] ?? null,
                            'butuh_dl' => $butuhDlFinal,
                            'status_dl' => $butuhDlFinal ? 'Menunggu' : null,
                            'butuh_translok' => $butuhTranslokFinal,
                            'status_translok' => $butuhTranslokFinal ? 'Menunggu' : null,
                            'status' => 'Belum Dikirim', // ✅ DEFAULT
                        ]));
                    }
                }
            });

            $createdPenugasans->each(
                fn ($penugasan) => app(PushNotificationService::class)->notifyPenugasanCreated($penugasan)
            );
            
            $response = redirect()->back()->with('success', 'Kegiatan Berhasil Disimpan');
            if ($hasSelfAssign) {
                $response->with('info', 'Anda menambahkan diri anda sendiri di penugasan sub kegiatan ini.');
            }
            return $response;
        } catch (\Exception $e) {
            Log::error('Error umum simpan Data Master Kegiatan', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Gagal Menyimpan Data Master Kegiatan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function exportMphAll()
    {
        $bidangs = Bidang::orderBy('nama_bidang')->get(); // semua bidang

        return Excel::download(new MphAllExport($bidangs), 'matriks_peran_hasil.xlsx');
    }

    public function index_rk_dl()
    {
        $pegawai = Auth::user();
        $activeRole = $pegawai->active_role;

        $pegawais = Pegawai::orderBy('nama_pegawai')->get(['id_pegawai', 'nama_pegawai']);
        $ketuaTims = Pegawai::join('pegawai_role', 'pegawais.id_pegawai', '=', 'pegawai_role.pegawai_id')
            ->join('roles', 'pegawai_role.role_id', '=', 'roles.id')
            ->where('roles.nama_role', 'Ketua Tim')
            ->orderBy('pegawais.nama_pegawai')
            ->get([
                'pegawais.id_pegawai',
                'pegawais.nama_pegawai',
                'roles.nama_role',
            ]);

        $filterPenugasan = function ($q) {
            $q->where(function ($query) {
                $query->where('butuh_dl', true)->orWhere('butuh_translok', true);
            });
        };

        $bidangs = Bidang::whereHas('kegiatans', function ($kegiatanQuery) {
            $kegiatanQuery->whereHas('subKegiatans.penugasans', function ($q) {
                    $q->where(function ($query) {
                        $query->where('butuh_dl', true)->orWhere('butuh_translok', true);
                    });
                });
        })->with(['kegiatans' => function ($kegiatanQuery) use ($filterPenugasan) {
            $kegiatanQuery->whereHas('subKegiatans.penugasans', $filterPenugasan)
            ->with(['subKegiatans' => function ($subQuery) use ($filterPenugasan) {
            $subQuery->whereHas('penugasans', $filterPenugasan)
                ->with(['penugasans' => function ($penugasanQuery) use ($filterPenugasan) {
                    $penugasanQuery->where($filterPenugasan)
                        ->with(['anggota', 'jenisKegiatan']);
                    }
                ]);
            },
            'rencanaJpt',
            'indikatorJpt',
            'penanggungJawab'
        ]);
    }
        ])->orderBy('nama_bidang')->get();

        // 🔹 Hitung jumlah "Menunggu", "Ditolak", dan "ACC Belum Masuk Kalender" untuk tiap bidang
        $bidangs->each(function ($bidang) use ($pegawai, $activeRole) {
            $bidang->menungguCount = $bidang->kegiatans->sum(function ($kegiatan) {
                return $kegiatan->subKegiatans->sum(function ($sub) {
                    return $sub->penugasans->filter(function ($p) {
                        return $p->status_dl === 'Menunggu' || $p->status_translok === 'Menunggu';
                    })->count();
                });
            });

            $bidang->ditolakCount = $bidang->kegiatans->sum(function ($kegiatan) use ($pegawai, $activeRole) {
                // Jika Ketua Tim, hanya hitung yang dia tanggung jawab
                if ($activeRole === 'Ketua Tim' && $kegiatan->id_penanggung_jawab !== $pegawai->id_pegawai) {
                    return 0;
                }

                return $kegiatan->subKegiatans->sum(function ($sub) {
                    return $sub->penugasans->filter(function ($p) {
                        return $p->status_dl === 'Ditolak' || $p->status_translok === 'Ditolak';
                    })->count();
                });
            });

        });

        $allPenugasans = $bidangs
            ->flatMap(fn($bidang) => $bidang->kegiatans)
            ->flatMap(fn($kegiatan) => $kegiatan->subKegiatans)
            ->flatMap(fn($sub) => $sub->penugasans);

        $menunggu = $allPenugasans->filter(fn($p) =>
            $p->status_dl === 'Menunggu' || $p->status_translok === 'Menunggu'
        )->count();

        $diterima = $allPenugasans->filter(fn($p) =>
            $p->status_dl === 'ACC' || $p->status_translok === 'ACC'
        )->count();

        $ditolak = $allPenugasans->filter(fn($p) =>
            $p->status_dl === 'Ditolak' || $p->status_translok === 'Ditolak'
        )->count();

        return view('pages.main.pegawai.rencana-kerja.rencana-kerja-dl', [
            'title' => "Rencana Kerja Perlu DL",
            'bidangs' => $bidangs,
            'pegawais' => $pegawais,
            'ketuaTims' => $ketuaTims,
            'allPenugasans' => $allPenugasans,
            'menunggu' => $menunggu,
            'diterima' => $diterima,
            'ditolak' => $ditolak,
        ]);
    }
}
