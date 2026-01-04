<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Pegawai;

class SubKegiatanController extends Controller
{
    public function store(Request $request, Kegiatan $kegiatan) {
        // dd($request->all());

        $validated = $request->validate([
            'nama_sub_kegiatan' => ['required', 'string', 'max:255'],
            'jenis_kegiatan' => ['required', 'string', 'max:255'],
            'satuan_target' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['required',
                                    'date',
                                    'date_format:Y-m-d',
                                    'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:Belum Mulai,Berjalan,Selesai'],
        ]);

        try {
            // Simpan
            $kegiatan->subKegiatans()->create($validated);

            // Redirect dengan flash message
            return redirect()
                ->route('kegiatan.index', [
                    'bidang' => $kegiatan->bidang->slug
                ])
                ->with('success', 'Sub Kegiatan berhasil ditambahkan.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Sub Kegiatan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function show(Kegiatan $kegiatan, SubKegiatan $subKegiatan) {
        // Data referensi untuk dropdown modal
        $pegawais = Pegawai::orderBy('nama_pegawai')->get(['id_pegawai', 'nama_pegawai']);

        return view('pages.main.pegawai.tagihan-kerja.detail-sub-kegiatan', [
            'kegiatan' => $kegiatan,
            'subKegiatan' => $subKegiatan,
            'pegawais' => $pegawais
        ]);
    }

    public function update(Request $request, Kegiatan $kegiatan, SubKegiatan $subKegiatan){
        // dd($request->all());

        $validated = $request->validate([
            'nama_sub_kegiatan' => ['required', 'string', 'max:255'],
            'jenis_kegiatan' => ['required', 'string', 'max:255'],
            'satuan_target' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['required',
                                    'date',
                                    'date_format:Y-m-d',
                                    'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'in:Belum Mulai,Berjalan,Selesai'],
        ]);

        // 🔒 VALIDASI KEPEMILIKAN (PENTING!)
        if ($subKegiatan->id_kegiatan !== $kegiatan->id_kegiatan) {
            abort(403, 'Sub kegiatan tidak milik kegiatan ini');
        }


        try {
            $subKegiatan->update($validated);
            // Redirect dengan flash message
            return redirect()
                ->route('kegiatan.index', [
                    'bidang' => $kegiatan->bidang->slug
                ])
                ->with('success', 'Sub Kegiatan berhasil diperbarui.');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Sub Kegiatan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function delete(Kegiatan $kegiatan, SubKegiatan $subKegiatan) {
        // optional safety check
        if ($subKegiatan->id_kegiatan != $kegiatan->id_kegiatan) {
            abort(403, 'Sub kegiatan tidak sesuai dengan kegiatan');
        }

        $subKegiatan->delete();

        return redirect()->back()->with('success', 'Sub kegiatan berhasil dihapus');
    }

}
