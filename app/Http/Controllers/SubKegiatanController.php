<?php

namespace App\Http\Controllers;

use App\Models\JenisKegiatan;
use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Pegawai;
use App\Models\Penugasan;
use Carbon\Carbon;

class SubKegiatanController extends Controller
{
    public function store(Request $request, Kegiatan $kegiatan) {
        // dd($request->all());
        $this->authorize('createSubKegiatan', $kegiatan);

        $validated = $request->validate([
            'nama_sub_kegiatan' => ['required', 'string', 'max:255'],
            'target' => ['required', 'integer', 'min:1'],
            'satuan_target' => ['required', 'string'],
            'tanggal_mulai' => [
                'required',
                'date',
            ],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
        ]);

        // STATUS AWAL (STATE DEFAULT)
        $validated['status'] = 'Berjalan';

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
        $jenisKegiatans = JenisKegiatan::query()
            ->orderByRaw("
                CASE
                WHEN kategori = 'Utama' THEN 1
                WHEN kategori = 'Tambahan' THEN 2
                ELSE 3
                END
                ")
            ->orderBy('jenis_kegiatan', 'asc')
            ->get();

        $penugasans = $subKegiatan->penugasans()->orderBy('tanggal_mulai', 'asc')->get();

        // === FILTER BUTUH DL / TIDAK BUTUH DL ===
        $penugasanButuhDL = $penugasans->filter(fn ($row) => $row->butuh_dl);
        $penugasanTidakButuhDL = $penugasans->filter(fn ($row) => ! $row->butuh_dl);

        // Total (SEMUA)
        $totalKirim = $this->hitungTotalKirim($penugasans);
        $totalTerima = $this->hitungTotalTerima($penugasans);

        // Total BUTUH DL
        $totalKirimButuhDL = $this->hitungTotalKirim($penugasanButuhDL);
        $totalTerimaButuhDL = $this->hitungTotalTerima($penugasanButuhDL);

        // Total TIDAK BUTUH DL
        $totalKirimTidakButuhDL = $this->hitungTotalKirim($penugasanTidakButuhDL);
        $totalTerimaTidakButuhDL = $this->hitungTotalTerima($penugasanTidakButuhDL);

        return view('pages.main.pegawai.tagihan-kerja.detail-sub-kegiatan', [
            'kegiatan' => $kegiatan,
            'subKegiatan' => $subKegiatan,
            'pegawais' => $pegawais,
            'jenisKegiatans' => $jenisKegiatans,
            'penugasanButuhDL' => $penugasanButuhDL,
            'penugasanTidakButuhDL' => $penugasanTidakButuhDL,
            'totalKirim' => $totalKirim,
            'totalKirimTerima' => $totalTerima,
            'totalKirimButuhDL' => $totalKirimButuhDL,
            'totalTerimaButuhDL' => $totalTerimaButuhDL,
            'totalKirimTidakButuhDL' => $totalKirimTidakButuhDL,
            'totalTerimaTidakButuhDL' => $totalTerimaTidakButuhDL,
        ]);
    }

    public function update(Request $request, Kegiatan $kegiatan, SubKegiatan $subKegiatan){
        // dd($request->all());

        $this->authorize('update', $subKegiatan);

        $validated = $request->validate([
            'nama_sub_kegiatan' => ['required', 'string', 'max:255'],
            'target' => ['required', 'integer', 'min:1'],
            'satuan_target' => ['required', 'string'],
            'tanggal_mulai' => [
                'required',
                'date',
            ],
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
            // 'status' => ['required', 'in:Belum Mulai,Berjalan,Selesai'],
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
        $this->authorize('delete', $subKegiatan);
        // optional safety check
        if ($subKegiatan->id_kegiatan != $kegiatan->id_kegiatan) {
            abort(403, 'Sub kegiatan tidak sesuai dengan kegiatan');
        }

        $subKegiatan->forceDelete();

        return redirect()->back()->with('success', 'Sub kegiatan berhasil dihapus');
    }

    private function hitungTotalKirim($penugasans)
    {
        return $penugasans->unique('id_penugasan')->sum(fn ($p) =>
            $p->latestPengiriman?->jumlah_dikirim ?? 0
        );
    }

    private function hitungTotalTerima($penugasans)
    {
        return $penugasans->unique('id_penugasan')->sum(fn ($p) =>
            $p->latestPenerimaan?->jumlah_diterima ?? 0
        );
    }


}
