<?php

namespace App\Http\Controllers;

use App\Models\IndikatorJPT;
use Illuminate\Http\Request;

class IndikatorJPTController extends Controller
{
    public function index()
    {
        $rencanaJpts = IndikatorJPT::with(['indikatorjpts'])->get();

        return view('pages.admin.rk-iki-jpt.index', ['title' => 'Rencana Kerja dan IKI Pimpinan', 'rencanaJpts' => $rencanaJpts]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_rencana_jpt' => 'required|integer',
            'nama_indikator_jpt' => 'required|string|max:255',
        ]);

        try {
            $indikatorJpt = IndikatorJPT::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Indikator JPT berhasil ditambahkan',
                'data' => $indikatorJpt
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan Indikator JPT: ' . $e->getMessage()
            ], 500);
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
