<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RencanaJPT;
use Illuminate\Support\Facades\DB;

class RencanaJPTController extends Controller
{
    public function index()
    {
        $this->authorize('kelola-master-data');
        $rencanaJpts = RencanaJPT::with(['indikatorjpts'])->get();

        return view('pages.main.admin.rk-iki-jpt.index', ['title' => 'Rencana Kerja dan IKI Pimpinan', 'rencanaJpts' => $rencanaJpts]);
    }

    public function indikator(RencanaJPT $rencanaJpt)
    {
        return $rencanaJpt->indikatorJpts()
            ->orderBy('nama_indikator_jpt')
            ->get(['id', 'nama_indikator_jpt']);
    }


    public function store(Request $request)
    {
        $this->authorize('kelola-master-data');
        $validatedData = $request->validate([
            'tahun' => 'required|integer',
            'nama_rencana_jpt' => 'required|string|max:255',
            'ikis' => 'nullable|array',
            'ikis.*.nama_indikator_jpt' => 'nullable|string|max:255',
            'ikis.*.satuan' => 'nullable|string|max:100',
            'ikis.*.target' => 'nullable|numeric|min:0',
            'ikis.*.realisasi' => 'nullable|numeric|min:0',
            'ikis.*.status' => 'nullable|in:Selesai,Belum Selesai',

        ]);

        try {

            DB::transaction(function () use ($validatedData) {

                // Pisahkan ikis
                $ikis = $validatedData['ikis'] ?? [];

                $rencana = RencanaJPT::create([
                    'tahun' => $validatedData['tahun'],
                    'nama_rencana_jpt' => $validatedData['nama_rencana_jpt'],
                ]);

                // 2️⃣ Simpan IKI JPT (jika ada)
                if (!empty($ikis)) {
                    foreach ($ikis as $iki) {

                        $rencana->indikatorjpts()->create([
                            'nama_indikator_jpt' => $iki['nama_indikator_jpt'] ?? null,
                            'satuan' => $iki['satuan'] ?? null,
                            'target' => $iki['target'] ?? null,
                            'realisasi' => $iki['realisasi'] ?? null,
                            'status' => $iki['status'] ?? null,
                        ]);
                    }
                }
            });

            // Redirect dengan flash message
            return redirect()->route('rencana-indikator-jpt.rencana.index')
                ->with('success', 'Rencana JPT berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Rencana JPT. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function update(Request $request, RencanaJPT $rencanaJpt)
    {
        $this->authorize('kelola-master-data');
        $validatedData = $request->validate([
            'tahun' => 'required|integer',
            'nama_rencana_jpt' => 'required|string|max:255',
        ]);

        try {
            $rencanaJpt->update($validatedData);

            // Redirect dengan flash message
            return redirect()->route('rencana-indikator-jpt.rencana.index')
                ->with('success', 'Rencana JPT berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Rencana JPT. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function delete(RencanaJPT $rencanaJpt)
    {
        $this->authorize('kelola-master-data'); 
        try {
            $rencanaJpt->delete();

            return redirect()
                ->route('rencana-indikator-jpt.rencana.index')
                ->with('success', 'Rencana JPT berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus Rencana JPT');
        }
    }
}
