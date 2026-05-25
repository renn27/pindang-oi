<?php

namespace App\Services;

use App\Models\JenisKegiatan;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiDraftService
{
    public function draft(string $prompt, array $context): array
    {
        $prompt = trim($prompt);
        $context['_prompt'] = $prompt;

        if ($prompt === '') {
            return $this->emptyResult('Tulis instruksi yang ingin dibuat terlebih dahulu.');
        }

        $result = $this->requestGemini($prompt, $context);

        if (! $result) {
            $result = $this->fallbackDraft($prompt, $context);
        }

        return $this->normalizeResult($result, $context);
    }

    private function requestGemini(string $prompt, array $context): ?array
    {
        $apiKey = config('services.ai.gemini.api_key');

        if (! $apiKey) {
            return null;
        }

        $model = config('services.ai.gemini.model', 'gemini-2.5-flash-lite');
        $endpoint = rtrim(config('services.ai.gemini.endpoint'), '/');
        $url = "{$endpoint}/models/{$model}:generateContent";

        try {
            $http = Http::timeout(config('services.ai.timeout', 20))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->when(! filter_var(config('services.ai.gemini.ssl_verify', true), FILTER_VALIDATE_BOOLEAN), fn ($client) => $client->withoutVerifying());

            $response = $http->post($url . '?key=' . $apiKey, [
                    'generationConfig' => [
                        'temperature' => 0.15,
                        'responseMimeType' => 'application/json',
                    ],
                    'contents' => [[
                        'role' => 'user',
                        'parts' => [[
                            'text' => $this->buildSystemPrompt($prompt, $context),
                        ]],
                    ]],
                ]);

            if (! $response->successful()) {
                Log::warning('Gemini draft request failed', [
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 500),
                ]);

                return null;
            }

            $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
            if (! is_string($text) || trim($text) === '') {
                return null;
            }

            $decoded = json_decode($text, true);
            return is_array($decoded) ? $decoded : null;
        } catch (\Throwable $e) {
            Log::warning('Gemini draft exception: ' . $this->maskApiKey($e->getMessage()));

            return null;
        }
    }

    private function maskApiKey(string $message): string
    {
        return preg_replace('/([?&]key=)[^\\s&]+/i', '$1***', $message) ?? $message;
    }

    private function buildSystemPrompt(string $prompt, array $context): string
    {
        $compactContext = [
            'now' => now('Asia/Jakarta')->format('Y-m-d'),
            'timezone' => 'Asia/Jakarta',
            'requested_type' => $context['type'] ?? 'auto',
            'chat_history' => $context['history'] ?? [],
            'current_draft_state' => $context['draft_state'] ?? [],
            'user' => $context['user'] ?? null,
            'form_rules' => $this->formRules(),
            'prompt_examples' => $this->promptExamples(),
            'current_bidang' => $context['bidang'] ?? null,
            'current_kegiatan' => $context['kegiatan'] ?? null,
            'current_sub_kegiatan' => $context['sub_kegiatan'] ?? null,
            'allowed_pegawais' => $context['pegawais'] ?? [],
            'allowed_jenis_kegiatan' => $context['jenis_kegiatans'] ?? [],
            'allowed_kegiatans' => $context['kegiatans'] ?? [],
            'allowed_sub_kegiatans' => $context['sub_kegiatans'] ?? [],
            'allowed_ketua_tims' => $context['ketua_tims'] ?? [],
            'allowed_rencana_jpts' => $context['rencana_jpts'] ?? [],
            'allowed_indikator_jpts' => $context['indikator_jpts'] ?? [],
        ];

        return <<<'PROMPT'
Kamu adalah AI assistant untuk aplikasi tagihan kerja internal.
Tugasmu hanya membuat DRAFT FORM dari instruksi user. Kamu TIDAK BOLEH menyimpan data, mengubah status, menyetujui, menghapus, atau mengambil keputusan final.
Kamu juga boleh mengubah draft yang sedang ada di current_draft_state, tetapi hasilnya tetap berupa draft JSON, bukan aksi simpan.

Pahami instruksi bebas user dalam Bahasa Indonesia yang bisa tidak rapi, singkat, typo, atau ambigu.
Jika ambigu, tetap isi field yang bisa dipahami dan berikan choices/missing_fields agar UI bisa menampilkan pilihan.
Jangan mengarang ID. ID hanya boleh berasal dari daftar allowed_*.
Tanggal harus format YYYY-MM-DD. Jika user memakai "besok", "minggu depan", atau nama bulan, tafsirkan berdasarkan now dan timezone.
Jika tanggal penugasan berada di luar rentang current_sub_kegiatan, beri warning.
Jika tidak yakin kegiatan/sub kegiatan/anggota/jenis kegiatan mana, isi null dan buat choices.
Gunakan chat_history untuk memahami revisi dan referensi seperti "yang tadi", "draft itu", "sisanya sama", "ganti", "ubah", "tambahkan".
Jika prompt terbaru adalah revisi, pertahankan field draft yang masih relevan dari konteks percakapan dan ubah hanya bagian yang diminta user.
Gunakan current_draft_state sebagai sumber kebenaran draft yang sedang terlihat di UI, termasuk edit manual user.
Jika user berkata "isi info yang sama", "samakan", "buat sisanya sama", "untuk pegawai lainnya", atau makna serupa:
- cari draft penugasan yang paling lengkap di current_draft_state.items atau current_draft_state.active_draft sebagai template.
- salin semua field non-identitas dari template ke draft penugasan lain: id_jenis_kegiatan, jenis_kegiatan, jenis_kegiatan_baru, target, satuan_target, tanggal_mulai, tanggal_selesai, butuh_dl, butuh_translok, tanggal_tambahan.
- jangan menimpa identitas anggota tiap item: id_anggota dan nama_anggota harus tetap sesuai pegawai masing-masing.
- kembalikan items yang sudah lengkap, bukan membuat satu form baru dari nol.
- jika pegawai lain belum jelas, buat choices id_anggota untuk item yang belum punya pegawai.
Jika user berkata "ubah semua", "ganti semua", "aktifkan semua", atau "matikan semua", terapkan perubahan ke semua items yang relevan.
Jika user tidak menyebut "semua", perubahan hanya berlaku ke current_draft_state.active_index.
Jika user meminta mengubah target/tanggal/satuan/DL/Translok, jangan membuat form baru. Perbarui draft yang sudah ada.
Jika user menyebut nama orang sebagian saja, cocokkan ke allowed_pegawais/allowed_ketua_tims berdasarkan token nama. Jika lebih dari satu kandidat kuat, buat choices.
Jika user menyebut "saya", "aku", "sendiri", atau "yang login", gunakan user.id_pegawai untuk anggota/penanggung jawab bila formnya relevan.
Anggap AI ini bersifat global. Jangan bergantung pada posisi tombol. Jika current_kegiatan/current_sub_kegiatan kosong dan prompt membutuhkan konteks, wajib buat choices untuk memilih id_kegiatan atau id_sub_kegiatan.
Jika current_sub_kegiatan ada tetapi prompt jelas meminta membuat sub kegiatan baru, jangan paksa jadi penugasan; target_form harus sub_kegiatan.
Jika current_kegiatan ada tetapi prompt jelas meminta membuat kegiatan utama baru, target_form harus kegiatan.
Jika prompt berisi kata tugaskan/anggota/pegawai/nama orang + tanggal/target/jenis, biasanya target_form adalah penugasan.
Jika prompt berisi tambah/buat sub kegiatan/RK anggota/nama pekerjaan dengan target dan rentang tanggal, biasanya target_form adalah sub_kegiatan.
Jika prompt berisi tambah/buat kegiatan/RK ketua/kegiatan utama/tahun/RK JPT/IKI JPT, biasanya target_form adalah kegiatan.
Jika user meminta lebih dari satu data, pecah menjadi beberapa draft di field items.
Contoh multi data: beberapa nama pegawai, beberapa jenis tugas, beberapa sub kegiatan, atau instruksi "buat kegiatan beserta sub kegiatan dan penugasan".
Untuk alur bertingkat, buat urutan items yang logis: kegiatan lebih dulu, lalu sub_kegiatan, lalu penugasan. Jika sub kegiatan/penugasan bergantung pada data yang belum tersimpan, isi konteks yang tersedia dan beri missing_fields/choices yang relevan.

Jenis form yang boleh:
1. kegiatan: nama_rk_kegiatan, tahun_kegiatan, rk_jpt, iki_jpt, id_penanggung_jawab
2. sub_kegiatan: nama_sub_kegiatan, target, satuan_target, tanggal_mulai, tanggal_selesai
3. penugasan: id_anggota, id_jenis_kegiatan, jenis_kegiatan_baru, target, satuan_target, tanggal_mulai, tanggal_selesai, butuh_dl, butuh_translok, tanggal_tambahan

Khusus kegiatan:
- Jika user active_role adalah Ketua Tim, jangan minta id_penanggung_jawab dan jangan menebak penanggung jawab. Sistem akan otomatis memakai user login saat submit.
- Untuk Admin/Pimpinan, id_penanggung_jawab wajib dipilih dari allowed_ketua_tims.
- rk_jpt harus dipilih dari allowed_rencana_jpts.
- iki_jpt harus dipilih dari allowed_indikator_jpts dan harus punya id_rencana_jpt yang sama dengan rk_jpt. Jika rk_jpt belum jelas, jangan pilih iki_jpt dulu.

Khusus sub_kegiatan:
- id_kegiatan tidak masuk draft, tetapi harus jelas lewat current_kegiatan atau choices id_kegiatan.
- status tidak boleh diisi; backend otomatis membuat status Berjalan.
- target integer minimal 1. tanggal_selesai harus setelah/sama dengan tanggal_mulai.

Output WAJIB JSON valid dengan bentuk:
{
  "intent": "create_kegiatan|create_sub_kegiatan|create_penugasan|clarify",
  "target_form": "kegiatan|sub_kegiatan|penugasan|null",
  "summary": "ringkasan singkat",
  "draft": {},
  "choices": [
    {
      "field": "id_anggota|id_jenis_kegiatan|id_kegiatan|id_sub_kegiatan|target_form|rk_jpt|iki_jpt|id_penanggung_jawab",
      "label": "label pilihan",
      "options": [{"value": "id atau value", "label": "teks", "reason": "alasan singkat"}]
    }
  ],
  "missing_fields": ["field_yang_belum_jelas"],
  "warnings": ["peringatan validasi/ambiguitas"],
  "confidence": 0.0,
  "follow_up_question": "pertanyaan lanjutan jika perlu",
  "items": [
    {
      "intent": "create_kegiatan|create_sub_kegiatan|create_penugasan|clarify",
      "target_form": "kegiatan|sub_kegiatan|penugasan|null",
      "summary": "ringkasan item",
      "draft": {},
      "choices": [],
      "missing_fields": [],
      "warnings": [],
      "confidence": 0.0,
      "follow_up_question": ""
    }
  ]
}

Jika hanya satu draft, items boleh kosong atau tidak diisi. Jika lebih dari satu draft, isi items dan tetap isi summary top-level.

Khusus penugasan:
- Jika jenis kegiatan allowed punya butuh_dl_atau_translok true dan user menyebut dinas luar/DL/perjalanan, set butuh_dl=1, butuh_translok=0 kecuali user jelas menyebut translok.
- Jika jenis kegiatan allowed punya butuh_dl_atau_translok true tapi user tidak menyebut DL/translok, default butuh_dl=1 dan butuh_translok=0 karena backend juga akan default ke DL.
- Jika jenis kegiatan tidak butuh_dl_atau_translok, set butuh_dl=0 dan butuh_translok=0 meskipun user tidak menyebut.
- Jika jenis kegiatan tidak ada di daftar dan user jelas menulis nama jenis baru, isi id_jenis_kegiatan="LAINNYA" dan jenis_kegiatan_baru dengan nama tersebut.
- Jika user menyebut beberapa tanggal, gunakan tanggal_mulai/tanggal_selesai pertama sebagai tanggal utama dan sisanya masukkan ke tanggal_tambahan: [{"tanggal_mulai":"YYYY-MM-DD","tanggal_selesai":"YYYY-MM-DD"}].
- tanggal penugasan harus berada dalam current_sub_kegiatan.tanggal_mulai sampai current_sub_kegiatan.tanggal_selesai. Jika tidak ada current_sub_kegiatan, jangan mengarang batas tanggal.
- Jangan isi status, status_dl, status_translok.
- target integer minimal 1. satuan_target maksimal 50 karakter.

Contoh pola:
- "tugaskan Budi supervisi 10-12 Mei target 1 laporan butuh DL" => penugasan, cari Budi di allowed_pegawais, cari supervisi di allowed_jenis_kegiatan, tanggal dari now.
- "buat sub kegiatan pengolahan data target 10 dokumen 1 juni sampai 30 juni" => sub_kegiatan.
- "buat kegiatan SNLIK tahun 2026" => kegiatan, minta/pilih RK JPT dan IKI JPT.

CONTEXT:
PROMPT
        . json_encode($compactContext, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        . "\n\nUSER_PROMPT:\n"
        . $prompt;
    }

    private function fallbackDraft(string $prompt, array $context): array
    {
        $type = $this->guessType($prompt, $context['type'] ?? 'auto');
        $draft = [];
        $missing = [];
        $warnings = ['AI provider belum aktif atau sedang gagal, draft dibuat dengan parser sederhana.'];

        if ($type === 'penugasan') {
            $pegawai = $this->bestMatch($prompt, $context['pegawais'] ?? [], 'nama_pegawai', 'id_pegawai');
            $jenis = $this->bestMatch($prompt, $context['jenis_kegiatans'] ?? [], 'jenis_kegiatan', 'id');
            $dates = $this->extractDateRanges($prompt);

            $draft = [
                'id_anggota' => $pegawai['value'] ?? null,
                'nama_anggota' => $pegawai['label'] ?? null,
                'id_jenis_kegiatan' => $jenis['value'] ?? null,
                'jenis_kegiatan' => $jenis['label'] ?? null,
                'target' => $this->extractNumberNear($prompt, ['target']) ?? null,
                'satuan_target' => $this->extractSatuan($prompt),
                'tanggal_mulai' => $dates[0]['tanggal_mulai'] ?? null,
                'tanggal_selesai' => $dates[0]['tanggal_selesai'] ?? null,
                'butuh_dl' => Str::contains(Str::lower($prompt), ['dl', 'dinas luar', 'perjalanan dinas']) ? 1 : 0,
                'butuh_translok' => Str::contains(Str::lower($prompt), ['translok']) ? 1 : 0,
                'tanggal_tambahan' => array_slice($dates, 1),
            ];

            foreach (['id_anggota', 'id_jenis_kegiatan', 'target', 'satuan_target', 'tanggal_mulai', 'tanggal_selesai'] as $field) {
                if (empty($draft[$field])) {
                    $missing[] = $field;
                }
            }
        } elseif ($type === 'sub_kegiatan') {
            $dates = $this->extractDateRanges($prompt);
            $draft = [
                'nama_sub_kegiatan' => $this->extractName($prompt),
                'target' => $this->extractNumberNear($prompt, ['target']) ?? null,
                'satuan_target' => $this->extractSatuan($prompt),
                'tanggal_mulai' => $dates[0]['tanggal_mulai'] ?? null,
                'tanggal_selesai' => $dates[0]['tanggal_selesai'] ?? null,
            ];

            foreach (['nama_sub_kegiatan', 'target', 'satuan_target', 'tanggal_mulai', 'tanggal_selesai'] as $field) {
                if (empty($draft[$field])) {
                    $missing[] = $field;
                }
            }
        } else {
            $draft = [
                'nama_rk_kegiatan' => $this->extractName($prompt),
                'tahun_kegiatan' => $this->extractYear($prompt) ?? now()->year,
                'rk_jpt' => null,
                'iki_jpt' => null,
                'id_penanggung_jawab' => null,
            ];
            $missing = ['rk_jpt', 'iki_jpt'];
        }

        return [
            'intent' => 'create_' . $type,
            'target_form' => $type,
            'summary' => 'Draft dibuat dari instruksi user.',
            'draft' => $draft,
            'choices' => [],
            'missing_fields' => $missing,
            'warnings' => $warnings,
            'confidence' => empty($missing) ? 0.65 : 0.35,
            'follow_up_question' => empty($missing) ? '' : 'Lengkapi field yang belum jelas sebelum menyimpan.',
        ];
    }

    private function normalizeResult(array $result, array $context): array
    {
        $operationResult = $this->buildDeterministicOperationResult($context);
        if ($operationResult) {
            $normalizedItems = array_values(array_map(
                fn ($item) => $this->normalizeSingleResult($item, $context),
                $operationResult['items']
            ));
            $first = $normalizedItems[0];

            return [
                ...$first,
                'summary' => $operationResult['summary'],
                'follow_up_question' => '',
                'items' => $normalizedItems,
                'item_count' => count($normalizedItems),
            ];
        }

        $items = $this->extractResultItems($result);

        if ($items !== []) {
            $normalizedItems = array_values(array_map(
                fn ($item) => $this->normalizeSingleResult($item, $context),
                $items
            ));
            $first = $normalizedItems[0];

            return [
                ...$first,
                'summary' => (string) ($result['summary'] ?? $first['summary'] ?? 'Beberapa draft berhasil dibuat.'),
                'warnings' => array_values(array_unique(array_merge((array) ($result['warnings'] ?? []), $first['warnings'] ?? []))),
                'follow_up_question' => (string) ($result['follow_up_question'] ?? $first['follow_up_question'] ?? ''),
                'items' => $normalizedItems,
                'item_count' => count($normalizedItems),
            ];
        }

        return [
            ...$this->normalizeSingleResult($result, $context),
            'items' => [],
            'item_count' => 1,
        ];
    }

    private function buildDeterministicOperationResult(array $context): ?array
    {
        $state = $context['draft_state'] ?? [];
        $items = array_values(array_filter((array) ($state['items'] ?? []), fn ($item) => is_array($item)));
        if ($items === []) {
            return null;
        }

        if ($this->shouldFillOtherDraftsFromTemplate($context)) {
            $filledItems = $this->fillOtherDraftsFromTemplate($context);
            if ($filledItems !== []) {
                return [
                    'summary' => 'Draft penugasan lain sudah disamakan dengan informasi dari draft yang lengkap.',
                    'items' => $filledItems,
                ];
            }
        }

        $updates = $this->extractDraftUpdatesFromPrompt((string) ($context['_prompt'] ?? ''));
        if ($updates === []) {
            return null;
        }

        $applyAll = $this->shouldApplyToAllDrafts((string) ($context['_prompt'] ?? ''));
        $activeIndex = max(0, (int) ($state['active_index'] ?? 0));

        $updatedItems = array_map(function ($item, $index) use ($updates, $applyAll, $activeIndex) {
            $targetForm = $item['target_form'] ?? null;
            $draft = is_array($item['draft'] ?? null) ? $item['draft'] : [];
            $isTarget = $applyAll
                ? in_array($targetForm, ['penugasan', 'sub_kegiatan'], true)
                : $index === $activeIndex;

            if (! $isTarget) {
                return $item;
            }

            foreach ($updates as $key => $value) {
                $draft[$key] = $value;
            }

            return [
                ...$item,
                'draft' => $draft,
                'summary' => $item['summary'] ?? 'Draft diperbarui.',
            ];
        }, $items, array_keys($items));

        return [
            'summary' => $applyAll ? 'Semua draft terkait sudah diperbarui sesuai instruksi.' : 'Draft aktif sudah diperbarui sesuai instruksi.',
            'items' => $updatedItems,
        ];
    }

    private function shouldApplyToAllDrafts(string $prompt): bool
    {
        $prompt = Str::lower($prompt);

        return Str::contains($prompt, [
            'semua',
            'seluruh',
            'semuanya',
            'semua draft',
            'yang lain',
            'lainnya',
            'pegawai lainnya',
            'anggota lainnya',
        ]);
    }

    private function extractDraftUpdatesFromPrompt(string $prompt): array
    {
        $lower = Str::lower($prompt);
        if (! Str::contains($lower, ['ubah', 'ganti', 'set ', 'jadikan', 'aktifkan', 'matikan', 'target', 'tanggal', 'satuan', 'dl', 'translok'])) {
            return [];
        }

        $updates = [];
        $target = $this->extractNumberNear($prompt, ['target']);
        if ($target !== null) {
            $updates['target'] = $target;
        }

        $satuan = $this->extractSatuan($prompt);
        if ($satuan) {
            $updates['satuan_target'] = $satuan;
        }

        $dates = $this->extractDateRanges($prompt);
        if ($dates !== []) {
            $updates['tanggal_mulai'] = $dates[0]['tanggal_mulai'];
            $updates['tanggal_selesai'] = $dates[0]['tanggal_selesai'];
            if (count($dates) > 1) {
                $updates['tanggal_tambahan'] = array_slice($dates, 1);
            }
        }

        if (Str::contains($lower, ['aktifkan dl', 'butuh dl', 'pakai dl', 'dinas luar'])) {
            $updates['butuh_dl'] = 1;
            $updates['butuh_translok'] = 0;
        }

        if (Str::contains($lower, ['aktifkan translok', 'butuh translok', 'pakai translok'])) {
            $updates['butuh_dl'] = 0;
            $updates['butuh_translok'] = 1;
        }

        if (Str::contains($lower, ['matikan dl', 'tidak dl', 'tanpa dl'])) {
            $updates['butuh_dl'] = 0;
        }

        if (Str::contains($lower, ['matikan translok', 'tidak translok', 'tanpa translok'])) {
            $updates['butuh_translok'] = 0;
        }

        return $updates;
    }

    private function shouldFillOtherDraftsFromTemplate(array $context): bool
    {
        $prompt = Str::lower((string) ($context['_prompt'] ?? ''));

        return Str::contains($prompt, [
            'info yang sama',
            'informasi yang sama',
            'samakan',
            'sama seperti',
            'sisanya sama',
            'pegawai lainnya',
            'anggota lainnya',
            'yang lain',
            'lainnya sama',
        ]);
    }

    private function fillOtherDraftsFromTemplate(array $context): array
    {
        $state = $context['draft_state'] ?? [];
        $items = array_values(array_filter((array) ($state['items'] ?? []), fn ($item) => is_array($item)));

        if (count($items) < 2) {
            return [];
        }

        $template = $this->findBestPenugasanTemplate($items, (array) ($state['active_draft'] ?? []));
        if ($template === []) {
            return [];
        }

        $copyKeys = [
            'id_jenis_kegiatan',
            'jenis_kegiatan',
            'jenis_kegiatan_baru',
            'target',
            'satuan_target',
            'tanggal_mulai',
            'tanggal_selesai',
            'butuh_dl',
            'butuh_translok',
            'tanggal_tambahan',
        ];

        return array_map(function ($item) use ($template, $copyKeys) {
            $targetForm = $item['target_form'] ?? 'penugasan';
            $draft = is_array($item['draft'] ?? null) ? $item['draft'] : [];

            if ($targetForm !== 'penugasan') {
                return $item;
            }

            foreach ($copyKeys as $key) {
                if (array_key_exists($key, $template) && $template[$key] !== null && $template[$key] !== '') {
                    $draft[$key] = $template[$key];
                }
            }

            return [
                'intent' => 'create_penugasan',
                'target_form' => 'penugasan',
                'summary' => $item['summary'] ?? 'Draft penugasan disamakan.',
                'draft' => $draft,
                'choices' => $item['choices'] ?? [],
                'missing_fields' => $item['missing_fields'] ?? [],
                'warnings' => $item['warnings'] ?? [],
                'confidence' => max(0.75, (float) ($item['confidence'] ?? 0)),
                'follow_up_question' => '',
            ];
        }, $items);
    }

    private function findBestPenugasanTemplate(array $items, array $activeDraft): array
    {
        $candidates = [];
        if ($activeDraft !== []) {
            $candidates[] = $activeDraft;
        }

        foreach ($items as $item) {
            if (($item['target_form'] ?? null) !== 'penugasan') {
                continue;
            }

            $draft = is_array($item['draft'] ?? null) ? $item['draft'] : [];
            if ($draft !== []) {
                $candidates[] = $draft;
            }
        }

        return collect($candidates)
            ->sortByDesc(fn ($draft) => collect([
                'id_jenis_kegiatan',
                'target',
                'satuan_target',
                'tanggal_mulai',
                'tanggal_selesai',
            ])->filter(fn ($key) => ! empty($draft[$key] ?? null))->count())
            ->first() ?? [];
    }

    private function extractResultItems(array $result): array
    {
        $items = $result['items'] ?? $result['drafts'] ?? $result['actions'] ?? [];
        if (! is_array($items)) {
            return [];
        }

        return array_values(array_filter($items, fn ($item) => is_array($item)));
    }

    private function normalizeSingleResult(array $result, array $context): array
    {
        $targetForm = $this->inferTargetForm($result, $context);
        if (! in_array($targetForm, ['kegiatan', 'sub_kegiatan', 'penugasan', null], true)) {
            $targetForm = null;
        }

        $draft = is_array($result['draft'] ?? null) ? $result['draft'] : [];

        if ($targetForm === 'penugasan') {
            $draft = $this->normalizePenugasanDraft($draft, $context);
        }

        if ($targetForm === 'sub_kegiatan') {
            $draft = $this->normalizeSubKegiatanDraft($draft);
        }

        if ($targetForm === 'kegiatan') {
            $draft = $this->normalizeKegiatanDraft($draft, $context);

            if (($context['user']['active_role'] ?? null) === 'Ketua Tim') {
                unset($draft['id_penanggung_jawab']);
            }
        }

        $choices = $this->normalizeChoices($result['choices'] ?? [], $context);
        $choices = $this->augmentChoices($choices, $targetForm, $draft, $context);
        $warnings = array_values(array_filter(array_merge(
            (array) ($result['warnings'] ?? []),
            $this->dateWarnings($targetForm, $draft, $context)
        )));
        $missingFields = $this->normalizeMissingFields(
            array_merge((array) ($result['missing_fields'] ?? []), $this->requiredMissingFields($targetForm, $draft, $context)),
            $draft,
            $context,
            $targetForm
        );

        return [
            'intent' => $result['intent'] ?? ($targetForm ? 'create_' . $targetForm : 'clarify'),
            'target_form' => $targetForm,
            'summary' => (string) ($result['summary'] ?? ''),
            'draft' => $draft,
            'choices' => $choices,
            'missing_fields' => $missingFields,
            'warnings' => $warnings,
            'confidence' => max(0, min(1, (float) ($result['confidence'] ?? 0))),
            'follow_up_question' => (string) ($result['follow_up_question'] ?? ''),
        ];
    }

    private function normalizePenugasanDraft(array $draft, array $context): array
    {
        $namaAnggota = $draft['nama_anggota']
            ?? $draft['nama_anggota_guess']
            ?? $draft['anggota']
            ?? null;

        if (empty($draft['id_anggota']) && ! empty($namaAnggota)) {
            $match = $this->bestMatch($namaAnggota, $context['pegawais'] ?? [], 'nama_pegawai', 'id_pegawai');
            $draft['id_anggota'] = $match['value'] ?? null;
            $draft['nama_anggota'] = $match['label'] ?? $namaAnggota;
        }

        if (empty($draft['id_anggota']) && $this->isSelfReference((string) ($context['_prompt'] ?? ''))) {
            $draft['id_anggota'] = $context['user']['id_pegawai'] ?? null;
            $draft['nama_anggota'] = $context['user']['nama_pegawai'] ?? null;
        }

        if (! empty($draft['id_anggota']) && ! $this->findByValue($context['pegawais'] ?? [], 'id_pegawai', $draft['id_anggota'])) {
            unset($draft['id_anggota']);
        }

        if (! empty($draft['id_anggota'])) {
            $pegawai = $this->findByValue($context['pegawais'] ?? [], 'id_pegawai', $draft['id_anggota']);
            $draft['nama_anggota'] = $pegawai['nama_pegawai'] ?? ($draft['nama_anggota'] ?? null);
        }

        $jenisKegiatan = $draft['jenis_kegiatan']
            ?? $draft['jenis_kegiatan_guess']
            ?? null;

        if (empty($draft['id_jenis_kegiatan']) && ! empty($jenisKegiatan)) {
            $match = $this->bestMatch($jenisKegiatan, $context['jenis_kegiatans'] ?? [], 'jenis_kegiatan', 'id');
            $draft['id_jenis_kegiatan'] = $match['value'] ?? ($this->looksLikeNewJenisKegiatan((string) $jenisKegiatan) ? 'LAINNYA' : null);
            $draft['jenis_kegiatan'] = $match['label'] ?? $jenisKegiatan;
            if ($draft['id_jenis_kegiatan'] === 'LAINNYA') {
                $draft['jenis_kegiatan_baru'] = $draft['jenis_kegiatan_baru'] ?? $jenisKegiatan;
            }
        }

        if (! empty($draft['id_jenis_kegiatan']) && $draft['id_jenis_kegiatan'] !== 'LAINNYA' && ! $this->findByValue($context['jenis_kegiatans'] ?? [], 'id', $draft['id_jenis_kegiatan'])) {
            unset($draft['id_jenis_kegiatan']);
        }

        if (! empty($draft['id_jenis_kegiatan']) && $draft['id_jenis_kegiatan'] !== 'LAINNYA') {
            $jenis = $this->findByValue($context['jenis_kegiatans'] ?? [], 'id', $draft['id_jenis_kegiatan']);
            $draft['jenis_kegiatan'] = $jenis['jenis_kegiatan'] ?? ($draft['jenis_kegiatan'] ?? null);
        }

        $allowed = [
            'id_anggota',
            'nama_anggota',
            'id_jenis_kegiatan',
            'jenis_kegiatan',
            'jenis_kegiatan_baru',
            'target',
            'satuan_target',
            'tanggal_mulai',
            'tanggal_selesai',
            'butuh_dl',
            'butuh_translok',
            'tanggal_tambahan',
        ];

        $draft = Arr::only($draft, $allowed);

        if (isset($draft['target'])) {
            $draft['target'] = max(1, (int) $draft['target']);
        }

        foreach (['tanggal_mulai', 'tanggal_selesai'] as $field) {
            if (! empty($draft[$field])) {
                $draft[$field] = $this->coerceDate($draft[$field]);
            }
        }

        $jenisItem = $this->findByValue($context['jenis_kegiatans'] ?? [], 'id', $draft['id_jenis_kegiatan'] ?? null);
        if ($jenisItem && ! (bool) ($jenisItem['butuh_dl_atau_translok'] ?? false)) {
            $draft['butuh_dl'] = 0;
            $draft['butuh_translok'] = 0;
        } elseif ($jenisItem && (bool) ($jenisItem['butuh_dl_atau_translok'] ?? false)) {
            $wantsTranslok = Str::contains(Str::lower((string) ($context['_prompt'] ?? '')), ['translok']);
            $draft['butuh_dl'] = (int) (bool) ($draft['butuh_dl'] ?? ! $wantsTranslok);
            $draft['butuh_translok'] = (int) (bool) ($draft['butuh_translok'] ?? $wantsTranslok);
            if ($draft['butuh_dl'] && $draft['butuh_translok']) {
                $draft['butuh_translok'] = 0;
            }
        } else {
            $draft['butuh_dl'] = (int) (bool) ($draft['butuh_dl'] ?? 0);
            $draft['butuh_translok'] = (int) (bool) ($draft['butuh_translok'] ?? 0);
        }

        $draft['tanggal_tambahan'] = array_values(array_filter((array) ($draft['tanggal_tambahan'] ?? [])));
        $draft['tanggal_tambahan'] = array_map(fn ($range) => [
            'tanggal_mulai' => ! empty($range['tanggal_mulai'] ?? null) ? $this->coerceDate($range['tanggal_mulai']) : null,
            'tanggal_selesai' => ! empty($range['tanggal_selesai'] ?? null) ? $this->coerceDate($range['tanggal_selesai']) : null,
        ], $draft['tanggal_tambahan']);

        return $draft;
    }

    private function normalizeSubKegiatanDraft(array $draft): array
    {
        $draft = Arr::only($draft, ['nama_sub_kegiatan', 'target', 'satuan_target', 'tanggal_mulai', 'tanggal_selesai']);

        if (isset($draft['target'])) {
            $draft['target'] = max(1, (int) $draft['target']);
        }

        foreach (['tanggal_mulai', 'tanggal_selesai'] as $field) {
            if (! empty($draft[$field])) {
                $draft[$field] = $this->coerceDate($draft[$field]);
            }
        }

        return $draft;
    }

    private function normalizeKegiatanDraft(array $draft, array $context): array
    {
        $rkGuess = $draft['rk_jpt_label'] ?? $draft['rk_jpt_nama'] ?? $draft['nama_rencana_jpt'] ?? null;
        $ikiGuess = $draft['iki_jpt_label'] ?? $draft['iki_jpt_nama'] ?? $draft['nama_indikator_jpt'] ?? null;
        $ketuaGuess = $draft['nama_penanggung_jawab'] ?? $draft['penanggung_jawab'] ?? null;

        if (empty($draft['rk_jpt']) && $rkGuess) {
            $match = $this->bestMatch((string) $rkGuess, $context['rencana_jpts'] ?? [], 'nama_rencana_jpt', 'id');
            $draft['rk_jpt'] = $match['value'] ?? null;
            $draft['rk_jpt_label'] = $match['label'] ?? $rkGuess;
        }

        if (empty($draft['iki_jpt']) && $ikiGuess) {
            $candidates = $context['indikator_jpts'] ?? [];
            if (! empty($draft['rk_jpt'])) {
                $candidates = array_values(array_filter($candidates, fn ($item) => (string) ($item['id_rencana_jpt'] ?? '') === (string) $draft['rk_jpt']));
            }

            $match = $this->bestMatch((string) $ikiGuess, $candidates, 'nama_indikator_jpt', 'id');
            $draft['iki_jpt'] = $match['value'] ?? null;
            $draft['iki_jpt_label'] = $match['label'] ?? $ikiGuess;
        }

        if (empty($draft['id_penanggung_jawab']) && $ketuaGuess) {
            $match = $this->bestMatch((string) $ketuaGuess, $context['ketua_tims'] ?? [], 'nama_pegawai', 'id_pegawai');
            $draft['id_penanggung_jawab'] = $match['value'] ?? null;
            $draft['nama_penanggung_jawab'] = $match['label'] ?? $ketuaGuess;
        }

        if (empty($draft['id_penanggung_jawab']) && $this->isSelfReference((string) ($context['_prompt'] ?? ''))) {
            $draft['id_penanggung_jawab'] = $context['user']['id_pegawai'] ?? null;
            $draft['nama_penanggung_jawab'] = $context['user']['nama_pegawai'] ?? null;
        }

        if (empty($draft['tahun_kegiatan'])) {
            $draft['tahun_kegiatan'] = $this->extractYear((string) ($context['_prompt'] ?? '')) ?? now('Asia/Jakarta')->year;
        }

        if (! empty($draft['rk_jpt']) && ! $this->findByValue($context['rencana_jpts'] ?? [], 'id', $draft['rk_jpt'])) {
            unset($draft['rk_jpt'], $draft['rk_jpt_label']);
        }

        if (! empty($draft['iki_jpt']) && ! $this->findByValue($context['indikator_jpts'] ?? [], 'id', $draft['iki_jpt'])) {
            unset($draft['iki_jpt'], $draft['iki_jpt_label']);
        }

        if (! empty($draft['rk_jpt']) && ! empty($draft['iki_jpt'])) {
            $iki = $this->findByValue($context['indikator_jpts'] ?? [], 'id', $draft['iki_jpt']);
            if ($iki && (string) ($iki['id_rencana_jpt'] ?? '') !== (string) $draft['rk_jpt']) {
                unset($draft['iki_jpt'], $draft['iki_jpt_label']);
            }
        }

        if (! empty($draft['id_penanggung_jawab']) && ! $this->findByValue($context['ketua_tims'] ?? [], 'id_pegawai', $draft['id_penanggung_jawab'])) {
            unset($draft['id_penanggung_jawab'], $draft['nama_penanggung_jawab']);
        }

        return Arr::only($draft, [
            'nama_rk_kegiatan',
            'tahun_kegiatan',
            'rk_jpt',
            'rk_jpt_label',
            'iki_jpt',
            'iki_jpt_label',
            'id_penanggung_jawab',
            'nama_penanggung_jawab',
        ]);
    }

    private function inferTargetForm(array $result, array $context): ?string
    {
        $targetForm = $result['target_form'] ?? null;
        if ($targetForm) {
            return $targetForm;
        }

        $intent = Str::lower((string) ($result['intent'] ?? ''));
        if (Str::contains($intent, 'penugasan')) {
            return 'penugasan';
        }
        if (Str::contains($intent, ['sub_kegiatan', 'sub kegiatan', 'subkegiatan'])) {
            return 'sub_kegiatan';
        }
        if (Str::contains($intent, 'kegiatan')) {
            return 'kegiatan';
        }

        return null;
    }

    private function normalizeChoices(array $choices, array $context): array
    {
        $normalized = [];
        foreach ($choices as $choice) {
            if (! is_array($choice)) {
                continue;
            }

            $options = array_values(array_filter(array_map(function ($option) {
                if (! is_array($option)) {
                    return null;
                }

                return [
                    'value' => (string) ($option['value'] ?? ''),
                    'label' => (string) ($option['label'] ?? $option['value'] ?? ''),
                    'reason' => (string) ($option['reason'] ?? ''),
                ];
            }, (array) ($choice['options'] ?? []))));

            if ($options === []) {
                continue;
            }

            $normalized[] = [
                'field' => (string) ($choice['field'] ?? ''),
                'label' => (string) ($choice['label'] ?? 'Pilih salah satu'),
                'options' => $options,
            ];
        }

        return $normalized;
    }

    private function normalizeMissingFields(array $missingFields, array $draft, array $context, ?string $targetForm = null): array
    {
        $aliases = [
            'nama_anggota' => 'id_anggota',
            'nama_anggota_guess' => 'id_anggota',
            'jenis_kegiatan' => 'id_jenis_kegiatan',
            'jenis_kegiatan_guess' => 'id_jenis_kegiatan',
            'nama_penanggung_jawab' => 'id_penanggung_jawab',
            'penanggung_jawab' => 'id_penanggung_jawab',
            'nama_rencana_jpt' => 'rk_jpt',
            'nama_indikator_jpt' => 'iki_jpt',
        ];

        return collect($missingFields)
            ->map(fn ($field) => $aliases[$field] ?? $field)
            ->filter(fn ($field) => ! $this->fieldFilled($field, $draft, $context, $targetForm))
            ->unique()
            ->values()
            ->all();
    }

    private function requiredMissingFields(?string $targetForm, array $draft, array $context): array
    {
        if ($targetForm === 'penugasan') {
            $required = ['id_anggota', 'id_jenis_kegiatan', 'target', 'satuan_target', 'tanggal_mulai', 'tanggal_selesai'];
            if (empty($context['sub_kegiatan'])) {
                $required[] = 'id_sub_kegiatan';
            }

            if (($draft['id_jenis_kegiatan'] ?? null) === 'LAINNYA') {
                $required[] = 'jenis_kegiatan_baru';
            }

            return $required;
        }

        if ($targetForm === 'sub_kegiatan') {
            $required = ['nama_sub_kegiatan', 'target', 'satuan_target', 'tanggal_mulai', 'tanggal_selesai'];
            if (empty($context['kegiatan'])) {
                $required[] = 'id_kegiatan';
            }

            return $required;
        }

        if ($targetForm === 'kegiatan') {
            $required = ['nama_rk_kegiatan', 'tahun_kegiatan', 'rk_jpt', 'iki_jpt'];
            if (($context['user']['active_role'] ?? null) !== 'Ketua Tim') {
                $required[] = 'id_penanggung_jawab';
            }

            return $required;
        }

        return [];
    }

    private function fieldFilled(string $field, array $draft, array $context, ?string $targetForm): bool
    {
        if (! empty($draft[$field])) {
            return true;
        }

        if ($field === 'id_kegiatan') {
            return ! empty($context['kegiatan']) || ! empty($context['id_kegiatan']);
        }

        if ($field === 'id_sub_kegiatan') {
            return ! empty($context['sub_kegiatan']) || ! empty($context['id_sub_kegiatan']);
        }

        if ($field === 'id_penanggung_jawab' && $targetForm === 'kegiatan' && ($context['user']['active_role'] ?? null) === 'Ketua Tim') {
            return true;
        }

        return false;
    }

    private function dateWarnings(?string $targetForm, array $draft, array $context): array
    {
        $warnings = [];

        if (isset($draft['target']) && (! is_numeric($draft['target']) || (int) $draft['target'] < 1)) {
            $warnings[] = 'Target harus berupa angka minimal 1.';
        }

        if (! empty($draft['satuan_target']) && strlen((string) $draft['satuan_target']) > 50) {
            $warnings[] = 'Satuan target terlalu panjang untuk form penugasan.';
        }

        if (! empty($draft['tanggal_mulai']) && ! empty($draft['tanggal_selesai']) && $draft['tanggal_selesai'] < $draft['tanggal_mulai']) {
            $warnings[] = 'Tanggal selesai lebih awal dari tanggal mulai.';
        }

        if ($targetForm === 'penugasan' && ! empty($context['sub_kegiatan'])) {
            $min = $context['sub_kegiatan']['tanggal_mulai'] ?? null;
            $max = $context['sub_kegiatan']['tanggal_selesai'] ?? null;

            $ranges = [[
                'tanggal_mulai' => $draft['tanggal_mulai'] ?? null,
                'tanggal_selesai' => $draft['tanggal_selesai'] ?? null,
            ]];

            foreach ((array) ($draft['tanggal_tambahan'] ?? []) as $range) {
                $ranges[] = $range;
            }

            foreach ($ranges as $range) {
                $start = $range['tanggal_mulai'] ?? null;
                $end = $range['tanggal_selesai'] ?? null;
                if ($start && $end && (($min && $start < $min) || ($max && $end > $max))) {
                    $warnings[] = 'Ada tanggal penugasan di luar rentang sub kegiatan.';
                    break;
                }
            }
        }

        if ($targetForm === 'penugasan') {
            if (! empty($draft['butuh_dl']) && ! empty($draft['butuh_translok'])) {
                $warnings[] = 'DL dan Translok tidak boleh aktif bersamaan.';
            }

            if (empty($context['sub_kegiatan']) && empty($draft['id_sub_kegiatan'])) {
                $warnings[] = 'Konteks sub kegiatan belum jelas untuk penugasan ini.';
            }
        }

        if ($targetForm === 'sub_kegiatan' && empty($context['kegiatan']) && empty($draft['id_kegiatan'])) {
            $warnings[] = 'Konteks kegiatan belum jelas untuk sub kegiatan ini.';
        }

        return array_values(array_unique($warnings));
    }

    private function augmentChoices(array $choices, ?string $targetForm, array $draft, array $context): array
    {
        $existingFields = collect($choices)->pluck('field')->all();
        $prompt = (string) ($context['_prompt'] ?? '');

        if (! $targetForm && ! in_array('target_form', $existingFields, true)) {
            $choices[] = [
                'field' => 'target_form',
                'label' => 'Instruksi ini untuk form apa?',
                'options' => [
                    ['value' => 'penugasan', 'label' => 'Penugasan anggota', 'reason' => 'Mengisi form tambah anggota/penugasan'],
                    ['value' => 'sub_kegiatan', 'label' => 'Sub kegiatan', 'reason' => 'Mengisi form tambah sub kegiatan'],
                    ['value' => 'kegiatan', 'label' => 'Kegiatan', 'reason' => 'Mengisi form tambah kegiatan utama'],
                ],
            ];
        }

        if ($targetForm === 'penugasan') {
            if (empty($draft['id_anggota']) && ! in_array('id_anggota', $existingFields, true)) {
                $choices[] = [
                    'field' => 'id_anggota',
                    'label' => 'Pilih anggota yang ditugaskan',
                    'options' => $this->rankOptions($prompt . ' ' . ($draft['nama_anggota'] ?? ''), $context['pegawais'] ?? [], 'nama_pegawai', 'id_pegawai')
                        ->take(10)
                        ->map(fn ($pegawai) => [
                            'value' => (string) $pegawai['value'],
                            'label' => (string) $pegawai['label'],
                            'reason' => 'Pegawai tersedia di sistem',
                        ])
                        ->values()
                        ->all(),
                ];
            }

            if (empty($draft['id_jenis_kegiatan']) && ! in_array('id_jenis_kegiatan', $existingFields, true)) {
                $choices[] = [
                    'field' => 'id_jenis_kegiatan',
                    'label' => 'Pilih jenis kegiatan',
                    'options' => $this->rankOptions($prompt . ' ' . ($draft['jenis_kegiatan'] ?? ''), $context['jenis_kegiatans'] ?? [], 'jenis_kegiatan', 'id')
                        ->take(10)
                        ->map(fn ($jenis) => [
                            'value' => (string) $jenis['value'],
                            'label' => (string) $jenis['label'],
                            'reason' => ! empty($jenis['item']['butuh_dl_atau_translok']) ? 'Biasanya terkait DL/Translok' : 'Jenis kegiatan reguler',
                        ])
                        ->values()
                        ->all(),
                ];
            }
        }

        if ($targetForm === 'sub_kegiatan' && empty($context['kegiatan']) && ! in_array('id_kegiatan', $existingFields, true)) {
            $choices[] = [
                'field' => 'id_kegiatan',
                'label' => 'Sub kegiatan ini masuk ke kegiatan mana?',
                'options' => collect($context['kegiatans'] ?? [])
                    ->take(8)
                    ->map(fn ($kegiatan) => [
                        'value' => (string) $kegiatan['id_kegiatan'],
                        'label' => (string) $kegiatan['nama_rk_kegiatan'],
                        'reason' => 'Kegiatan tahun ' . ($kegiatan['tahun_kegiatan'] ?? '-'),
                    ])
                    ->values()
                    ->all(),
            ];
        }

        if ($targetForm === 'kegiatan') {
            if (empty($draft['rk_jpt']) && ! in_array('rk_jpt', $existingFields, true)) {
                $choices[] = [
                    'field' => 'rk_jpt',
                    'label' => 'Pilih RK JPT',
                    'options' => $this->rankOptions($prompt . ' ' . ($draft['nama_rk_kegiatan'] ?? ''), $context['rencana_jpts'] ?? [], 'nama_rencana_jpt', 'id')
                        ->take(10)
                        ->map(fn ($rk) => [
                            'value' => (string) $rk['value'],
                            'label' => (string) $rk['label'],
                            'reason' => 'Rencana JPT tersedia',
                        ])
                        ->values()
                        ->all(),
                ];
            }

            if (empty($draft['iki_jpt']) && ! in_array('iki_jpt', $existingFields, true)) {
                $indikatorOptions = $context['indikator_jpts'] ?? [];
                if (! empty($draft['rk_jpt'])) {
                    $indikatorOptions = array_values(array_filter($indikatorOptions, fn ($item) => (string) ($item['id_rencana_jpt'] ?? '') === (string) $draft['rk_jpt']));
                }

                $choices[] = [
                    'field' => 'iki_jpt',
                    'label' => 'Pilih IKI JPT',
                    'options' => $this->rankOptions($prompt . ' ' . ($draft['nama_rk_kegiatan'] ?? ''), $indikatorOptions, 'nama_indikator_jpt', 'id')
                        ->take(10)
                        ->map(fn ($iki) => [
                            'value' => (string) $iki['value'],
                            'label' => (string) $iki['label'],
                            'reason' => 'Indikator dari RK #' . ($iki['item']['id_rencana_jpt'] ?? '-'),
                        ])
                        ->values()
                        ->all(),
                ];
            }

            if (($context['user']['active_role'] ?? null) !== 'Ketua Tim' && empty($draft['id_penanggung_jawab']) && ! in_array('id_penanggung_jawab', $existingFields, true)) {
                $choices[] = [
                    'field' => 'id_penanggung_jawab',
                    'label' => 'Pilih Ketua Tim penanggung jawab',
                    'options' => $this->rankOptions($prompt, $context['ketua_tims'] ?? [], 'nama_pegawai', 'id_pegawai')
                        ->take(10)
                        ->map(fn ($pegawai) => [
                            'value' => (string) $pegawai['value'],
                            'label' => (string) $pegawai['label'],
                            'reason' => 'Ketua Tim tersedia',
                        ])
                        ->values()
                        ->all(),
                ];
            }
        }

        if ($targetForm === 'penugasan' && empty($context['sub_kegiatan']) && ! in_array('id_sub_kegiatan', $existingFields, true)) {
            $choices[] = [
                'field' => 'id_sub_kegiatan',
                'label' => 'Penugasan ini masuk ke sub kegiatan mana?',
                'options' => collect($context['sub_kegiatans'] ?? [])
                    ->take(8)
                    ->map(fn ($subKegiatan) => [
                        'value' => (string) $subKegiatan['id_sub_kegiatan'],
                        'label' => (string) $subKegiatan['nama_sub_kegiatan'],
                        'reason' => (string) ($subKegiatan['nama_rk_kegiatan'] ?? 'Sub kegiatan tersedia'),
                    ])
                    ->values()
                    ->all(),
            ];
        }

        return array_values(array_filter($choices, fn ($choice) => ! empty($choice['options'])));
    }

    private function emptyResult(string $message): array
    {
        return [
            'intent' => 'clarify',
            'target_form' => null,
            'summary' => '',
            'draft' => [],
            'choices' => [],
            'missing_fields' => ['prompt'],
            'warnings' => [$message],
            'confidence' => 0,
            'follow_up_question' => $message,
        ];
    }

    private function guessType(string $prompt, string $requested): string
    {
        if (in_array($requested, ['kegiatan', 'sub_kegiatan', 'penugasan'], true)) {
            return $requested;
        }

        $lower = Str::lower($prompt);
        if (Str::contains($lower, ['tugaskan', 'penugasan', 'anggota', 'beri tugas'])) {
            return 'penugasan';
        }

        if (Str::contains($lower, ['sub kegiatan', 'subkegiatan', 'rk anggota'])) {
            return 'sub_kegiatan';
        }

        return 'kegiatan';
    }

    private function bestMatch(string $needle, array $items, string $labelKey, string $valueKey): ?array
    {
        $ranked = $this->rankOptions($needle, $items, $labelKey, $valueKey);
        $best = $ranked->first();

        return ($best && $best['score'] >= 45)
            ? ['value' => $best['value'], 'label' => $best['label']]
            : null;
    }

    private function rankOptions(string $needle, array $items, string $labelKey, string $valueKey): \Illuminate\Support\Collection
    {
        $normalizedNeedle = $this->normalizeSearchText($needle);

        return collect($items)
            ->map(function ($item) use ($normalizedNeedle, $labelKey, $valueKey) {
                $label = (string) ($item[$labelKey] ?? '');
                $normalizedLabel = $this->normalizeSearchText($label);
                similar_text($normalizedNeedle, $normalizedLabel, $score);

                if ($normalizedNeedle !== '' && Str::contains($normalizedLabel, $normalizedNeedle)) {
                    $score += 35;
                }

                if ($normalizedLabel !== '' && Str::contains($normalizedNeedle, $normalizedLabel)) {
                    $score += 35;
                }

                $needleTokens = array_filter(explode(' ', $normalizedNeedle));
                $labelTokens = array_filter(explode(' ', $normalizedLabel));
                foreach ($needleTokens as $needleToken) {
                    if (strlen($needleToken) < 3) {
                        continue;
                    }

                    foreach ($labelTokens as $labelToken) {
                        if ($needleToken === $labelToken) {
                            $score += 45;
                        } elseif (strlen($needleToken) >= 4 && Str::startsWith($labelToken, $needleToken)) {
                            $score += 25;
                        }
                    }
                }

                return [
                    'value' => $item[$valueKey] ?? null,
                    'label' => $label,
                    'score' => $score,
                    'item' => $item,
                ];
            })
            ->sortByDesc('score')
            ->values();
    }

    private function normalizeSearchText(string $value): string
    {
        $value = Str::lower($value);
        $value = preg_replace('/\b(s\.p|m\.e|s\.e|s\.si|m\.si|drs|dra|h|hj)\b\.?/i', ' ', $value);
        $value = preg_replace('/[^a-z0-9]+/i', ' ', (string) $value);

        return trim(preg_replace('/\s+/', ' ', (string) $value));
    }

    private function formRules(): array
    {
        return [
            'kegiatan' => [
                'submit_route_context' => 'bidang',
                'required' => ['nama_rk_kegiatan', 'tahun_kegiatan', 'rk_jpt', 'iki_jpt', 'id_penanggung_jawab'],
                'auto_fields' => [
                    'id_bidang' => 'dari route bidang',
                    'id_penanggung_jawab' => 'otomatis user login jika active_role Ketua Tim',
                ],
                'validation' => [
                    'nama_rk_kegiatan' => 'string max 255',
                    'tahun_kegiatan' => 'tahun, contoh 2026',
                    'rk_jpt' => 'exists rencana_jpts.id',
                    'iki_jpt' => 'exists indikator_jpts.id dan harus milik rk_jpt',
                    'id_penanggung_jawab' => 'exists pegawais.id_pegawai, hanya Ketua Tim',
                ],
            ],
            'sub_kegiatan' => [
                'submit_route_context' => 'kegiatan',
                'required' => ['nama_sub_kegiatan', 'target', 'satuan_target', 'tanggal_mulai', 'tanggal_selesai'],
                'auto_fields' => ['status' => 'Berjalan'],
                'validation' => [
                    'target' => 'integer min 1',
                    'tanggal_mulai' => 'date YYYY-MM-DD',
                    'tanggal_selesai' => 'date YYYY-MM-DD after_or_equal tanggal_mulai',
                ],
            ],
            'penugasan' => [
                'submit_route_context' => 'sub_kegiatan',
                'required' => ['id_anggota', 'id_jenis_kegiatan', 'target', 'satuan_target', 'tanggal_mulai', 'tanggal_selesai'],
                'auto_fields' => ['status' => 'Belum Dikirim', 'status_dl/status_translok' => 'diatur backend'],
                'validation' => [
                    'id_anggota' => 'exists pegawais.id_pegawai',
                    'id_jenis_kegiatan' => 'exists jenis_kegiatans.id atau LAINNYA dengan jenis_kegiatan_baru',
                    'target' => 'integer min 1',
                    'satuan_target' => 'string max 50',
                    'tanggal_mulai/tanggal_selesai' => 'harus dalam rentang sub kegiatan',
                    'butuh_dl/butuh_translok' => 'boolean, hanya salah satu aktif untuk jenis butuh_dl_atau_translok',
                ],
            ],
        ];
    }

    private function promptExamples(): array
    {
        return [
            [
                'user' => 'buatkan tugas untuk dea tanggal 24-27 mei, perjalanan dinas target 25 peta',
                'expected' => 'target_form penugasan, resolve Dea dari allowed_pegawais, resolve Perjalanan Dinas dari allowed_jenis_kegiatan, tanggal 2026-05-24 sampai 2026-05-27, butuh_dl aktif jika jenisnya mewajibkan DL/Translok',
            ],
            [
                'user' => 'tugaskan Dea dan Budi supervisi tanggal 1 juni target 1 laporan',
                'expected' => 'buat items berisi dua draft penugasan, satu untuk Dea dan satu untuk Budi, field lain sama',
            ],
            [
                'user' => 'buat sub kegiatan pengolahan data target 12 dokumen tanggal 1-30 juni',
                'expected' => 'target_form sub_kegiatan, perlu current_kegiatan atau choice id_kegiatan jika konteks kegiatan belum ada',
            ],
            [
                'user' => 'buat kegiatan SBR 2026, sub kegiatan ground check 20 peta, tugaskan Dea',
                'expected' => 'buat items berurutan kegiatan, sub_kegiatan, penugasan; untuk item yang bergantung pada data baru, tampilkan missing/choices konteks yang perlu dipilih setelah submit item sebelumnya',
            ],
            [
                'user' => 'ubah yang tadi jadi Budi dan targetnya 10',
                'expected' => 'gunakan chat_history, pertahankan field lain dari draft sebelumnya, ubah anggota dan target saja',
            ],
            [
                'user' => 'buatkan untuk saya sendiri besok target 1 laporan',
                'expected' => 'gunakan user.id_pegawai sebagai id_anggota jika target_form penugasan',
            ],
        ];
    }

    private function coerceDate(mixed $value): ?string
    {
        if (! is_scalar($value) || trim((string) $value) === '') {
            return null;
        }

        try {
            return Carbon::parse((string) $value, 'Asia/Jakarta')->format('Y-m-d');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function findByValue(array $items, string $key, mixed $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        foreach ($items as $item) {
            if ((string) ($item[$key] ?? '') === (string) $value) {
                return $item;
            }
        }

        return null;
    }

    private function isSelfReference(string $prompt): bool
    {
        return Str::contains(Str::lower($prompt), ['saya', 'aku', 'sendiri', 'yang login']);
    }

    private function looksLikeNewJenisKegiatan(string $value): bool
    {
        $value = trim($value);

        return $value !== '' && strlen($value) <= 100;
    }

    private function extractDateRanges(string $prompt): array
    {
        $ranges = [];
        preg_match_all('/(\d{4}-\d{2}-\d{2})/', $prompt, $matches);
        $dates = $matches[1] ?? [];

        for ($i = 0; $i < count($dates); $i += 2) {
            $start = $dates[$i];
            $end = $dates[$i + 1] ?? $start;
            $ranges[] = ['tanggal_mulai' => $start, 'tanggal_selesai' => $end];
        }

        if ($ranges === []) {
            preg_match_all('/\b(\d{1,2})[\/\-](\d{1,2})(?:[\/\-](20\d{2}))?\b/', $prompt, $matches, PREG_SET_ORDER);
            $dates = array_map(function ($match) {
                $year = $match[3] ?? now('Asia/Jakarta')->year;

                return sprintf('%04d-%02d-%02d', (int) $year, (int) $match[2], (int) $match[1]);
            }, $matches);

            for ($i = 0; $i < count($dates); $i += 2) {
                $start = $dates[$i];
                $end = $dates[$i + 1] ?? $start;
                $ranges[] = ['tanggal_mulai' => $start, 'tanggal_selesai' => $end];
            }
        }

        if ($ranges === [] && preg_match_all('/\b(\d{1,2})(?:\s*(?:-|sampai|s\/d|sd)\s*(\d{1,2}))?\s+(januari|jan|februari|feb|maret|mar|april|apr|mei|juni|jun|juli|jul|agustus|agu|agt|september|sep|oktober|okt|november|nov|desember|des)(?:\s+(20\d{2}))?\b/i', $prompt, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $month = $this->monthNumber($match[3]);
                if (! $month) {
                    continue;
                }

                $year = (int) ($match[4] ?? now('Asia/Jakarta')->year);
                $startDay = (int) $match[1];
                $endDay = ! empty($match[2]) ? (int) $match[2] : $startDay;

                $ranges[] = [
                    'tanggal_mulai' => sprintf('%04d-%02d-%02d', $year, $month, $startDay),
                    'tanggal_selesai' => sprintf('%04d-%02d-%02d', $year, $month, $endDay),
                ];
            }
        }

        if ($ranges === [] && preg_match('/\b(hari ini|besok)\b/i', $prompt, $m)) {
            $date = Str::lower($m[1]) === 'besok' ? now('Asia/Jakarta')->addDay() : now('Asia/Jakarta');
            $ranges[] = [
                'tanggal_mulai' => $date->format('Y-m-d'),
                'tanggal_selesai' => $date->format('Y-m-d'),
            ];
        }

        return $ranges;
    }

    private function monthNumber(string $month): ?int
    {
        $key = Str::lower($month);
        $months = [
            'januari' => 1,
            'jan' => 1,
            'februari' => 2,
            'feb' => 2,
            'maret' => 3,
            'mar' => 3,
            'april' => 4,
            'apr' => 4,
            'mei' => 5,
            'juni' => 6,
            'jun' => 6,
            'juli' => 7,
            'jul' => 7,
            'agustus' => 8,
            'agu' => 8,
            'agt' => 8,
            'september' => 9,
            'sep' => 9,
            'oktober' => 10,
            'okt' => 10,
            'november' => 11,
            'nov' => 11,
            'desember' => 12,
            'des' => 12,
        ];

        return $months[$key] ?? null;
    }

    private function extractNumberNear(string $prompt, array $keywords): ?int
    {
        foreach ($keywords as $keyword) {
            if (preg_match('/' . preg_quote($keyword, '/') . '\D+(\d+)/i', $prompt, $match)) {
                return (int) $match[1];
            }
        }

        return preg_match('/\b(\d+)\b/', $prompt, $match) ? (int) $match[1] : null;
    }

    private function extractSatuan(string $prompt): ?string
    {
        $known = ['dokumen', 'laporan', 'kegiatan', 'orang', 'paket', 'data', 'sampel', 'desa', 'kecamatan'];
        $lower = Str::lower($prompt);
        foreach ($known as $unit) {
            if (Str::contains($lower, $unit)) {
                return Str::title($unit);
            }
        }

        return null;
    }

    private function extractName(string $prompt): string
    {
        $clean = preg_replace('/\b(buat|tambahkan|tambah|sub kegiatan|subkegiatan|kegiatan|penugasan|target|mulai|sampai|tanggal)\b/i', '', $prompt);
        $clean = preg_replace('/\s+/', ' ', trim((string) $clean));

        return Str::limit($clean, 120, '');
    }

    private function extractYear(string $prompt): ?int
    {
        return preg_match('/\b(20\d{2})\b/', $prompt, $match) ? (int) $match[1] : null;
    }
}
