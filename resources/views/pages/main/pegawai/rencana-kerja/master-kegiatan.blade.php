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
        window.jenisButuhMap = @json($jenisKegiatans->pluck('butuh_dl_atau_translok', 'id'));

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
            } else if (isNaN(tahunInput?.value?.trim())) {
                addError(
                    'Tahun Kegiatan harus berupa angka',
                    tahunInput, tahunInput,
                    tahunInput?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg')
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
                    detailContainer.querySelectorAll('.rk-detail-item') : [];

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
                    const jenisInput = detail.querySelector('input[name*="detail_id_jenis_kegiatan"]');
                    const jenisFocusEl = jenisInput ? jenisInput.nextElementSibling?.querySelector('button') || jenisInput : null;

                    if (!jenisInput?.value) {
                        addError(
                            `Sub Kegiatan ${sectionNum} › Anggota ${anggotaNum}: Jenis kegiatan wajib dipilih`,
                            jenisFocusEl, jenisFocusEl, null
                        );
                        detailHasError = true;
                    } else if (jenisInput.value === 'LAINNYA') {
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

                    // --- Validasi DL / Translok ---
                    if (jenisInput?.value && window.jenisButuhMap?.[Number(jenisInput.value)] == 1) {
                        const dlVal = detail.querySelector('input[name*="detail_butuh_dl"]')?.value;
                        const translokVal = detail.querySelector('input[name*="detail_butuh_translok"]')?.value;
                        const toggleDlBtn = document.getElementById(`toggle-dl-btn-${detail.id}`);

                        if (dlVal == 1 && translokVal == 1) {
                            addError(
                                `Sub Kegiatan ${sectionNum} › Anggota ${anggotaNum}: Pilih salah satu DL atau Translok — tidak boleh keduanya aktif sekaligus. Silakan matikan salah satu toggle tersebut.`,
                                toggleDlBtn, null, null
                            );
                            detailHasError = true;
                        } else if (dlVal != 1 && translokVal != 1) {
                            addError(
                                `Sub Kegiatan ${sectionNum} › Anggota ${anggotaNum}: Salah satu dari DL atau Translok wajib dipilih.`,
                                toggleDlBtn, null, null
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

        let rkAnggotaCounter = 0;
        let detailAnggotaCounter = {};
        function tambahRKAnggota() {
            rkAnggotaCounter++;
            const sectionIndex = rkAnggotaCounter;
            const sectionId = `rk-anggota-${sectionIndex}`;
            detailAnggotaCounter[sectionId] = 0;

            const sectionHTML = `
                <div id="${sectionId}" x-data="{ min_date: '', max_date: '' }" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4">
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
                                <x-form.date-picker x-model="min_date" name="tanggal_mulai[]" placeholder="Tanggal Mulai" />
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 md:flex-row md:items-center">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                                Tanggal Selesai
                            </label>
                            <div class="md:w-3/4">
                                <x-form.date-picker x-model="max_date" name="tanggal_selesai[]" placeholder="Tanggal Selesai" />
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
                // Default values removed intentionally per requirement to show placeholder
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
            <div id="${detailId}" class="rk-detail-item rounded-lg border border-dashed border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-4">
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

                    <div
                        x-data="{
                            idJenisKegiatan: '',
                            isOther: false,
                            butuhDl: false,
                            butuhTranslok: false,
                            showToggle: false,
                            targetDikunci: false,
                            target: '',

                            open: false,
                            highlightedIndex: -1,
                            search: '',

                            options: [
                                @foreach ($jenisKegiatans as $jenis)
                                    {
                                        id: '{{ $jenis->id }}',
                                        text: '{{ addslashes($jenis->jenis_kegiatan) }} ({{ $jenis->kategori }})',
                                        style: '{{ $jenis->kategori === 'Utama'
                                            ? 'text-green-700 font-medium dark:text-green-300'
                                            : 'text-orange-700 dark:text-orange-300' }}'
                                    },
                                @endforeach
                                {
                                    id: 'LAINNYA',
                                    text: '➕ Lainnya',
                                    style: 'text-blue-700 font-medium dark:text-blue-300'
                                }
                            ],

                            get filteredOptions() {
                                if (!this.search) return this.options;
                                return this.options.filter(o =>
                                    o.text.toLowerCase().includes(this.search.toLowerCase())
                                );
                            },

                            get selectText() {
                                if (!this.idJenisKegiatan) return '-- Pilih Jenis Kegiatan --';
                                let opt = this.options.find(o => o.id == this.idJenisKegiatan);
                                return opt ? opt.text : '-- Pilih Jenis Kegiatan --';
                            },

                            selectJenis(opt) {
                                this.idJenisKegiatan = opt.id;
                                this.isOther = (opt.id === 'LAINNYA');
                                this.open = false;
                                this.search = '';
                                this.highlightedIndex = -1;

                                if (opt?.text) {
                                    const nama = opt.text.toLowerCase();
                                    this.targetDikunci = (
                                        nama.includes('pengawasan') ||
                                        nama.includes('supervisi') ||
                                        nama.includes('perjalanan dinas')
                                    );
                                    if (this.targetDikunci) {
                                        this.target = 1;
                                    }
                                } else {
                                    this.targetDikunci = false;
                                }
                            },

                            highlightNext() {
                                if (this.highlightedIndex < this.filteredOptions.length - 1)
                                    this.highlightedIndex++;
                            },

                            highlightPrev() {
                                if (this.highlightedIndex > 0)
                                    this.highlightedIndex--;
                            },

                            selectHighlighted() {
                                if (this.highlightedIndex >= 0)
                                    this.selectJenis(this.filteredOptions[this.highlightedIndex]);
                            },

                            get jenisId() { return Number(this.idJenisKegiatan || 0) },
                            get isLainnya() { return this.idJenisKegiatan === 'LAINNYA' },
                            get jenisSelected() { return this.jenisId > 0 || this.isLainnya },

                            syncState() {
                                this.showToggle = (window.jenisButuhMap?.[this.jenisId] == 1);
                                this.isOther = this.idJenisKegiatan === 'LAINNYA';

                                if (!this.showToggle) {
                                    this.butuhDl = false;
                                    this.butuhTranslok = false;
                                    return;
                                }

                                if (!this.butuhDl && !this.butuhTranslok) {
                                    this.butuhDl = true;
                                    this.butuhTranslok = true;
                                }
                            },

                            toggleDL() {
                                this.butuhDl = !this.butuhDl;
                                if(this.butuhDl) this.butuhTranslok = false;
                            },

                            toggleTranslok() {
                                this.butuhTranslok = !this.butuhTranslok;
                                if(this.butuhTranslok) this.butuhDl = false;
                            }
                        }"
                        x-effect="syncState()"
                        class="space-y-4">

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 md:col-span-1 pt-2">
                                Jenis Kegiatan
                            </label>

                            <div class="md:col-span-3 space-y-2">
                                <input type="hidden" name="detail_id_jenis_kegiatan[${sectionId}][]" x-model="idJenisKegiatan">

                                <div class="relative"
                                    @keydown.arrow-down.prevent="if(!open) open = true; else highlightNext()"
                                    @keydown.arrow-up.prevent="highlightPrev()"
                                    @keydown.enter.prevent="if(open) selectHighlighted(); else open = true"
                                    @keydown.escape="open = false">

                                    <!-- BUTTON -->
                                    <button type="button"
                                        @click="
                                            open = !open;
                                            if(open){
                                                search = '';
                                                highlightedIndex = -1;
                                            }
                                        "
                                        @click.outside="open = false"
                                        class="flex h-10 w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs focus:ring-2 focus:ring-brand-500/20 dark:border-gray-600 dark:bg-gray-700">

                                        <span x-text="selectText"
                                            class="truncate"
                                            :class="!idJenisKegiatan ? 'text-gray-400' : 'text-gray-800 dark:text-gray-300'">
                                        </span>

                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                                            <path stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                        </svg>
                                    </button>

                                    <!-- DROPDOWN -->
                                    <div x-show="open" x-transition
                                        class="absolute z-50 mt-1 max-h-56 w-full overflow-hidden rounded-lg border bg-white shadow-lg dark:bg-gray-800 border-gray-200 dark:border-gray-700">

                                        <!-- SEARCH (TANPA MERUBAH STYLE UTAMA) -->
                                        <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                            <input type="text"
                                                x-model="search"
                                                placeholder="Cari..."
                                                class="w-full px-2 py-1 text-xs rounded border dark:bg-gray-700 dark:border-gray-600"
                                                @input="highlightedIndex = -1">
                                        </div>

                                        <!-- LIST -->
                                        <div class="max-h-44 overflow-y-auto">
                                            <template x-for="(opt, index) in filteredOptions" :key="opt.id">
                                                <button type="button"
                                                    @click="selectJenis(opt)"
                                                    class="w-full px-3 py-2 text-left text-xs hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0"
                                                    :class="[opt.style, highlightedIndex === index ? 'bg-gray-50 dark:bg-gray-700' : '']">

                                                    <span x-text="opt.text"></span>
                                                </button>
                                            </template>

                                            <!-- EMPTY -->
                                            <div x-show="filteredOptions.length === 0"
                                                class="px-3 py-2 text-xs text-gray-500 text-center">
                                                Tidak ditemukan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- INPUT LAINNYA -->
                                <div x-show="isOther">
                                    <input
                                        type="text"
                                        name="detail_jenis_kegiatan_baru[${sectionId}][]"
                                        placeholder="Masukkan jenis kegiatan baru"
                                        class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 px-3 py-2 text-xs focus:ring-2 focus:ring-brand-500/20 transition" />
                                </div>

                                <!-- TOGGLE DL / TRANSLOK (TIDAK DIUBAH) -->
                                <div x-show="showToggle" x-transition class="pt-2">
                                    <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                        Pilih Salah Satu
                                    </label>

                                    <div class="flex gap-6">
                                        <div class="flex items-center gap-3">
                                            <button type="button" @click="toggleDL()" id="toggle-dl-btn-${detailId}"
                                                :class="butuhDl ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-700'"
                                                class="relative inline-flex h-6 w-12 items-center rounded-full transition">
                                                <span :class="butuhDl ? 'translate-x-[26px]' : 'translate-x-1'"
                                                    class="inline-block h-4 w-4 bg-white rounded-full transition"></span>
                                            </button>
                                            <span class="text-xs font-medium"
                                                :class="butuhDl ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">
                                                DL
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <button type="button" @click="toggleTranslok()"
                                                :class="butuhTranslok ? 'bg-teal-500' : 'bg-gray-300 dark:bg-gray-700'"
                                                class="relative inline-flex h-6 w-12 items-center rounded-full transition">
                                                <span :class="butuhTranslok ? 'translate-x-[26px]' : 'translate-x-1'"
                                                    class="inline-block h-4 w-4 bg-white rounded-full transition"></span>
                                            </button>
                                            <span class="text-xs font-medium"
                                                :class="butuhTranslok ? 'text-teal-600 dark:text-teal-400' : 'text-gray-500 dark:text-gray-400'">
                                                Translok
                                            </span>
                                        </div>
                                    </div>

                                    <p class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                                        Pilih salah satu: DL atau Translok.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="detail_butuh_dl[${sectionId}][]" :value="butuhDl ? 1 : 0">
                        <input type="hidden" name="detail_butuh_translok[${sectionId}][]" :value="butuhTranslok ? 1 : 0">
                        
                        <div class="space-y-3 pt-3">
                            <!-- Target Anggota -->
                            <div class="flex flex-col gap-2 md:flex-row md:items-start">
                        <label class="mt-2 block text-xs font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Target
                        </label>
                        <div class="md:w-3/4">
                            <input name="detail_target[${sectionId}][]" type="number" placeholder="Masukkan target"
                                x-model="target"
                                :readonly="targetDikunci"
                                :class="targetDikunci
                                    ? 'bg-gray-100 cursor-not-allowed text-gray-500 dark:bg-gray-700 dark:text-gray-400'
                                    : 'bg-transparent text-gray-800 dark:text-gray-300'"
                                @keydown="if (targetDikunci) $event.preventDefault()"
                                class="h-10 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-xs shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />

                            <p x-show="targetDikunci" x-transition
                                class="mt-2 text-[11px] text-amber-600 dark:text-amber-400 font-medium">
                                Target dikunci otomatis ke 1 untuk jenis kegiatan Pengawasan / Supervisi / Perjalanan Dinas
                            </p>
                        </div>
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
                                ::mindate="min_date"
                                ::maxdate="max_date"
                                inputClass="h-10 px-3 py-2 text-xs" />
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
                                ::mindate="min_date"
                                ::maxdate="max_date"
                                inputClass="h-10 px-3 py-2 text-xs" />
                        </div>
                    </div>
                </div></div>
            </div>
        `;

            const detailContainer = document.getElementById(`detail-${sectionId}`);
            if (detailContainer) {
                detailContainer.insertAdjacentHTML('beforeend', detailHTML);
                if (window.Alpine) Alpine.initTree(detailContainer);

                const detailElement = document.getElementById(detailId);
                if (detailElement) {
                    // Default values removed intentionally per requirement to show placeholder
                }
            }
        }

        function hapusDetailAnggota(detailId) {
            document.getElementById(detailId)?.remove();
        }

        // =============================================
        // SAVE ALL — dengan validasi frontend
        // =============================================

        async function saveAll(event) {
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

            // ---- CEK DUPLIKASI DL/TRANSLOK KE SERVER ----
            const anggotaPayloads = {}; // { 'idAnggota':  [{tanggal_mulai, tanggal_selesai, focusEl}]  }
            const sectionsDL = document.querySelectorAll('[id^="rk-anggota-"]:not([id*="-detail-"])');
            
            sectionsDL.forEach(section => {
                const sectionId = section.id;
                const detailItems = section.querySelectorAll('[id*="-detail-"]');
                detailItems.forEach(detail => {
                    const butuhDLInput = detail.querySelector('input[name*="detail_butuh_dl"]');
                    const butuhTranslokInput = detail.querySelector('input[name*="detail_butuh_translok"]');
                    const isDL = butuhDLInput && Number(butuhDLInput.value) === 1;
                    const isTrans = butuhTranslokInput && Number(butuhTranslokInput.value) === 1;
                    
                    if (isDL || isTrans) {
                        const idAnggotaInput = detail.querySelector('input[name*="detail_id_anggota"]');
                        const idAnggota = idAnggotaInput ? idAnggotaInput.value : '';
                        
                        const mulInput = detail.querySelector(`input[name="detail_tanggal_mulai[${sectionId}][]"]`);
                        const selInput = detail.querySelector(`input[name="detail_tanggal_selesai[${sectionId}][]"]`);
                        
                        if (idAnggota && mulInput && selInput && mulInput.value && selInput.value) {
                            if(!anggotaPayloads[idAnggota]) anggotaPayloads[idAnggota] = [];
                            anggotaPayloads[idAnggota].push({
                                tanggal_mulai: mulInput.value,
                                tanggal_selesai: selInput.value,
                                focusElMulai: mulInput,
                                focusElSelesai: selInput
                            });
                        }
                    }
                });
            });

            const anggotaKeys = Object.keys(anggotaPayloads);
            if (anggotaKeys.length > 0) {
                try {
                    const btnSubmit = event && event.target ? event.target : document.getElementById('saveAllButton');
                    const origText = btnSubmit ? btnSubmit.innerHTML : '';
                    if(btnSubmit) { btnSubmit.innerHTML = 'Memeriksa...'; btnSubmit.disabled = true; }

                    const token = document.querySelector('input[name="_token"]')?.value;
                    let hasDbDuplicate = false;

                    for(const idAngg of anggotaKeys) {
                         const apiDates = anggotaPayloads[idAngg].map(p => ({
                             tanggal_mulai: p.tanggal_mulai,
                             tanggal_selesai: p.tanggal_selesai
                         }));

                         const payload = {
                             id_anggota: idAngg,
                             dates: apiDates,
                             _token: token
                         };

                         const response = await fetch('/penugasan/check-duplicate-dates', {
                             method: 'POST',
                             headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                             body: JSON.stringify(payload)
                         });

                         if (response.ok) {
                             const result = await response.json();
                             if (result.has_duplicate) {
                                 hasDbDuplicate = true;
                                 result.duplicates.forEach((d) => {
                                     // Match to the form input for scrolling
                                     const matchingPayload = anggotaPayloads[idAngg].find(p => p.tanggal_mulai === d.requested_mulai && p.tanggal_selesai === d.requested_selesai);
                                     
                                     let targetEl = null;
                                     if (matchingPayload) {
                                         // Backend now passed `is_selesai` bool flag to indicate which end triggers the conflict
                                         targetEl = d.is_selesai
                                                    ? matchingPayload.focusElSelesai 
                                                    : matchingPayload.focusElMulai;
                                     }

                                     errors.push({
                                         message: d.message,
                                         focusEl: targetEl
                                     });
                                 });
                             }
                         }
                    }

                    if(btnSubmit) { btnSubmit.innerHTML = origText; btnSubmit.disabled = false; }

                    if (hasDbDuplicate) {
                        showValidationBanner(errors);
                        return; // batalkan konfirmasi karna ada error duplicate
                    }
                } catch(e) {
                    console.error("Gagal mengecek duplikasi tanggal", e);
                }
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
                        detailAnggotas = detailContainer.querySelectorAll('.rk-detail-item');
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

                            const jenisInput = detail.querySelector(
                                'input[name*="detail_id_jenis_kegiatan"]');
                            const jenisBaruInput = detail.querySelector(
                                'input[name*="detail_jenis_kegiatan_baru"]');
                            let jenisKegiatan = '-';
                            if (jenisInput) {
                                if (jenisInput.value === 'LAINNYA') {
                                    jenisKegiatan = jenisBaruInput && jenisBaruInput.value ? jenisBaruInput
                                        .value : '-';
                                } else {
                                    const jenisSpan = jenisInput.nextElementSibling?.querySelector('button span.truncate');
                                    jenisKegiatan = jenisSpan ? jenisSpan.innerText : '-';
                                }
                            }

                            const butuhDLInput = detail.querySelector('input[name*="detail_butuh_dl"]');
                            const butuhDL = butuhDLInput ? Number(butuhDLInput.value) === 1 : false;

                            const butuhTranslokInput = detail.querySelector('input[name*="detail_butuh_translok"]');
                            const butuhTranslok = butuhTranslokInput ? Number(butuhTranslokInput.value) === 1 : false;

                            let butuhDLBadge = `<span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-900/40 px-2 py-1 text-[11px] font-medium text-gray-600 dark:text-gray-400">Tidak Perlu DL / Translok</span>`;

                            if (butuhDL) {
                                butuhDLBadge = `<span class="inline-flex items-center rounded-md bg-blue-100 dark:bg-blue-900/40 px-2 py-1 text-[11px] font-medium text-blue-700 dark:text-blue-400">✓ Perlu Dinas Luar</span>`;
                            } else if (butuhTranslok) {
                                butuhDLBadge = `<span class="inline-flex items-center rounded-md bg-teal-100 dark:bg-teal-900/40 px-2 py-1 text-[11px] font-medium text-teal-700 dark:text-teal-400">✓ Perlu Translok</span>`;
                            }

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
