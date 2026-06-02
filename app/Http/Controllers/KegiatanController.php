<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidang;
use App\Models\Kegiatan;
use App\Models\RencanaJPT;
use App\Models\Pegawai;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Services\PushNotificationService;
use App\Exports\MphExport;
use Maatwebsite\Excel\Facades\Excel;

class KegiatanController extends Controller
{
    public function index(Bidang $bidang)
    {
        $pegawai = Auth::user();
        $this->authorize('viewAny', Kegiatan::class);
        $kegiatanQuery = $bidang->kegiatans()
            ->with([
                'penanggungJawab',
                'transfer.fromKetua',
                'transfer.toKetua',
                'subKegiatans' => function ($q) use ($pegawai) {

                    // MODE ANGGOTA → subkegiatan harus ada penugasan dia
                    if ($pegawai->active_role === 'Anggota Tim') {
                        $q->whereHas('penugasans', function ($p) use ($pegawai) {
                            $p->where('id_anggota', $pegawai->id_pegawai);
                        });

                    }
                    $q->orderByRaw('COALESCE(tanggal_mulai, tanggal_selesai) ASC');

                    // ✅ Tambahkan eager load ini
                    $q->with([
                        'ckp',
                        'ckpBulanan',
                        'penugasans.pengirimans.penerimaan',
                    ]);
                },
                'rencanaJpt',
                'indikatorJpt'
            ]);

        if ($pegawai->active_role === 'Anggota Tim') {
            $kegiatanQuery->forAnggota($pegawai);
        }

        if ($pegawai->active_role === 'Ketua Tim') {
            $kegiatanQuery->forKetua($pegawai);
        }

        $kegiatans = $kegiatanQuery->get();
        // $subKegiatans = $kegiatans->flatMap->subKegiatans;

        // Data referensi untuk dropdown modal
        $pegawais = Pegawai::active()->orderBy('nama_pegawai')->get(['id_pegawai', 'nama_pegawai']);
        $rkJpts   = RencanaJPT::orderBy('nama_rencana_jpt')->get(['id', 'nama_rencana_jpt']);
        $ketuaTims = Pegawai::join('pegawai_role', 'pegawais.id_pegawai', '=', 'pegawai_role.pegawai_id')
            ->join('roles', 'pegawai_role.role_id', '=', 'roles.id')
            ->where('roles.nama_role', 'Ketua Tim')
            ->where('pegawais.is_active', true)
            ->orderBy('pegawais.nama_pegawai')
            ->get([
                'pegawais.id_pegawai',
                'pegawais.nama_pegawai',
                'roles.nama_role',
            ]);

        return view('pages.main.pegawai.tagihan-kerja.index', [
            'title'     => $bidang->detail_bidang,
            'bidang'    => $bidang,
            'kegiatans' => $kegiatans,
            // 'subKegiatans' => $subKegiatans,
            'pegawais'  => $pegawais,
            'rkJpts'    => $rkJpts,
            'ketuaTims' => $ketuaTims
        ]);
    }

    public function store(Request $request, Bidang $bidang)
    {
        // dd($request->all());
        $this->authorize('create', Kegiatan::class);

        $user = $request->user();
        if ($user?->isKetuaTim()) {
            $request->merge([
                'id_penanggung_jawab' => $user->id_pegawai,
            ]);
        }

        $validated = $request->validate([
            'nama_rk_kegiatan' => ['required', 'string', 'max:255'],
            'rk_jpt' => ['required', 'exists:rencana_jpts,id'],
            'iki_jpt' => [
                'required',
                Rule::exists('indikator_jpts', 'id')
                    ->where('id_rencana_jpt', $request->rk_jpt),
            ],
            'id_penanggung_jawab' => [
                'required',
                Rule::exists('pegawais', 'id_pegawai')->where('is_active', true),
            ],
            'tahun_kegiatan' => ['required'],
        ]);

        // 🔐 PAKSA id_bidang dari route
        $validated['id_bidang'] = $bidang->id_bidang;

        try {
            // Simpan
            Kegiatan::create($validated);

            // Redirect dengan flash message
            return redirect()
                ->route('kegiatan.index', $bidang->slug)
                ->with('success', 'Kegiatan berhasil ditambahkan.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Kegiatan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        // dd($request->all());
        $this->authorize('update', $kegiatan);

        $user = $request->user();
        if ($user?->isKetuaTim()) {
            $request->merge([
                'id_penanggung_jawab' => $user->id_pegawai,
            ]);
        }

        $penanggungJawabRule = (string) $request->id_penanggung_jawab === (string) $kegiatan->id_penanggung_jawab
            ? Rule::exists('pegawais', 'id_pegawai')
            : Rule::exists('pegawais', 'id_pegawai')->where('is_active', true);

        // ✅ Validasi
        $validated = $request->validate([
            'nama_rk_kegiatan' => ['required', 'string', 'max:255'],
            'rk_jpt' => ['required', 'exists:rencana_jpts,id'],
            'iki_jpt' => [
                'required',
                Rule::exists('indikator_jpts', 'id')
                    ->where('id_rencana_jpt', $request->rk_jpt),
            ],
            'id_penanggung_jawab' => ['required', $penanggungJawabRule],
            'tahun_kegiatan' => ['required'],
        ]);

        // 🔐 PERTAHANKAN id_bidang (tidak boleh diubah lewat form)
        $validated['id_bidang'] = $kegiatan->id_bidang;

        try {
            // 🔄 Update data
            $kegiatan->update($validated);

            return redirect()
                ->route('kegiatan.index', $kegiatan->bidang->slug)
                ->with('success', 'Kegiatan berhasil diperbarui.');
        } catch (\Exception $e) {
            dd($e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memperbarui Kegiatan. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function delete(Kegiatan $kegiatan)
    {
        $this->authorize('delete', $kegiatan);

        try {
            $kegiatanForNotification = $kegiatan->loadMissing([
                'penanggungJawab',
                'subKegiatans.penugasans.anggota',
            ]);

            $kegiatan->forceDelete();

            app(PushNotificationService::class)->notifyKegiatanDeleted($kegiatanForNotification);

            return redirect()
                ->route('dashboard')
                ->with('success', 'Kegiatan berhasil dihapus.');
        } catch (\Exception $e) {
            dd($e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal menghapus Kegiatan. Silakan coba lagi.');
        }
    }

    public function exportMph(Bidang $bidang)
    {
        $pegawai = Auth::user();

        $this->authorize('viewAny', Kegiatan::class);

        return Excel::download(
            new MphExport($bidang, $pegawai),
            'MPH_' . $bidang->detail_bidang . '_2026.xlsx'
        );
    }
}
