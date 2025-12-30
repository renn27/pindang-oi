<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidang;
use App\Models\Kegiatan;
use App\Models\RencanaJPT;
use App\Models\Pegawai;
use Illuminate\Validation\Rule;

class KegiatanController extends Controller
{
    public function index(Bidang $bidang) {
        // Data utama
        $kegiatans = $bidang->kegiatans()
            ->with(['subKegiatans', 'rencanaJpt', 'indikatorJpt'])
            ->get();

        // Data referensi untuk dropdown modal
        $pegawais = Pegawai::orderBy('nama_pegawai')->get(['id_pegawai', 'nama_pegawai']);
        $rkJpts   = RencanaJPT::orderBy('nama_rencana_jpt')->get(['id', 'nama_rencana_jpt']);

        return view('pages.main.pegawai.tagihan-kerja.index', [
            'title'     => 'Kegiatan pada' . $bidang->nama_bidang,
            'bidang'    => $bidang,
            'kegiatans' => $kegiatans,
            'pegawais'  => $pegawais,
            'rkJpts'    => $rkJpts,
        ]);
    }

    public function subKegiatan()
    {
        $kegiatans = Kegiatan::all();
        return view('pages.main.pegawai.tagihan-kerja.detail-kegiatan', ['title' => 'Sub Kegiatan', 'kegiatans' => $kegiatans]);
    }

    public function show($slug)
    {
        $bidang = Bidang::where('slug', $slug)->firstOrFail();
        return view('pages.rencana-kerja.show', ['title' => $bidang->nama_bidang, 'bidang' => $bidang]);
    }

    public function store(Request $request, Bidang $bidang) {
        // dd($request->all());

        $validated = $request->validate([
            'nama_rk_kegiatan' => ['required', 'string', 'max:255'],
            'rk_jpt' => ['required','exists:rencana_jpts,id'],
            'iki_jpt' => [
                'required',
                Rule::exists('indikator_jpts', 'id')
                    ->where('id_rencana_jpt', $request->rk_jpt),
            ],
            'id_penanggung_jawab' => ['required', 'exists:pegawais,id_pegawai',],
            'tahun_kegiatan' => ['required'],
        ]);

        // 🔐 PAKSA id_bidang dari route
        $validated['id_bidang'] = $bidang->id_bidang;

        try {
            // Simpan
            Kegiatan::create($validated);

            // Redirect dengan flash message
            return redirect()
                ->route('kegiatan.index', $bidang->slug)
                ->with('success', 'Kegiatan berhasil ditambahkan.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            // return redirect()->back()
            //     ->with('error', 'Gagal menambahkan Kegiatan. Silakan coba lagi.')
            //     ->withInput();
        }
    }
}
