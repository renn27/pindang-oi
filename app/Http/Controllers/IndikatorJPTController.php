<?php

namespace App\Http\Controllers;

use App\Models\IndikatorJPT;
use Illuminate\Http\Request;
use App\Models\RencanaJPT;

class IndikatorJPTController extends Controller
{
    public function store(Request $request, RencanaJPT $rencanaJpt) {
        $this->authorize('kelola-master-data');
        $validatedData = $request->validate([
            'nama_indikator_jpt' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:100',
            'target' => 'nullable|numeric|min:0',
            'realisasi' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:Selesai,Belum Selesai',
        ]);

        try {
            $rencanaJpt->indikatorjpts()->create($validatedData);

            // Redirect dengan flash message
            return redirect()->route('rencana-indikator-jpt.rencana.index')
                ->with('success', 'Indikator JPT berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Indikator JPT. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function update(Request $request, RencanaJPT $rencanaJpt, IndikatorJPT $indikatorJpt) {
        $this->authorize('kelola-master-data');
        $validatedData = $request->validate([
            'nama_indikator_jpt' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:100',
            'target' => 'nullable|numeric|min:0',
            'realisasi' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:Selesai,Belum Selesai',
        ]);

        // pastikan indikator milik rencana tsb
        if ($indikatorJpt->id_rencana_jpt !== $rencanaJpt->id) {
            abort(403);
        }

        try {
            $indikatorJpt->update($validatedData);

            return back()->with('success', 'Indikator JPT berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal memperbarui Indikator JPT.')
                ->withInput();
        }
    }

    public function delete(RencanaJPT $rencanaJpt, IndikatorJPT $indikatorJpt) {
        $this->authorize('kelola-master-data');

        try {
            $indikatorJpt->delete();

            return back()->with('success', 'Indikator JPT berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus Indikator JPT');
        }
    }
}
