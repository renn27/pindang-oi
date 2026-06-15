<?php

namespace App\Http\Controllers;

use App\Models\AgendaPimpinan;
use App\Models\RencanaJPT;
use App\Models\IndikatorJPT;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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
            'butuh_dl' => 'nullable|in:0,1',
        ]);

        try {
            $indikator = IndikatorJPT::find($validated['iki_jpt']);
            if ($indikator->id_rencana_jpt != $validated['rk_jpt']) {
                return back()->with('error', 'IKI tidak sesuai dengan RK');
            }

            $validated['butuh_dl'] = $request->has('butuh_dl') ? (bool)$request->butuh_dl : false;

            DB::transaction(function () use ($validated) {
                $agenda = AgendaPimpinan::create($validated);
                $this->syncKalenderDL($agenda);
            });

            // Redirect dengan flash message
            return redirect()
                ->route('agenda.index')
                ->with('success', 'Kegiatan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Agenda: ' . $e->getMessage())
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
            'butuh_dl' => 'nullable|in:0,1',
        ]);

        try {
            $validated['butuh_dl'] = $request->has('butuh_dl') ? (bool)$request->butuh_dl : false;

            DB::transaction(function () use ($agenda, $validated) {
                $agenda->update($validated);
                $this->syncKalenderDL($agenda);
            });

            // Redirect dengan flash message
            return redirect()
                ->route('agenda.index')
                ->with('success', 'Agenda berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Agenda: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function delete(AgendaPimpinan $agenda) {
        $this->authorize('kelola-master-data');
        $agenda->delete();
        return back()->with('success', 'Agenda berhasil dihapus');
    }

    private function syncKalenderDL(AgendaPimpinan $agenda)
    {
        // Delete existing rows for this agenda
        \App\Models\KalenderDL::where('id_agenda_pimpinan', $agenda->id_agenda)->delete();

        if ($agenda->butuh_dl) {
            $pimpinan = \App\Models\Pegawai::where('nama_pegawai', 'Sukendro Suryo Wiguno, SST, M.Ec.Dev')->first();
            if (!$pimpinan) {
                throw new \Exception('Pegawai Pimpinan tidak ditemukan.');
            }

            $period = \Carbon\CarbonPeriod::create($agenda->tanggal_mulai, $agenda->tanggal_selesai);
            $dataToInsert = [];
            foreach ($period as $date) {
                $dataToInsert[] = [
                    'id_pegawai' => $pimpinan->id_pegawai,
                    'id_penugasan' => null,
                    'id_agenda_pimpinan' => $agenda->id_agenda,
                    'tanggal_dl' => $date->format('Y-m-d'),
                    'keterangan' => 'DL Pimpinan',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($dataToInsert)) {
                \App\Models\KalenderDL::insert($dataToInsert);
            }
        }
    }
}
