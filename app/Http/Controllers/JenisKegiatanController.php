<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisKegiatan;

class JenisKegiatanController extends Controller
{
    public function index() {
        $jenisKegiatan = JenisKegiatan::get();

        return view('pages.main.admin.jenis-kegiatan.index', [
            'title' => 'Jenis Kegiatan',
            'jenis_kegiatans' => $jenisKegiatan,
        ]);
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'jenis_kegiatan' => 'required|string|max:255',
            'kategori' => 'required|string|in:Utama,Tambahan',
        ]);

        $jenisKegiatan= JenisKegiatan::create($validatedData);

        return redirect()
        ->route('jenis-kegiatan.index', $jenisKegiatan->id)
        ->with('success', 'Jenis Kegiatan berhasil ditambahkan');
    }

    public function update(Request $request, JenisKegiatan $jenisKegiatan)
    {
        $validatedData = $request->validate([
            'jenis_kegiatan' => 'required|string|max:255',
            'kategori' => 'required|string|in:Utama,Tambahan',
        ]);

        try {
            $jenisKegiatan->update($validatedData);

            // Redirect dengan flash message
            return redirect()->route('jenis-kegiatan.index')
                ->with('success', 'Jenis Kegiatan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Jenis Kegiatan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function delete(JenisKegiatan $jenisKegiatan)
    {
        try {
            $jenisKegiatan->delete();

            return redirect()
                ->route('jenis-kegiatan.index')
                ->with('success', 'Jenis Kegiatan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus Jenis Kegiatan');
        }
    }
}
