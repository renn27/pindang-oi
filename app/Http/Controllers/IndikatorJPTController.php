<?php

namespace App\Http\Controllers;

use App\Models\IndikatorJPT;
use Illuminate\Http\Request;
use App\Models\RencanaJPT;

class IndikatorJPTController extends Controller
{
    public function index()
    {
        $rencanaJpts = IndikatorJPT::with(['indikatorjpts'])->get();

        return view('pages.admin.rk-iki-jpt.index', ['title' => 'Rencana Kerja dan IKI Pimpinan', 'rencanaJpts' => $rencanaJpts]);
    }

    public function store(Request $request, RencanaJPT $rencanaJpt)
    {
        $validatedData = $request->validate([
            'nama_indikator_jpt' => 'required|string|max:255',
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

    public function update(Request $request, IndikatorJPT $indikatorJpt)
    {
        $validatedData = $request->validate([
            'nama_indikator_jpt' => 'required|string|max:255',
        ]);

        try {
            $indikatorJpt = IndikatorJPT::update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Indikator JPT berhasil diubah',
                'data' => $indikatorJpt
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah Indikator JPT: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete(IndikatorJPT $indikatorJpt)
    {
        try {
            $indikatorJpt->delete();

            return response()->json([
                'success' => true,
                'message' => 'Indikator JPT berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Indikator JPT: ' . $e->getMessage()
            ], 500);
        }
    }
}
