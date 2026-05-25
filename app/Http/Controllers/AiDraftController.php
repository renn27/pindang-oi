<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\IndikatorJPT;
use App\Models\JenisKegiatan;
use App\Models\Kegiatan;
use App\Models\Pegawai;
use App\Models\RencanaJPT;
use App\Models\SubKegiatan;
use App\Services\AiDraftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AiDraftController extends Controller
{
    public function __invoke(Request $request, AiDraftService $aiDraft): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['auto', 'kegiatan', 'sub_kegiatan', 'penugasan'])],
            'prompt' => ['required', 'string', 'max:2000'],
            'bidang_slug' => ['nullable', 'string'],
            'kegiatan_id' => ['nullable', 'string'],
            'sub_kegiatan_id' => ['nullable', 'string'],
            'history' => ['nullable', 'array', 'max:12'],
            'history.*.role' => ['required_with:history', Rule::in(['user', 'assistant'])],
            'history.*.text' => ['required_with:history', 'string', 'max:1200'],
            'draft_state' => ['nullable', 'array'],
        ]);

        $context = $this->buildContext($request, $validated);

        return response()->json([
            'ok' => true,
            'result' => $aiDraft->draft($validated['prompt'], $context),
            'context' => [
                'type' => $context['type'],
                'bidang' => $context['bidang'],
                'kegiatan' => $context['kegiatan'],
                'sub_kegiatan' => $context['sub_kegiatan'],
            ],
        ]);
    }

    private function buildContext(Request $request, array $validated): array
    {
        $bidang = ! empty($validated['bidang_slug'])
            ? Bidang::where('slug', $validated['bidang_slug'])->first()
            : null;

        $kegiatan = ! empty($validated['kegiatan_id'])
            ? Kegiatan::with(['bidang', 'penanggungJawab'])->find($validated['kegiatan_id'])
            : null;

        $subKegiatan = ! empty($validated['sub_kegiatan_id'])
            ? SubKegiatan::with([
                'kegiatan.bidang',
                'kegiatan.penanggungJawab',
                'penugasans.anggota:id_pegawai,nama_pegawai',
                'penugasans.jenisKegiatan:id,jenis_kegiatan,kategori,butuh_dl_atau_translok',
            ])->find($validated['sub_kegiatan_id'])
            : null;

        if ($subKegiatan && ! $kegiatan) {
            $kegiatan = $subKegiatan->kegiatan;
        }

        if ($kegiatan && ! $bidang) {
            $bidang = $kegiatan->bidang;
        }

        $kegiatanChoices = Kegiatan::query()
            ->when($bidang, fn ($query) => $query->where('id_bidang', $bidang->id_bidang))
            ->when($request->user()?->isKetuaTim(), fn ($query) => $query->where('id_penanggung_jawab', $request->user()->id_pegawai))
            ->orderBy('nama_rk_kegiatan')
            ->limit(50)
            ->get(['id_kegiatan', 'nama_rk_kegiatan', 'tahun_kegiatan']);

        $subKegiatanChoices = SubKegiatan::query()
            ->with('kegiatan:id_kegiatan,nama_rk_kegiatan')
            ->when($kegiatan, fn ($query) => $query->where('id_kegiatan', $kegiatan->id_kegiatan))
            ->when(! $kegiatan && $bidang, fn ($query) => $query->whereHas('kegiatan', fn ($k) => $k->where('id_bidang', $bidang->id_bidang)))
            ->orderBy('nama_sub_kegiatan')
            ->limit(80)
            ->get(['id_sub_kegiatan', 'id_kegiatan', 'nama_sub_kegiatan', 'target', 'satuan_target', 'tanggal_mulai', 'tanggal_selesai']);

        $ketuaTims = Pegawai::query()
            ->join('pegawai_role', 'pegawais.id_pegawai', '=', 'pegawai_role.pegawai_id')
            ->join('roles', 'pegawai_role.role_id', '=', 'roles.id')
            ->where('roles.nama_role', 'Ketua Tim')
            ->where('pegawais.is_active', true)
            ->when($request->user()?->isKetuaTim(), fn ($query) => $query->where('pegawais.id_pegawai', $request->user()->id_pegawai))
            ->orderBy('pegawais.nama_pegawai')
            ->get(['pegawais.id_pegawai', 'pegawais.nama_pegawai'])
            ->map(fn ($pegawai) => [
                'id_pegawai' => $pegawai->id_pegawai,
                'nama_pegawai' => $pegawai->nama_pegawai,
            ])
            ->values()
            ->all();

        return [
            'type' => $validated['type'],
            'history' => collect($validated['history'] ?? [])
                ->take(-10)
                ->map(fn ($message) => [
                    'role' => $message['role'],
                    'text' => $message['text'],
                ])
                ->values()
                ->all(),
            'draft_state' => $this->sanitizeDraftState($validated['draft_state'] ?? []),
            'user' => [
                'id_pegawai' => $request->user()?->id_pegawai,
                'nama_pegawai' => $request->user()?->nama_pegawai,
                'active_role' => $request->user()?->active_role,
            ],
            'bidang' => $bidang ? [
                'id_bidang' => $bidang->id_bidang,
                'slug' => $bidang->slug,
                'detail_bidang' => $bidang->detail_bidang,
            ] : null,
            'kegiatan' => $kegiatan ? [
                'id_kegiatan' => $kegiatan->id_kegiatan,
                'nama_rk_kegiatan' => $kegiatan->nama_rk_kegiatan,
                'tahun_kegiatan' => $kegiatan->tahun_kegiatan,
                'id_penanggung_jawab' => $kegiatan->id_penanggung_jawab,
                'penanggung_jawab' => $kegiatan->penanggungJawab?->nama_pegawai,
            ] : null,
            'sub_kegiatan' => $subKegiatan ? [
                'id_sub_kegiatan' => $subKegiatan->id_sub_kegiatan,
                'id_kegiatan' => $subKegiatan->id_kegiatan,
                'nama_sub_kegiatan' => $subKegiatan->nama_sub_kegiatan,
                'target' => $subKegiatan->target,
                'satuan_target' => $subKegiatan->satuan_target,
                'tanggal_mulai' => optional($subKegiatan->tanggal_mulai)->format('Y-m-d'),
                'tanggal_selesai' => optional($subKegiatan->tanggal_selesai)->format('Y-m-d'),
                'penugasan_aktif' => $subKegiatan->penugasans
                    ->take(25)
                    ->map(fn ($penugasan) => [
                        'id_penugasan' => $penugasan->id_penugasan,
                        'anggota' => $penugasan->anggota?->nama_pegawai,
                        'jenis_kegiatan' => $penugasan->jenisKegiatan?->jenis_kegiatan,
                        'target' => $penugasan->target,
                        'satuan_target' => $penugasan->satuan_target,
                        'tanggal_mulai' => optional($penugasan->tanggal_mulai)->format('Y-m-d'),
                        'tanggal_selesai' => optional($penugasan->tanggal_selesai)->format('Y-m-d'),
                        'butuh_dl' => (bool) $penugasan->butuh_dl,
                        'butuh_translok' => (bool) $penugasan->butuh_translok,
                    ])
                    ->values()
                    ->all(),
            ] : null,
            'pegawais' => Pegawai::active()->orderBy('nama_pegawai')
                ->limit(120)
                ->get(['id_pegawai', 'nama_pegawai'])
                ->map(fn ($pegawai) => [
                    'id_pegawai' => $pegawai->id_pegawai,
                    'nama_pegawai' => $pegawai->nama_pegawai,
                ])
                ->values()
                ->all(),
            'jenis_kegiatans' => JenisKegiatan::orderByRaw("
                    CASE
                    WHEN kategori = 'Utama' THEN 1
                    WHEN kategori = 'Tambahan' THEN 2
                    ELSE 3
                    END
                ")
                ->orderBy('jenis_kegiatan')
                ->get(['id', 'jenis_kegiatan', 'kategori', 'butuh_dl_atau_translok'])
                ->map(fn ($jenis) => [
                    'id' => $jenis->id,
                    'jenis_kegiatan' => $jenis->jenis_kegiatan,
                    'kategori' => $jenis->kategori,
                    'butuh_dl_atau_translok' => (bool) $jenis->butuh_dl_atau_translok,
                ])
                ->values()
                ->all(),
            'kegiatans' => $kegiatanChoices->map(fn ($item) => [
                'id_kegiatan' => $item->id_kegiatan,
                'nama_rk_kegiatan' => $item->nama_rk_kegiatan,
                'tahun_kegiatan' => $item->tahun_kegiatan,
            ])->values()->all(),
            'sub_kegiatans' => $subKegiatanChoices->map(fn ($item) => [
                'id_sub_kegiatan' => $item->id_sub_kegiatan,
                'id_kegiatan' => $item->id_kegiatan,
                'nama_sub_kegiatan' => $item->nama_sub_kegiatan,
                'nama_rk_kegiatan' => $item->kegiatan?->nama_rk_kegiatan,
                'target' => $item->target,
                'satuan_target' => $item->satuan_target,
                'tanggal_mulai' => optional($item->tanggal_mulai)->format('Y-m-d'),
                'tanggal_selesai' => optional($item->tanggal_selesai)->format('Y-m-d'),
            ])->values()->all(),
            'rencana_jpts' => RencanaJPT::orderBy('nama_rencana_jpt')
                ->limit(50)
                ->get(['id', 'nama_rencana_jpt'])
                ->values()
                ->all(),
            'indikator_jpts' => IndikatorJPT::orderBy('nama_indikator_jpt')
                ->limit(100)
                ->get(['id', 'id_rencana_jpt', 'nama_indikator_jpt'])
                ->values()
                ->all(),
            'ketua_tims' => $ketuaTims,
        ];
    }

    private function sanitizeDraftState(array $state): array
    {
        $allowedDraftKeys = [
            'nama_rk_kegiatan',
            'tahun_kegiatan',
            'rk_jpt',
            'rk_jpt_label',
            'iki_jpt',
            'iki_jpt_label',
            'id_penanggung_jawab',
            'nama_penanggung_jawab',
            'nama_sub_kegiatan',
            'target',
            'satuan_target',
            'tanggal_mulai',
            'tanggal_selesai',
            'id_anggota',
            'nama_anggota',
            'id_jenis_kegiatan',
            'jenis_kegiatan',
            'jenis_kegiatan_baru',
            'butuh_dl',
            'butuh_translok',
            'tanggal_tambahan',
        ];

        $sanitizeDraft = function ($draft) use ($allowedDraftKeys) {
            if (! is_array($draft)) {
                return [];
            }

            return collect($draft)
                ->only($allowedDraftKeys)
                ->map(function ($value) {
                    if (is_array($value)) {
                        return array_slice($value, 0, 12);
                    }

                    return is_scalar($value) ? mb_substr((string) $value, 0, 300) : null;
                })
                ->filter(fn ($value) => $value !== null && $value !== '')
                ->all();
        };

        $items = collect($state['items'] ?? [])
            ->take(30)
            ->filter(fn ($item) => is_array($item))
            ->map(fn ($item) => [
                'target_form' => in_array(($item['target_form'] ?? null), ['kegiatan', 'sub_kegiatan', 'penugasan', null], true) ? ($item['target_form'] ?? null) : null,
                'summary' => is_scalar($item['summary'] ?? null) ? mb_substr((string) $item['summary'], 0, 300) : '',
                'draft' => $sanitizeDraft($item['draft'] ?? []),
                'missing_fields' => array_values(array_filter(array_slice((array) ($item['missing_fields'] ?? []), 0, 20), 'is_scalar')),
            ])
            ->values()
            ->all();

        return [
            'active_index' => max(0, (int) ($state['active_index'] ?? 0)),
            'active_target_form' => in_array(($state['active_target_form'] ?? null), ['kegiatan', 'sub_kegiatan', 'penugasan', null], true) ? ($state['active_target_form'] ?? null) : null,
            'active_draft' => $sanitizeDraft($state['active_draft'] ?? []),
            'items' => $items,
        ];
    }
}
