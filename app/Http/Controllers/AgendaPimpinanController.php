<?php

namespace App\Http\Controllers;

use App\Models\AgendaPimpinan;
use App\Models\RencanaJPT;
use App\Models\IndikatorJPT;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AgendaPimpinanController extends Controller
{
    public function index() {
        $this->authorize('kelola-master-data');
        $agendas = AgendaPimpinan::with(['rencanaJpt', 'indikatorJpt'])->get();
        $rkJpts = RencanaJPT::all();

        return view('pages.main.admin.agenda-pimpinan.index', [
            'title' => 'Agenda Pimpinan',
            'agendas' => $agendas,
            'rkJpts'    => $rkJpts,
        ]);
    }

    public function store(Request $request) {
        $this->authorize('kelola-master-data');
        $validated = $request->validate([
            'nama_agenda' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'rk_jpt' => ['required', 'exists:rencana_jpts,id'],
            'iki_jpt' => [
                'required',
                Rule::exists('indikator_jpts', 'id')
                    ->where('id_rencana_jpt', $request->rk_jpt),
            ],
            'target' => 'nullable|integer',
            'satuan_target' => 'nullable|string',
            'realisasi' => 'nullable|integer',
            'link_bukti' => 'nullable|url',
            'status' => 'required|in:Selesai,Belum Selesai',
        ]);

        try {
            $indikator = IndikatorJPT::find($validated['iki_jpt']);
            if ($indikator->id_rencana_jpt != $validated['rk_jpt']) {
                return back()->with('error', 'IKI tidak sesuai dengan RK');
            }

            AgendaPimpinan::create($validated);

            // Redirect dengan flash message
            return redirect()
                ->route('agenda.index')
                ->with('success', 'Kegiatan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Agenda. Silakan coba lagi.')
                ->withInput();
        }


    }

    public function update(Request $request, AgendaPimpinan $agenda) {
        $this->authorize('kelola-master-data');
        $validated = $request->validate([
            'nama_agenda' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'rk_jpt' => ['required', 'exists:rencana_jpts,id'],
            'iki_jpt' => [
                'required',
                Rule::exists('indikator_jpts', 'id')
                    ->where('id_rencana_jpt', $request->rk_jpt),
            ],
            'target' => 'nullable|integer',
            'satuan_target' => 'nullable|string',
            'realisasi' => 'nullable|integer',
            'link_bukti' => 'nullable|url',
            'status' => 'required|in:Selesai,Belum Selesai',
        ]);

        try {
            $agenda->update($validated);

            // Redirect dengan flash message
            return redirect()
                ->route('agenda.index')
                ->with('success', 'Agenda berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Agenda. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function delete(AgendaPimpinan $agenda) {
        $this->authorize('kelola-master-data');
        $agenda->delete();
        return back()->with('success', 'Agenda berhasil dihapus');
    }
}
