@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="Master Kegiatan" />

    <div id="app" data-pegawais='@json($pegawais)'></div>

    <div class="space-y-6">
        <x-common.component-card title="Master Kegiatan">
            <div class="flex justify-end">
                @can('create', App\Models\Kegiatan::class)
                    <button
                        class="flex items-center gap-2 rounded-full border border-gray-300 dark:border-gray-600
                        bg-white dark:bg-gray-800 px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300
                        shadow-theme-xs hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-200"
                        @click="$dispatch('open-smart-modal', {
                            modalId: 'modal-master-kegiatan',
                    })">
                        <!-- icon -->
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                                fill="" />
                        </svg>
                        Tambah Master Kegiatan
                    </button>
                @endcan
            </div>
        </x-common.component-card>

        <!-- Tampilan Card Fungsi dengan Accordion -->
        <x-common.component-card title="Matriks Peran Hasil">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white"></h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('kegiatan.export-mph-all') }}"
                        class="flex items-center gap-2 rounded-lg border border-green-500 dark:border-green-600
                            bg-green-50 dark:bg-green-900/30 px-4 py-2 text-sm font-medium text-green-700 dark:text-green-400
                            hover:bg-green-100 dark:hover:bg-green-800/30 hover:text-green-800 dark:hover:text-green-300 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 384 512">
                            <path
                                d="M48 448V64c0-8.8 7.2-16 16-16H224v80c0 17.7 14.3 32 32 32h80V448c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16zM64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V154.5c0-17-6.7-33.3-18.7-45.3L274.7 18.7C262.7 6.7 246.5 0 229.5 0H64zm90.9 233.3c-8.1-10.5-23.2-12.3-33.7-4.2s-12.3 23.2-4.2 33.7L161.6 320l-44.5 57.3c-8.1 10.5-6.3 25.5 4.2 33.7s25.5 6.3 33.7-4.2L192 359.1l37.1 47.6c8.1 10.5 23.2 12.3 33.7 4.2s12.3-23.2 4.2-33.7L222.4 320l44.5-57.3c-8.1-10.5-6.3-25.5-4.2-33.7s-23.2-12.3-33.7-4.2L192 280.9l-37.1-47.6z" />
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>

            <div class="space-y-4">
                @foreach ($bidangs as $bidang)
                    <div x-data="{ open: false }"
                        class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">

                        <!-- Header Fungsi -->
                        <button @click="open = !open"
                            class="flex w-full items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-white">{{ $bidang->nama_bidang }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Total {{ $bidang->kegiatans->count() }} kegiatan
                                </p>
                            </div>

                            <svg :class="{ 'rotate-180': open }"
                                class="h-5 w-5 text-gray-500 dark:text-gray-400 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Accordion -->
                        <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-gray-700">

                            @if ($bidang->kegiatans->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full border border-gray-200 dark:border-gray-700">

                                        <!-- HEADER -->
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border dark:border-gray-700">
                                                    Kegiatan</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border dark:border-gray-700">
                                                    Sub Kegiatan</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border dark:border-gray-700">
                                                    Nama Pegawai</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border dark:border-gray-700">
                                                    Jenis Kegiatan</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border dark:border-gray-700">
                                                    Target</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border dark:border-gray-700">
                                                    Satuan</th>
                                            </tr>
                                        </thead>

                                        <!-- BODY -->
                                        <tbody>

                                            @foreach ($bidang->kegiatans as $kegiatan)
                                                {{-- Kalau belum ada sub --}}
                                                @if ($kegiatan->subKegiatans->count() === 0)
                                                    <tr>
                                                        <td class="px-4 py-3 align-top border dark:border-gray-700">
                                                            <div class="flex flex-col">
                                                                <span class="font-medium text-gray-800 dark:text-gray-300">
                                                                    {{ $kegiatan->nama_rk_kegiatan }}
                                                                </span>
                                                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                    Ketua:
                                                                    {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                                                </span>
                                                            </div>
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 text-gray-500 dark:text-gray-400 italic border dark:border-gray-700">
                                                            Belum ada sub kegiatan
                                                        </td>

                                                        <td colspan="4"
                                                            class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 italic border dark:border-gray-700">
                                                            Belum ada penugasan
                                                        </td>
                                                    </tr>
                                                @else
                                                    @foreach ($kegiatan->subKegiatans as $subIndex => $subKegiatan)
                                                        @php($penugasanCount = max($subKegiatan->penugasans->count(), 1))

                                                        {{-- Kalau belum ada penugasan --}}
                                                        @if ($subKegiatan->penugasans->count() === 0)
                                                            <tr>

                                                                {{-- MERGED KEGIATAN --}}
                                                                @if ($subIndex === 0)
                                                                    <td rowspan="{{ $kegiatan->subKegiatans->sum(fn($s) => max($s->penugasans->count(), 1)) }}"
                                                                        class="px-4 py-3 align-top border dark:border-gray-700">
                                                                        <div class="flex flex-col">
                                                                            <span
                                                                                class="font-medium text-gray-800 dark:text-gray-300">
                                                                                {{ $kegiatan->nama_rk_kegiatan }}
                                                                            </span>
                                                                            <span
                                                                                class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                                Ketua:
                                                                                {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                @endif

                                                                {{-- MERGED SUB --}}
                                                                <td rowspan="1"
                                                                    class="px-4 py-3 align-top border dark:border-gray-700 text-gray-800 dark:text-gray-300">
                                                                    {{ $subKegiatan->nama_sub_kegiatan }}
                                                                </td>

                                                                <td colspan="4"
                                                                    class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 italic border dark:border-gray-700">
                                                                    Belum ada penugasan
                                                                </td>

                                                            </tr>
                                                        @else
                                                            @foreach ($subKegiatan->penugasans as $penugasanIndex => $penugasan)
                                                                <tr>

                                                                    {{-- KEGIATAN --}}
                                                                    @if ($subIndex === 0 && $penugasanIndex === 0)
                                                                        <td rowspan="{{ $kegiatan->subKegiatans->sum(fn($s) => max($s->penugasans->count(), 1)) }}"
                                                                            class="px-4 py-3 align-top border dark:border-gray-700">
                                                                            <div class="flex flex-col">
                                                                                <span
                                                                                    class="font-medium text-gray-800 dark:text-gray-300">
                                                                                    {{ $kegiatan->nama_rk_kegiatan }}
                                                                                </span>
                                                                                <span
                                                                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                                    Ketua:
                                                                                    {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                                                                </span>
                                                                            </div>
                                                                        </td>
                                                                    @endif

                                                                    {{-- SUB KEGIATAN --}}
                                                                    @if ($penugasanIndex === 0)
                                                                        <td rowspan="{{ $penugasanCount }}"
                                                                            class="px-4 py-3 align-top border dark:border-gray-700 text-gray-800 dark:text-gray-300">
                                                                            {{ $subKegiatan->nama_sub_kegiatan }}
                                                                        </td>
                                                                    @endif

                                                                    <td
                                                                        class="px-4 py-3 border dark:border-gray-700 text-gray-800 dark:text-gray-300">
                                                                        {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                                                                    </td>

                                                                    <td
                                                                        class="px-4 py-3 border dark:border-gray-700 text-gray-800 dark:text-gray-300">
                                                                        {{ $penugasan->jenisKegiatan->jenis_kegiatan ?? '-' }}
                                                                    </td>

                                                                    <td
                                                                        class="px-4 py-3 border dark:border-gray-700 text-gray-800 dark:text-gray-300">
                                                                        {{ $penugasan->target ?? '-' }}
                                                                    </td>

                                                                    <td
                                                                        class="px-4 py-3 border dark:border-gray-700 text-gray-800 dark:text-gray-300">
                                                                        {{ $penugasan->satuan_target ?? '-' }}
                                                                    </td>

                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-4 text-center border-t dark:border-gray-700">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                                        Belum ada kegiatan untuk fungsi ini
                                    </p>
                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach


                {{-- Kalau belum ada bidang --}}
                @if ($bidangs->count() === 0)
                    <div
                        class="text-center py-8 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Belum ada fungsi/bidang yang dibuat
                        </p>
                    </div>
                @endif
            </div>

        </x-common.component-card>
    </div>

    <!-- Modal Master Kegiatan -->
    @include('pages.main.components.modals.rencana-kerja.modal-master-kegiatan')

    <!-- Modal Konfirmasi Master Kegiatan -->
    @include('pages.main.components.modals.rencana-kerja.modal-konfirmasi-master-kegiatan')
@endsection

@push('scripts')
    <script>
        const app = document.getElementById('app');
        const pegawais = app?.dataset.pegawais ? JSON.parse(app.dataset.pegawais) : [];

        let rkAnggotaCounter = 0;
        let detailAnggotaCounter = {};

        // =============================================
        // VALIDASI FRONTEND
        // =============================================

        function clearValidation() {
            // Hapus semua border merah dari input
            document.querySelectorAll('.input-invalid').forEach(el => {
                el.classList.remove(
                    'input-invalid',
                    'border-red-500', 'dark:border-red-500',
                    'bg-red-50', 'dark:bg-red-500/10'
                );
            });

            // Sembunyikan semua pesan error field
            document.querySelectorAll('.field-error-msg').forEach(el => {
                el.classList.add('hidden');
            });

            // Hapus border merah dari section/card
            document.querySelectorAll('.section-invalid').forEach(el => {
                el.classList.remove('section-invalid', 'border-red-400', 'dark:border-red-500/60');
            });

            // Sembunyikan banner
            const banner = document.getElementById('validationBanner');
            if (banner) banner.classList.add('hidden');
        }

        function markInvalid(el) {
            if (!el) return;
            el.classList.add(
                'input-invalid',
                'border-red-500', 'dark:border-red-500',
                'bg-red-50', 'dark:bg-red-500/10'
            );
        }

        function showFieldError(el) {
            if (!el) return;
            el.classList.remove('hidden');
        }

        function markSectionInvalid(el) {
            if (!el) return;
            el.classList.add('section-invalid', 'border-red-400', 'dark:border-red-500/60');
        }

        function validateForm() {
            clearValidation();
            const errors = [];

            function addError(message, focusEl, inputEl, errorMsgEl) {
                errors.push({
                    message,
                    focusEl
                });
                if (inputEl) markInvalid(inputEl);
                if (errorMsgEl) showFieldError(errorMsgEl);
            }

            // --- 1. Tahun ---
            const tahunInput = document.getElementById('tahunInput');
            if (!tahunInput?.value?.trim()) {
                addError(
                    'Tahun wajib diisi',
                    tahunInput, tahunInput,
                    tahunInput?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg')
                );
            } else if (isNaN(tahunKegiatan?.value?.trim())) {
                addError(
                    'Tahun Kegiatan harus berupa angka',
                    tahunKegiatan, tahunKegiatan,
                    tahunKegiatan?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg')
                );
            }

            // --- 2. RK JPT ---
            const rkJptSelect = document.getElementById('rk_jpt');
            if (!rkJptSelect?.value) {
                addError(
                    'Rencana JPT wajib dipilih',
                    rkJptSelect, rkJptSelect,
                    rkJptSelect?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg')
                );
            }

            // --- 3. IKI JPT ---
            const ikiJptSelect = document.getElementById('iki_jpt');
            if (!ikiJptSelect?.value) {
                addError(
                    'Indikator JPT wajib dipilih',
                    ikiJptSelect, ikiJptSelect,
                    ikiJptSelect?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg')
                );
            }

            // --- 4. Ketua ---
            const ketuaIdInput = document.querySelector('input[name="id_penanggung_jawab"]');
            const ketuaSearchInput = document.getElementById('ketuaSearchInput');
            const ketuaErrorMsg = ketuaSearchInput?.closest('.relative')?.querySelector('.field-error-msg');
            if (!ketuaIdInput?.value) {
                addError(
                    'Ketua wajib dipilih dari daftar dropdown (klik nama dari hasil pencarian)',
                    ketuaSearchInput, ketuaSearchInput,
                    ketuaErrorMsg
                );
            }

            // --- 5. Nama Kegiatan ---
            const rkKetuaInput = document.getElementById('rkKetua');
            const rkKetuaErrorMsg = rkKetuaInput?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
            if (!rkKetuaInput?.value?.trim()) {
                addError(
                    'Nama Kegiatan wajib diisi',
                    rkKetuaInput, rkKetuaInput,
                    rkKetuaErrorMsg
                );
            }

            // --- 6. Minimal 1 sub kegiatan ---
            const sections = document.querySelectorAll('[id^="rk-anggota-"]:not([id*="-detail-"])');

            if (sections.length === 0) {
                addError(
                    'Minimal harus menambahkan 1 sub kegiatan. Klik tombol "Tambah Sub Kegiatan" terlebih dahulu',
                    document.getElementById('rkAnggotaContainer'),
                    null,
                    null
                );
            }

            // --- 7. Validasi field tiap sub kegiatan ---
            sections.forEach((section, sectionIndex) => {
                const sectionNum = sectionIndex + 1;
                const sectionId = section.id;
                let sectionHasError = false;

                // Nama sub kegiatan
                const rkAnggotaInput = section.querySelector('input[name="rk_anggota[]"]');
                if (!rkAnggotaInput?.value?.trim()) {
                    addError(`Sub Kegiatan ${sectionNum}: Nama sub kegiatan wajib diisi`, rkAnggotaInput,
                        rkAnggotaInput, null);
                    sectionHasError = true;
                }

                // Target
                const targetInput = section.querySelector('input[name="target[]"]');
                if (!targetInput?.value?.trim()) {
                    addError(`Sub Kegiatan ${sectionNum}: Target wajib diisi`, targetInput, targetInput, null);
                    sectionHasError = true;
                }

                // Satuan target
                const satuanInput = section.querySelector('input[name="satuan_target[]"]');
                if (!satuanInput?.value?.trim()) {
                    addError(`Sub Kegiatan ${sectionNum}: Satuan target wajib diisi`, satuanInput, satuanInput,
                        null);
                    sectionHasError = true;
                }

                // Tanggal mulai
                const tanggalMulaiInput = section.querySelector('input[name="tanggal_mulai[]"]');
                if (!tanggalMulaiInput?.value?.trim()) {
                    addError(`Sub Kegiatan ${sectionNum}: Tanggal mulai wajib diisi`, tanggalMulaiInput,
                        tanggalMulaiInput, null);
                    sectionHasError = true;
                }

                // Tanggal selesai
                const tanggalSelesaiInput = section.querySelector('input[name="tanggal_selesai[]"]');
                if (!tanggalSelesaiInput?.value?.trim()) {
                    addError(`Sub Kegiatan ${sectionNum}: Tanggal selesai wajib diisi`, tanggalSelesaiInput,
                        tanggalSelesaiInput, null);
                    sectionHasError = true;
                } else if (tanggalMulaiInput?.value && tanggalSelesaiInput.value < tanggalMulaiInput.value) {
                    addError(
                        `Sub Kegiatan ${sectionNum}: Tanggal selesai tidak boleh sebelum tanggal mulai`,
                        tanggalSelesaiInput, tanggalSelesaiInput, null
                    );
                    sectionHasError = true;
                }

                // --- Minimal 1 anggota per sub kegiatan ---
                const detailContainer = document.getElementById(`detail-${sectionId}`);
                const detailItems = detailContainer ?
                    detailContainer.querySelectorAll('[id*="-detail-"]') : [];

                if (detailItems.length === 0) {
                    addError(
                        `Sub Kegiatan ${sectionNum}: Minimal harus menambahkan 1 anggota. Klik tombol "Tambah Anggota"`,
                        section,
                        null,
                        null
                    );
                    sectionHasError = true;
                }

                // --- Validasi tiap anggota ---
                detailItems.forEach((detail, detailIndex) => {
                    const anggotaNum = detailIndex + 1;
                    let detailHasError = false;

                    // Nama anggota
                    const idAnggotaInput = detail.querySelector('input[name*="detail_id_anggota"]');
                    const namaAnggotaSearchInput = detail.querySelector('.detail-nama-anggota');
                    if (!idAnggotaInput?.value) {
                        addError(
                            `Sub Kegiatan ${sectionNum} › Anggota ${anggotaNum}: Nama anggota wajib dipilih dari dropdown`,
                            namaAnggotaSearchInput, namaAnggotaSearchInput, null
                        );
                        detailHasError = true;
                    }

                    // Jenis kegiatan
                    const jenisSelect = detail.querySelector('select[name*="detail_id_jenis_kegiatan"]');
                    if (!jenisSelect?.value) {
                        addError(
                            `Sub Kegiatan ${sectionNum} › Anggota ${anggotaNum}: Jenis kegiatan wajib dipilih`,
                            jenisSelect, jenisSelect, null
                        );
                        detailHasError = true;
                    } else if (jenisSelect.value === 'LAINNYA') {
                        const jenisBaruInput = detail.querySelector(
                            'input[name*="detail_jenis_kegiatan_baru"]');
                        if (!jenisBaruInput?.value?.trim()) {
                            addError(
                                `Sub Kegiatan ${sectionNum} › Anggota ${anggotaNum}: Nama jenis kegiatan baru wajib diisi`,
                                jenisBaruInput, jenisBaruInput, null
                            );
                            detailHasError = true;
                        }
                    }

                    // Target anggota
                    const detailTarget = detail.querySelector('input[name*="detail_target"]');
                    if (!detailTarget?.value?.trim()) {
                        addError(
                            `Sub Kegiatan ${sectionNum} › Anggota ${anggotaNum}: Target wajib diisi`,
                            detailTarget, detailTarget, null
                        );
                        detailHasError = true;
                    }

                    // Satuan target anggota
                    const detailSatuan = detail.querySelector('input[name*="detail_satuan_target"]');
                    if (!detailSatuan?.value?.trim()) {
                        addError(
                            `Sub Kegiatan ${sectionNum} › Anggota ${anggotaNum}: Satuan target wajib diisi`,
                            detailSatuan, detailSatuan, null
                        );
                        detailHasError = true;
                    }

                    // Tanggal mulai anggota
                    const detailTanggalMulai = detail.querySelector(
                        `input[name="detail_tanggal_mulai[${sectionId}][]"]`);
                    if (!detailTanggalMulai?.value?.trim()) {
                        addError(
                            `Sub Kegiatan ${sectionNum} › Anggota ${anggotaNum}: Tanggal mulai wajib diisi`,
                            detailTanggalMulai, detailTanggalMulai, null
                        );
                        detailHasError = true;
                    }

                    // Tanggal selesai anggota
                    const detailTanggalSelesai = detail.querySelector(
                        `input[name="detail_tanggal_selesai[${sectionId}][]"]`);
                    if (!detailTanggalSelesai?.value?.trim()) {
                        addError(
                            `Sub Kegiatan ${sectionNum} › Anggota ${anggotaNum}: Tanggal selesai wajib diisi`,
                            detailTanggalSelesai, detailTanggalSelesai, null
                        );
                        detailHasError = true;
                    } else if (detailTanggalMulai?.value && detailTanggalSelesai.value < detailTanggalMulai
                        .value) {
                        addError(
                            `Sub Kegiatan ${sectionNum} › Anggota ${anggotaNum}: Tanggal selesai tidak boleh sebelum tanggal mulai`,
                            detailTanggalSelesai, detailTanggalSelesai, null
                        );
                        detailHasError = true;
                    }

                    if (detailHasError) {
                        markSectionInvalid(detail);
                        sectionHasError = true;
                    }
                });

                if (sectionHasError) {
                    markSectionInvalid(section);
                }
            });

            return errors;
        }

        function showValidationBanner(errors) {
            const banner = document.getElementById('validationBanner');
            const list = document.getElementById('validationList');
            if (!banner || !list) return;

            list.innerHTML = '';
            errors.forEach(err => {
                const li = document.createElement('li');
                li.textContent = err.message;
                li.className = 'text-xs text-red-600 dark:text-red-400 cursor-pointer hover:underline';
                if (err.focusEl) {
                    li.onclick = () => {
                        err.focusEl.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        setTimeout(() => err.focusEl.focus(), 300);
                    };
                }
                list.appendChild(li);
            });

            banner.classList.remove('hidden');
            banner.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        // =============================================
        // TAMBAH / HAPUS SUB KEGIATAN
        // =============================================

        function tambahRKAnggota() {
            rkAnggotaCounter++;
            const sectionIndex = rkAnggotaCounter;
            const sectionId = `rk-anggota-${sectionIndex}`;
            detailAnggotaCounter[sectionId] = 0;

            const sectionHTML = `
            <div id="${sectionId}" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4">
                <div class="mb-4 flex items-center justify-between">
                    <h5 class="text-sm font-semibold text-gray-800 dark:text-gray-300">
                        Sub Kegiatan ${rkAnggotaCounter}
                    </h5>
                    <button type="button" onclick="hapusRKAnggota('${sectionId}')"
                        class="rounded-lg p-1 text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <input type="hidden" name="rk_section_keys[]" value="${sectionId}">

                <div class="space-y-4">
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Nama Sub Kegiatan
                        </label>
                        <input name="rk_anggota[]" type="text" placeholder="Masukkan nama sub kegiatan"
                            class="md:w-3/4 h-11 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                    </div>

                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Target Kegiatan
                        </label>
                        <input type="number" name="target[]" placeholder="Misalnya : 200"
                            class="md:w-3/4 h-11 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                    </div>

                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Satuan Target
                        </label>
                        <input type="text" name="satuan_target[]" placeholder="Misalnya : Kegiatan, Dokumen, dll"
                            class="md:w-3/4 h-11 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                    </div>

                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Tanggal Mulai
                        </label>
                        <div class="md:w-3/4">
                            <x-form.date-picker name="tanggal_mulai[]" placeholder="Tanggal Mulai" defaultDate="{{ now()->format('Y-m-d') }}" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Tanggal Selesai
                        </label>
                        <div class="md:w-3/4">
                            <x-form.date-picker name="tanggal_selesai[]" placeholder="Tanggal Selesai" defaultDate="{{ now()->format('Y-m-d') }}" />
                        </div>
                    </div>

                    <div id="detail-${sectionId}" class="space-y-4"></div>

                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <div class="md:w-1/4"></div>
                        <div class="md:w-3/4">
                            <button type="button" onclick="tambahDetailAnggota('${sectionId}')"
                                class="flex items-center gap-2 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8 3.5C8.27614 3.5 8.5 3.72386 8.5 4V7.5H12C12.2761 7.5 12.5 7.72386 12.5 8C12.5 8.27614 12.2761 8.5 12 8.5H8.5V12C8.5 12.2761 8.27614 12.5 8 12.5C7.72386 12.5 7.5 12.2761 7.5 12V8.5H4C3.72386 8.5 3.5 8.27614 3.5 8C3.5 7.72386 3.72386 7.5 4 7.5H7.5V4C7.5 3.72386 7.72386 3.5 8 3.5Z" fill=""/>
                                </svg>
                                Tambah Anggota
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

            const container = document.getElementById('rkAnggotaContainer');
            container.insertAdjacentHTML('beforeend', sectionHTML);

            const sectionElement = document.getElementById(sectionId);
            if (sectionElement) {
                const today = new Date().toISOString().split('T')[0];
                const nextWeek = new Date();
                nextWeek.setDate(nextWeek.getDate() + 7);
                const nextWeekFormatted = nextWeek.toISOString().split('T')[0];

                const tanggalMulaiInput = sectionElement.querySelector('input[name="tanggal_mulai[]"]');
                const tanggalSelesaiInput = sectionElement.querySelector('input[name="tanggal_selesai[]"]');
                if (tanggalMulaiInput) tanggalMulaiInput.value = today;
                if (tanggalSelesaiInput) tanggalSelesaiInput.value = nextWeekFormatted;
            }

            setTimeout(() => {
                document.getElementById(sectionId)?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }, 100);
        }

        function hapusRKAnggota(sectionId) {
            document.getElementById(sectionId)?.remove();
        }

        // =============================================
        // TAMBAH / HAPUS DETAIL ANGGOTA
        // =============================================

        function tambahDetailAnggota(sectionId) {
            if (!detailAnggotaCounter[sectionId]) detailAnggotaCounter[sectionId] = 0;
            detailAnggotaCounter[sectionId]++;
            const detailId = sectionId + '-detail-' + detailAnggotaCounter[sectionId];

            const detailHTML = `
            <div id="${detailId}" class="rounded-lg border border-dashed border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-4">
                <div class="mb-3 flex items-center justify-between">
                    <h6 class="text-xs font-medium text-gray-700 dark:text-gray-400">
                        Anggota ${detailAnggotaCounter[sectionId]}
                    </h6>
                    <button type="button" onclick="hapusDetailAnggota('${detailId}')"
                        class="rounded-lg p-1 text-gray-400 dark:text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <!-- Nama Anggota dengan Alpine Search -->
                    <div class="relative flex flex-col gap-2 md:flex-row md:items-center"
                        x-data="{
                            open: false,
                            search: '',
                            selectedId: '',
                            highlightedIndex: -1,
                            pegawais: pegawais,
                            filtered() {
                                if(this.search.length === 0) return [];
                                return this.pegawais.filter(p =>
                                    p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase())
                                );
                            },
                            selectPegawai(p) {
                                this.search = p.nama_pegawai;
                                this.selectedId = p.id_pegawai;
                                this.open = false;
                                this.highlightedIndex = -1;
                            },
                            highlightNext() { if(this.highlightedIndex < this.filtered().length - 1) this.highlightedIndex++; },
                            highlightPrev() { if(this.highlightedIndex > 0) this.highlightedIndex--; },
                            selectHighlighted() { if(this.highlightedIndex >= 0) this.selectPegawai(this.filtered()[this.highlightedIndex]); }
                        }">
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Nama Anggota
                        </label>

                        <div class="relative md:w-3/4">
                            <input
                                type="text"
                                x-model="search"
                                @focus="open = !!search"
                                @input="open = search.length > 0; selectedId = ''"
                                @keydown.arrow-down.prevent="highlightNext()"
                                @keydown.arrow-up.prevent="highlightPrev()"
                                @keydown.enter.prevent="selectHighlighted()"
                                placeholder="Ketik untuk cari nama anggota"
                                class="detail-nama-anggota h-10 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10">

                            <input type="hidden" name="detail_id_anggota[${sectionId}][]" x-model="selectedId">
                            <input type="hidden" name="detail_nama_anggota[${sectionId}][]" x-model="search">

                            <div
                                x-show="open && search.length > 0"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                @click.away="open = false"
                                class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg max-h-60 overflow-y-auto">

                                <template x-if="filtered().length > 0">
                                    <template x-for="(pegawai, index) in filtered()" :key="pegawai.id_pegawai">
                                        <div
                                            @click="selectPegawai(pegawai)"
                                            :class="{
                                                'bg-brand-50 dark:bg-brand-900/30': highlightedIndex===index,
                                                'hover:bg-gray-50 dark:hover:bg-gray-700': highlightedIndex!==index
                                            }"
                                            class="cursor-pointer px-3 py-2 text-xs text-gray-700 dark:text-gray-300"
                                            x-text="pegawai.nama_pegawai">
                                        </div>
                                    </template>
                                </template>

                                <template x-if="search.length > 0 && filtered().length === 0">
                                    <div class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400">
                                        Data tidak ditemukan
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Jenis Kegiatan -->
                    <div
                        x-data="{
                            idJenisKegiatan: '',
                            isOther: false,
                            butuhDl: false,
                            isLocked: true,
                            wajibJenis: [3,4,5,6],
                            get jenisId() { return Number(this.idJenisKegiatan || 0) },
                            get isLainnya() { return this.idJenisKegiatan === 'LAINNYA' },
                            get jenisSelected() { return this.jenisId > 0 || this.isLainnya },
                            syncState() {
                                const isWajib = this.wajibJenis.includes(this.jenisId);
                                this.isOther = this.idJenisKegiatan === 'LAINNYA';
                                if (this.isLainnya) { this.butuhDl = false; this.isLocked = false; return; }
                                if (!this.jenisSelected) { this.butuhDl = false; this.isLocked = true; return; }
                                if (isWajib) { this.butuhDl = true; this.isLocked = true; }
                                else { this.butuhDl = false; this.isLocked = false; }
                            }
                        }"
                        x-effect="syncState()"
                        class="space-y-4">

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 md:col-span-1 pt-2">
                                Jenis Kegiatan
                            </label>

                            <div class="md:col-span-2 space-y-2">
                                <select
                                    name="detail_id_jenis_kegiatan[${sectionId}][]"
                                    x-model="idJenisKegiatan"
                                    @change="isOther = (idJenisKegiatan === 'LAINNYA')"
                                    required
                                    class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 px-3 py-2 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 transition">
                                    <option value="">-- Pilih Jenis Kegiatan --</option>
                                    @foreach ($jenisKegiatans as $jenis)
                                        <option value="{{ $jenis->id }}"
                                            class="@if ($jenis->kategori === 'Utama') text-green-700 dark:text-green-400 font-medium @elseif($jenis->kategori === 'Tambahan') text-orange-700 dark:text-orange-400 @endif">
                                            {{ $jenis->jenis_kegiatan }} ({{ $jenis->kategori }})
                                        </option>
                                    @endforeach
                                    <option value="LAINNYA">➕ Lainnya</option>
                                </select>

                                <div x-show="isOther">
                                    <input
                                        type="text"
                                        name="detail_jenis_kegiatan_baru[${sectionId}][]"
                                        placeholder="Masukkan jenis kegiatan baru"
                                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 px-3 py-2 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-400 transition" />
                                </div>
                            </div>

                            <div class="md:col-span-1 flex md:justify-end items-center gap-3 pt-2">
                                <button
                                    type="button"
                                    @click="if (!isLocked) butuhDl = !butuhDl"
                                    :class="{
                                        'bg-brand-500 dark:bg-brand-600 shadow-sm': butuhDl,
                                        'bg-gray-200 dark:bg-gray-700 shadow-sm': !butuhDl,
                                        'cursor-not-allowed opacity-60': isLocked
                                    }"
                                    class="relative inline-flex h-7 w-14 items-center rounded-full transition-all duration-200">
                                    <span
                                        :class="butuhDl ? 'translate-x-7' : 'translate-x-1'"
                                        class="inline-block h-5 w-5 bg-white dark:bg-gray-300 rounded-full shadow-sm transition-all duration-200">
                                    </span>
                                </button>
                                <span
                                    class="text-xs font-medium transition-colors duration-200 whitespace-nowrap"
                                    :class="butuhDl ? 'text-brand-600 dark:text-brand-400' : 'text-gray-500 dark:text-gray-400'"
                                    x-text="!jenisSelected ? 'Pilih dulu' : (butuhDl ? 'Perlu DL' : 'Tidak DL')">
                                </span>
                            </div>
                        </div>

                        <div class="mt-2 md:ml-[25%]">
                            <p x-show="!jenisSelected" class="text-xs text-brand-500/70 dark:text-brand-400/70">
                                Pilih jenis kegiatan untuk menentukan keperluan DL.
                            </p>
                            <p x-show="isLocked && jenisSelected" class="text-xs text-gray-400 dark:text-gray-500">
                                Jenis kegiatan ini otomatis memerlukan DL dan tidak dapat diubah.
                            </p>
                            <input type="hidden" name="detail_butuh_dl[${sectionId}][]" :value="butuhDl ? 1 : 0">
                        </div>
                    </div>

                    <!-- Target Anggota -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Target
                        </label>
                        <input name="detail_target[${sectionId}][]" type="text" placeholder="Masukkan target"
                            class="md:w-3/4 h-10 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                    </div>

                    <!-- Satuan Target Anggota -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Satuan Target
                        </label>
                        <input name="detail_satuan_target[${sectionId}][]" type="text" placeholder="Masukkan satuan target"
                            class="md:w-3/4 h-10 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-transparent px-3 py-2 text-xs text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                    </div>

                    <!-- Tanggal Mulai Anggota -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Tanggal Mulai
                        </label>
                        <div class="md:w-3/4">
                            <x-form.date-picker
                                name="detail_tanggal_mulai[${sectionId}][]"
                                placeholder="Tanggal Mulai"
                                defaultDate="{{ now()->format('Y-m-d') }}" />
                        </div>
                    </div>

                    <!-- Tanggal Selesai Anggota -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Tanggal Berakhir (Deadline)
                        </label>
                        <div class="md:w-3/4">
                            <x-form.date-picker
                                name="detail_tanggal_selesai[${sectionId}][]"
                                placeholder="Tanggal Selesai"
                                defaultDate="{{ now()->format('Y-m-d') }}" />
                        </div>
                    </div>
                </div>
            </div>
        `;

            const detailContainer = document.getElementById(`detail-${sectionId}`);
            if (detailContainer) {
                detailContainer.insertAdjacentHTML('beforeend', detailHTML);
                if (window.Alpine) Alpine.initTree(detailContainer);

                const detailElement = document.getElementById(detailId);
                if (detailElement) {
                    const today = new Date().toISOString().split('T')[0];
                    const nextWeek = new Date();
                    nextWeek.setDate(nextWeek.getDate() + 7);
                    const nextWeekFormatted = nextWeek.toISOString().split('T')[0];

                    const tMulai = detailElement.querySelector(`input[name="detail_tanggal_mulai[${sectionId}][]"]`);
                    const tSelesai = detailElement.querySelector(`input[name="detail_tanggal_selesai[${sectionId}][]"]`);
                    if (tMulai) tMulai.value = today;
                    if (tSelesai) tSelesai.value = nextWeekFormatted;
                }
            }
        }

        function hapusDetailAnggota(detailId) {
            document.getElementById(detailId)?.remove();
        }

        // =============================================
        // SAVE ALL — dengan validasi frontend
        // =============================================

        function saveAll(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            // ---- Jalankan validasi ----
            const errors = validateForm();
            if (errors.length > 0) {
                showValidationBanner(errors);
                return; // Berhenti — tidak buka modal konfirmasi
            }

            // ---- Lolos validasi, lanjut build confirmation HTML ----
            try {
                const tahunInput = document.getElementById('tahunInput');
                const tahun = tahunInput ? tahunInput.value : '{{ now()->format('Y') }}';

                const rkJptInput = document.querySelector('#rk_jpt');
                const rkJpt = rkJptInput ? rkJptInput.value : '';

                const ikiJptInput = document.querySelector('#iki_jpt');
                const ikiJpt = ikiJptInput ? ikiJptInput.value : '';

                const bidangSelect = document.getElementById('bidang');
                let bidangNama = '';
                if (bidangSelect && bidangSelect.selectedIndex >= 0) {
                    const selectedOption = bidangSelect.options[bidangSelect.selectedIndex];
                    bidangNama = selectedOption ? selectedOption.text : '';
                }

                const ketuaSearchInput = document.getElementById('ketuaSearchInput');
                const namaKetua = ketuaSearchInput ? ketuaSearchInput.value : '';

                const ketuaIdInput = document.querySelector('input[name="id_penanggung_jawab"]');
                const idKetua = ketuaIdInput ? ketuaIdInput.value : '';

                const rkKetuaInput = document.getElementById('rkKetua');
                const rkKetua = rkKetuaInput ? rkKetuaInput.value : '';

                const sections = document.querySelectorAll('[id^="rk-anggota-"]:not([id*="-detail-"])');

                let detailHTML = '';

                sections.forEach((section, sectionIndex) => {
                    let sectionHTML = '';
                    const sectionId = section.id;

                    const rkAnggotaInput = section.querySelector('input[name="rk_anggota[]"]');
                    const TargetInput = section.querySelector('input[name="target[]"]');
                    const satuanTargetInput = section.querySelector('input[name="satuan_target[]"]');
                    const tanggalMulaiInput = section.querySelector('input[name="tanggal_mulai[]"]');
                    const tanggalSelesaiInput = section.querySelector('input[name="tanggal_selesai[]"]');

                    const rkAnggota = rkAnggotaInput ? rkAnggotaInput.value : '';
                    const target = TargetInput ? TargetInput.value : '';
                    const satuanTarget = satuanTargetInput ? satuanTargetInput.value : '';
                    const tanggalMulai = tanggalMulaiInput ? tanggalMulaiInput.value : '';
                    const tanggalAkhir = tanggalSelesaiInput ? tanggalSelesaiInput.value : '';

                    sectionHTML += `
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4 bg-white dark:bg-gray-800">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                RK Anggota ${sectionIndex + 1}
                            </h5>
                            <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-400">
                                Bagian ${sectionIndex + 1}
                            </span>
                        </div>
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">RK Anggota</span>
                                    <span class="block text-sm text-gray-800 dark:text-gray-300">${rkAnggota || '-'}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Target</span>
                                    <span class="block text-sm text-gray-800 dark:text-gray-300">${target || '-'} ${satuanTarget || ''}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tanggal Mulai</span>
                                    <span class="block text-sm text-gray-800 dark:text-gray-300">${tanggalMulai || '-'}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tanggal Selesai</span>
                                    <span class="block text-sm text-gray-800 dark:text-gray-300">${tanggalAkhir || '-'}</span>
                                </div>
                            </div>
                `;

                    const detailContainer = document.getElementById(`detail-${sectionId}`);
                    let detailAnggotas = [];
                    if (detailContainer) {
                        detailAnggotas = detailContainer.querySelectorAll('[id*="-detail-"]');
                    }

                    if (detailAnggotas.length > 0) {
                        sectionHTML += `
                        <div class="mt-3 border-t border-gray-100 dark:border-gray-700 pt-3">
                            <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Anggota:</p>
                            <div class="space-y-2">
                    `;

                        detailAnggotas.forEach((detail, detailIndex) => {
                            const namaInput = detail.querySelector('input[name*="detail_nama_anggota"]');
                            const namaAnggota = namaInput ? namaInput.value : '';

                            const idInput = detail.querySelector('input[name*="detail_id_anggota"]');
                            const idAnggota = idInput ? idInput.value : '';

                            const targetInput = detail.querySelector('input[name*="detail_target"]');
                            const target = targetInput ? targetInput.value : '';

                            const satuanTargetInput = detail.querySelector(
                                'input[name*="detail_satuan_target"]');
                            const satuanTarget = satuanTargetInput ? satuanTargetInput.value : '';

                            const jenisSelect = detail.querySelector(
                                'select[name*="detail_id_jenis_kegiatan"]');
                            const jenisBaruInput = detail.querySelector(
                                'input[name*="detail_jenis_kegiatan_baru"]');
                            let jenisKegiatan = '-';
                            if (jenisSelect) {
                                if (jenisSelect.value === 'LAINNYA') {
                                    jenisKegiatan = jenisBaruInput && jenisBaruInput.value ? jenisBaruInput
                                        .value : '-';
                                } else {
                                    const selectedOption = jenisSelect.options[jenisSelect.selectedIndex];
                                    jenisKegiatan = selectedOption ? selectedOption.text : '-';
                                }
                            }

                            const butuhDLInput = detail.querySelector('input[name*="detail_butuh_dl"]');
                            const butuhDL = butuhDLInput ? Number(butuhDLInput.value) === 1 : false;
                            const butuhDLText = butuhDL ? 'Perlu Dinas Luar / Surat Tugas' :
                                'Tidak Perlu Dinas Luar / Surat Tugas';
                            const butuhDLBadge = butuhDL ?
                                `<span class="inline-flex items-center rounded-md bg-green-100 dark:bg-green-900/40 px-2 py-1 text-[11px] font-medium text-green-700 dark:text-green-400">✓ ${butuhDLText}</span>` :
                                `<span class="inline-flex items-center rounded-md bg-orange-100 dark:bg-orange-900/40 px-2 py-1 text-[11px] font-medium text-orange-700 dark:text-orange-400">✕ ${butuhDLText}</span>`;

                            const detailTanggalMulaiInput = detail.querySelector(
                                `input[name="detail_tanggal_mulai[${sectionId}][]"]`);
                            const detailTanggalSelesaiInput = detail.querySelector(
                                `input[name="detail_tanggal_selesai[${sectionId}][]"]`);
                            const detailTanggalMulai = detailTanggalMulaiInput ? detailTanggalMulaiInput
                                .value : '';
                            const detailTanggalSelesai = detailTanggalSelesaiInput ?
                                detailTanggalSelesaiInput.value : '';

                            sectionHTML += `
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-400">Anggota ${detailIndex + 1}</span>
                                    <div>
                                        <span class="text-xs font-medium text-gray-800 dark:text-gray-300">${namaAnggota || '-'}</span>
                                        ${idAnggota ? `<span class="ml-2 text-xs text-gray-500 dark:text-gray-500">(ID: ${idAnggota})</span>` : ''}
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="col-span-2">
                                        <span class="block text-gray-500 dark:text-gray-500 mb-1">Jenis Kegiatan</span>
                                        <span class="block text-gray-700 dark:text-gray-300 font-medium">${jenisKegiatan}</span>
                                    </div>
                                    <div>
                                        <span class="block text-gray-500 dark:text-gray-500 mb-1">Target</span>
                                        <span class="block text-gray-700 dark:text-gray-300">${target || '-'} ${satuanTarget || '-'}</span>
                                        <div class="mt-2">${butuhDLBadge}</div>
                                    </div>
                                    <div>
                                        <span class="block text-gray-500 dark:text-gray-500 mb-1">Tanggal</span>
                                        <span class="block text-gray-700 dark:text-gray-300">${detailTanggalMulai || '-'} s/d ${detailTanggalSelesai || '-'}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        });

                        sectionHTML += `</div></div>`;
                    }

                    sectionHTML += `</div></div>`;
                    detailHTML += sectionHTML;
                });

                const confirmationHTML = `
                <div class="space-y-6">
                    <div class="text-center">
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 mb-3">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">DATA RENCANA KINERJA KETUA</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Review data sebelum disimpan ke database</p>
                    </div>

                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">DATA KETUA</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tahun</span>
                                    <span class="block text-sm text-gray-800 dark:text-gray-300 font-medium">${tahun || '-'}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">RK JPT</span>
                                    <span class="block text-sm text-gray-800 dark:text-gray-300">${rkJpt || '-'}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">IKI JPT</span>
                                    <span class="block text-sm text-gray-800 dark:text-gray-300">${ikiJpt || '-'}</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Bidang</span>
                                    <span class="block text-sm text-gray-800 dark:text-gray-300 font-medium">${bidangNama || '-'}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Ketua</span>
                                    <span class="block text-sm text-gray-800 dark:text-gray-300 font-medium">${namaKetua || '-'}</span>
                                    ${idKetua ? `<span class="block text-xs text-gray-500 dark:text-gray-500 mt-1">ID: ${idKetua}</span>` : ''}
                                </div>
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Rencana Kinerja Ketua</span>
                                    <span class="block text-sm text-gray-800 dark:text-gray-300">${rkKetua || '-'}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">DATA SUB KEGIATAN</h4>
                            <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:text-gray-300">
                                ${sections.length} sub kegiatan
                            </span>
                        </div>
                        ${sections.length > 0
                            ? `<div class="space-y-4">${detailHTML}</div>`
                            : `<div class="text-center py-4">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada sub kegiatan yang ditambahkan</p>
                                    </div>`
                        }
                    </div>

                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Data akan disimpan ke database setelah konfirmasi
                    </div>
                </div>
            `;

                const confirmationContent = document.getElementById('confirmationContent');
                if (confirmationContent) confirmationContent.innerHTML = confirmationHTML;

                const alpineElement = event?.target?.closest('[x-data]');
                if (alpineElement && alpineElement.__x) {
                    alpineElement.__x.$dispatch('open-confirmation-modal');
                } else {
                    const customEvent = new CustomEvent('open-confirmation-modal', {
                        bubbles: true,
                        composed: true
                    });
                    event?.target?.dispatchEvent(customEvent);
                }

            } catch (error) {
                console.error('❌ Error in saveAll():', error);
                alert('Terjadi kesalahan: ' + error.message);
            }
        }

        function confirmSave() {
            window.dispatchEvent(new CustomEvent('close-confirmation-modal', {
                detail: {
                    modalId: 'modal-konfirmasi-master-kegiatan'
                }
            }));
            const form = document.getElementById('masterKegiatanForm');
            if (!form) {
                alert('Form tidak ditemukan');
                return;
            }
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('saveAllButton')?.addEventListener('click', saveAll);
        });
    </script>
@endpush
