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
use Maatwebsite\Excel\Facades\Excel;


class MasterKegiatanController extends Controller
{
    public function index()
    {
        // Data referensi untuk dropdown modal
        $pegawais = Pegawai::orderBy('nama_pegawai')->get(['id_pegawai', 'nama_pegawai']);
        $rkJpts   = RencanaJPT::orderBy('nama_rencana_jpt')->get(['id', 'nama_rencana_jpt']);
        // $bidangs  = Bidang::orderBy('nama_bidang')->get(); // ambil semua bidang
        $jenisKegiatans = JenisKegiatan::orderBy('jenis_kegiatan')->get();
        $ketuaTims = Pegawai::join('pegawai_role', 'pegawais.id_pegawai', '=', 'pegawai_role.pegawai_id')
            ->join('roles', 'pegawai_role.role_id', '=', 'roles.id')
            ->where('roles.nama_role', 'Ketua Tim')
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
            'title'         => "Master Kegiatan",
            'bidangs'       => $bidangs,
            'pegawais'      => $pegawais,
            'rkJpts'        => $rkJpts,
            'jenisKegiatans' => $jenisKegiatans,
            'ketuaTims'     => $ketuaTims
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $this->authorize('create', Kegiatan::class);
        try {
            $validated = $request->validate([
                'id_bidang' => ['required', 'exists:bidangs,id_bidang'],
                'nama_rk_kegiatan' => ['required', 'string'],
                'rk_jpt' => ['required', 'exists:rencana_jpts,id'],
                'iki_jpt' => ['required'],
                'id_penanggung_jawab' => ['required', 'exists:pegawais,id_pegawai'],
                'tahun_kegiatan' => ['required', 'digits:4'],

                'rk_anggota' => ['required', 'array', 'min:1'],
                'rk_anggota.*' => ['required', 'string'],

                // ⬇️ PENTING (karena dipakai di controller)
                // 'status' => ['required', 'array'],
                // 'status.*' => ['required', 'in:Belum Mulai,Berjalan,Selesai'],

                'satuan_target' => ['array'],
                'tanggal_mulai' => ['array'],
                'tanggal_akhir' => ['array'],

                'detail_id_anggota' => ['required', 'array'],
                'detail_id_anggota.*' => ['array'],
                'detail_id_anggota.*.*' => ['required', 'exists:pegawais,id_pegawai'],

                'detail_id_jenis_kegiatan' => ['required', 'array'],
                'detail_id_jenis_kegiatan.*.*' => ['required'],

                'detail_jenis_kegiatan_baru' => ['nullable', 'array'],

                'detail_target' => ['array'],
                'detail_satuan_target' => ['array'],
                'detail_tanggal_mulai' => ['array'],
                'detail_tanggal_selesai' => ['array'],
            ]);

            DB::transaction(function () use ($request) {

                // 1️⃣ SIMPAN KEGIATAN (PARENT)
                $kegiatan = Kegiatan::create([
                    'id_bidang'            => $request->id_bidang,
                    'nama_rk_kegiatan'     => $request->nama_rk_kegiatan,
                    'rk_jpt'               => $request->rk_jpt,
                    'iki_jpt'              => $request->iki_jpt,
                    'id_penanggung_jawab'  => $request->id_penanggung_jawab,
                    'tahun_kegiatan'       => $request->tahun_kegiatan,
                ]);

                $rkAnggotas = $request->rk_anggota ?? [];

                foreach ($rkAnggotas as $index => $rkAnggota) {

                    $subKegiatan = $kegiatan->subKegiatans()->create([
                        'nama_sub_kegiatan' => $rkAnggota,
                        'target'            => $request->target[$index] ?? null,
                        'tanggal_mulai'     => $request->tanggal_mulai[$index] ?? null,
                        'tanggal_selesai'   => $request->tanggal_selesai[$index] ?? null,
                        'status'            => 'Belum Mulai', // ✅ DEFAULT
                    ]);

                    // Ambil key section (rk-anggota-1, dst)
                    $sectionKey = $request->rk_section_keys[$index] ?? null;

                    if (!$sectionKey) continue;

                    $anggotaIds     = $request->detail_id_anggota[$sectionKey] ?? [];
                    $jenisKegiatans = $request->detail_id_jenis_kegiatan[$sectionKey] ?? [];
                    $targets        = $request->detail_target[$sectionKey] ?? [];
                    $satuanTargets  = $request->detail_satuan_target[$sectionKey] ?? [];
                    $tglMulais      = $request->detail_tanggal_mulai[$sectionKey] ?? [];
                    $tglSelesais    = $request->detail_tanggal_selesai[$sectionKey] ?? [];

                    foreach ($anggotaIds as $i => $idAnggota) {
                        if (!$idAnggota) continue;

                        $idJenisKegiatan = $jenisKegiatans[$i] ?? null;

                        // 🔥 HANDLE JENIS KEGIATAN (SELECT / CREATE)
                        if ($idJenisKegiatan === 'LAINNYA') {

                            $namaBaru = $request->detail_jenis_kegiatan_baru[$sectionKey][$i] ?? null;

                            if (!$namaBaru) {
                                throw new \Exception('Jenis kegiatan baru wajib diisi');
                            }

                            $jenis = JenisKegiatan::create([
                                'jenis_kegiatan' => $namaBaru,
                                'kategori'       => 'Tambahan',
                            ]);

                            $idJenisKegiatan = $jenis->id;
                        } else {
                            // validasi FK existing
                            if (!$idJenisKegiatan || !JenisKegiatan::where('id', $idJenisKegiatan)->exists()) {
                                throw new \Exception('Jenis kegiatan tidak valid');
                            }
                        }

                        $subKegiatan->penugasans()->create([
                            'id_anggota'      => $idAnggota,
                            'id_jenis_kegiatan' => $idJenisKegiatan,
                            'satuan_target'   => $satuanTargets[$i] ?? null,
                            'target'          => $targets[$i] ?? null,
                            'tanggal_mulai'   => $tglMulais[$i] ?? null,
                            'tanggal_selesai' => $tglSelesais[$i] ?? null,
                            'status'          => 'Belum Dikirim', // ✅ DEFAULT
                        ]);
                    }
                }
            });

            return redirect()
                ->back()
                ->with('success', 'Kegiatan Berhasil Disimpan');
            // ->with('success', 'RK berhasil disimpan.');
        } catch (QueryException $e) {
            // log error DB
            Log::error('Gagal simpan RK', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan database. Silakan coba lagi.')
                ->withInput();
        } catch (\Exception $e) {
            // error umum
            Log::error('Error umum simpan RK', [
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Gagal menyimpan data. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function exportMphAll()
    {
        $bidangs = Bidang::orderBy('nama_bidang')->get(); // semua bidang

        return Excel::download(new MphAllExport($bidangs), 'matriks_peran_hasil.xlsx');
    }


    // RENCANA KERJA DENGAN DL
    public function index_rk_dl()
    {
        // Data referensi untuk dropdown modal
        $pegawais = Pegawai::orderBy('nama_pegawai')->get(['id_pegawai', 'nama_pegawai']);
        $rkJpts   = RencanaJPT::orderBy('nama_rencana_jpt')->get(['id', 'nama_rencana_jpt']);
        // $bidangs  = Bidang::orderBy('nama_bidang')->get(); // ambil semua bidang
        $jenisKegiatans = JenisKegiatan::orderBy('jenis_kegiatan')->get();
        $ketuaTims = Pegawai::join('pegawai_role', 'pegawais.id_pegawai', '=', 'pegawai_role.pegawai_id')
            ->join('roles', 'pegawai_role.role_id', '=', 'roles.id')
            ->where('roles.nama_role', 'Ketua Tim')
            ->orderBy('pegawais.nama_pegawai')
            ->get([
                'pegawais.id_pegawai',
                'pegawais.nama_pegawai',
                'roles.nama_role',
            ]);

        $bidangs = Bidang::whereHas('kegiatans.subKegiatans.penugasans', function ($q) {
            $q->where('butuh_dl', true);
        })
        ->with([
            'kegiatans' => function ($query) {
                $query->whereHas('subKegiatans.penugasans', function ($q) {
                    $q->where('butuh_dl', true);
                })
                ->with([
                    'subKegiatans' => function ($subQuery) {
                        $subQuery->whereHas('penugasans', function ($q) {
                            $q->where('butuh_dl', true);
                        })
                        ->with([
                            'penugasans' => function ($penugasanQuery) {
                                $penugasanQuery
                                    ->where('butuh_dl', true)
                                    // ❗ status_dl TIDAK difilter
                                    ->with([
                                        'anggota',
                                        'jenisKegiatan'
                                    ]);
                            }
                        ]);
                    },
                    'rencanaJpt',
                    'indikatorJpt',
                    'penanggungJawab'
                ]);
            }
        ])
        ->orderBy('nama_bidang')
        ->get();

        return view('pages.main.pegawai.rencana-kerja.rencana-kerja-dl', [
            'title'         => "Rencana Kerja Butuh DL",
            'bidangs'       => $bidangs,
            'pegawais'      => $pegawais,
            'rkJpts'        => $rkJpts,
            'jenisKegiatans' => $jenisKegiatans,
            'ketuaTims'     => $ketuaTims
        ]);
    }
}
