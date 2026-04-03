<?php

namespace App\Http\Controllers;

use App\Models\AgendaPimpinan;
use App\Models\RencanaJPT;
use App\Models\IndikatorJPT;
use Illuminate\Http\Request;

class AgendaPimpinanController extends Controller
{
    public function index()
    {
        $agenda = AgendaPimpinan::with(['rencanaJpt', 'indikatorJpt'])->get();
        $rencanaJpts = RencanaJPT::all();

        return view('pages.main.admin.agenda-pimpinan.index', [
            'title' => 'Agenda Pimpinan',
            'agenda' => $agenda,
            'rencanaJpts' => $rencanaJpts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'id_rencana_jpt' => 'required',
            'id_indikator_jpt' => 'required',
            'link_bukti' => 'nullable|url',
            'status' => 'required|in:Selesai,Belum Selesai',
        ]);

        // 🔥 VALIDASI RELASI
        $indikator = IndikatorJPT::find($validated['id_indikator_jpt']);
        if ($indikator->id_rencana_jpt != $validated['id_rencana_jpt']) {
            return back()->with('error', 'IKI tidak sesuai dengan RK');
        }

        AgendaPimpinan::create($validated);

        return back()->with('success', 'Agenda berhasil ditambahkan');
    }

    public function update(Request $request, AgendaPimpinan $agenda)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'id_rencana_jpt' => 'required',
            'id_indikator_jpt' => 'required',
            'link_bukti' => 'nullable|url',
            'status' => 'required|in:Selesai,Belum Selesai',
        ]);

        $agenda->update($validated);

        return back()->with('success', 'Agenda berhasil diupdate');
    }

    public function delete(AgendaPimpinan $agenda)
    {
        $agenda->delete();
        return back()->with('success', 'Agenda berhasil dihapus');
    }
}