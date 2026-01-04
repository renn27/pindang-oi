<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidang;
use App\Models\Kegiatan;
use App\Models\RencanaJPT;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;




class MasterKegiatanController extends Controller
{
    public function index()
    {

        // Data referensi untuk dropdown modal
        $pegawais = Pegawai::orderBy('nama_pegawai')->get(['id_pegawai', 'nama_pegawai']);
        $rkJpts   = RencanaJPT::orderBy('nama_rencana_jpt')->get(['id', 'nama_rencana_jpt']);
        $bidangs   = Bidang::orderBy('nama_bidang')->get(['id_bidang', 'nama_bidang']);


        return view('pages.rencana-kerja.rk-ketua', [
            'pegawais'  => $pegawais,
            'rkJpts'    => $rkJpts,
            'bidangs'    => $bidangs,
        ]);
    }


    // public function show($slug)
    // {
    //     $bidang = Bidang::where('slug', $slug)->firstOrFail();
    //     return view('pages.rencana-kerja.show', ['title' => $bidang->nama_bidang, 'bidang' => $bidang]);
    // }

    public function store(Request $request)
    {
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
                'status' => ['required', 'array'],
                'status.*' => ['required', 'in:Belum Mulai,Berjalan,Selesai'],

                'satuan_target' => ['array'],
                'tanggal_mulai' => ['array'],
                'tanggal_akhir' => ['array'],

                'detail_id_anggota' => ['required', 'array'],
                'detail_id_anggota.*' => ['array'],
                'detail_id_anggota.*.*' => ['required', 'exists:pegawais,id_pegawai'],

                'detail_target' => ['array'],
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
                        'jenis_kegiatan'    => 'RK Anggota',
                        'satuan_target'     => $request->satuan_target[$index] ?? null,
                        'tanggal_mulai'     => $request->tanggal_mulai[$index] ?? null,
                        'tanggal_selesai'   => $request->tanggal_akhir[$index] ?? null,
                        'status'            => $request->status[$index] ?? 'Belum Mulai', // ✅ PAKAI INPUT FORM

                    ]);

                    // Ambil key section (rk-anggota-1, dst)
                    $sectionKey = $request->rk_section_keys[$index] ?? null;

                    if (!$sectionKey) continue;

                    $anggotaIds     = $request->detail_id_anggota[$sectionKey] ?? [];
                    $targets        = $request->detail_target[$sectionKey] ?? [];
                    $tglMulais      = $request->detail_tanggal_mulai[$sectionKey] ?? [];
                    $tglSelesais    = $request->detail_tanggal_selesai[$sectionKey] ?? [];

                    foreach ($anggotaIds as $i => $idAnggota) {
                        if (!$idAnggota) continue;

                        $subKegiatan->penugasans()->create([
                            'id_anggota'      => $idAnggota,
                            'target'          => $targets[$i] ?? null,
                            'tanggal_mulai'   => $tglMulais[$i] ?? null,
                            'tanggal_selesai' => $tglSelesais[$i] ?? null,
                            'status'          => 'Belum Dikirim',
                        ]);
                    }
                }
            });

            return redirect()
                ->back()
                ->with('success', 'RK berhasil disimpan.');
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
}
