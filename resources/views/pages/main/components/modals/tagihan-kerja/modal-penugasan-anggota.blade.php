<!-- Modal Penugasan Anggota -->
<x-ui.smart-modal id="modal-penugasan-anggota" class="max-w-2xl"
    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-penugasan-anggota') return;

        mode = $event.detail.mode ?? 'create';
        itemKey = $event.detail.key ?? null;
        formData = $event.detail.data ?? {
            id_sub_kegiatan: '',
            nama_sub_kegiatan: '',
            id_anggota: '',
            nama_anggota: '',
            id_jenis_kegiatan: '',
            jenis_kegiatan: '',
            target: '',
            satuan_target: '',
            butuh_dl: 0,
            butuh_translok: 0,
            tanggal_mulai: '',
            tanggal_selesai: '',
            min_date: '',
            max_date: '',
        }">
    <form id="addPenugasanForm"
        :action="mode === 'edit'
            ?
            `/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan/${itemKey}` :
            `/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan`"
        method="POST" class="grid grid-cols-1 gap-y-5">
        @csrf
        <template x-if="mode === 'edit'">
            @method('PUT')
        </template>

        <div
            class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden
                rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">

            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white"
                    x-text="mode === 'create' ? 'Tambah Anggota' : 'Edit Data Anggota'"></h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                    x-text="mode === 'create' ? 'Tambahkan penugasan kepada anggota' : 'Edit anggota yang sudah ditugaskan'">
                </p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900">
                {{-- ====== VALIDATION BANNER ====== --}}
                <div id="validationBannerPenugasan"
                    class="hidden rounded-xl border border-red-300 dark:border-red-500/40 bg-red-50 dark:bg-red-500/10 px-4 py-3">
                    <p class="text-sm font-medium text-red-700 dark:text-red-400 mb-1">
                        ⚠ Ada beberapa field yang belum diisi atau tidak valid:
                    </p>
                    <ul id="validationListPenugasan" class="list-disc pl-5 space-y-1"></ul>
                </div>
                {{-- ====== END VALIDATION BANNER ====== --}}

                <!-- Nama Sub Kegiatan (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Sub Kegiatan
                    </label>

                    <input type="text" :value="formData.nama_sub_kegiatan" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                </div>

                {{-- Nama Anggota --}}
                <div x-data="pegawaiDropdown()"
                    @open-smart-modal.window="
                        if ($event.detail.modalId !== 'modal-penugasan-anggota') return;
                        initFromModal($event.detail);
                    "
                    class="mb-4">

                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Pegawai <span class="text-red-500">*</span>
                    </label>

                    <!-- Hidden ID Pegawai (WAJIB buat submit) -->
                    <input type="hidden" name="id_anggota" x-model="selectedId">

                    <!-- Input Visible -->
                    <div class="relative">
                        <input type="text" x-model="search" @click="mode === 'create' && (open = true)"
                            @input="mode === 'create' && (open = true)" @keydown.escape="open = false"
                            :readonly="mode === 'edit'" placeholder="Pilih pegawai..."
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">

                        <!-- Dropdown -->
                        <div x-show="open && mode === 'create'" x-transition @click.outside="open = false"
                            class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto
                                    rounded-lg border border-gray-200 bg-white shadow-lg
                                    dark:border-gray-700 dark:bg-gray-800">
                            <template x-if="filteredPegawais.length === 0">
                                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 italic">
                                    Nama Pegawai tidak ditemukan
                                </div>
                            </template>

                            <template x-for="pegawai in filteredPegawais" :key="pegawai.id_pegawai">
                                <button type="button" @click="selectPegawai(pegawai)"
                                    class="flex w-full items-center px-4 py-3 text-left text-sm
                                            hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                    <div class="font-medium text-gray-800 dark:text-gray-200"
                                        x-text="pegawai.nama_pegawai"></div>
                                </button>
                            </template>
                            {{-- --}}
                        </div>
                    </div>
                </div>

                {{-- Toggle Gabungan untuk DL dan Translok --}}
                <div x-data="{
                    open: false,
                    isOther: false,
                    options: [
                        @foreach ($jenisKegiatans as $jenis)
                            {
                                id: '{{ $jenis->id }}',
                                text: '{{ addslashes($jenis->jenis_kegiatan) }} ({{ $jenis->kategori }})',
                                style: '{{ $jenis->kategori === 'Utama' ? 'text-green-700 font-medium dark:text-green-300' : 'text-orange-700 dark:text-orange-300' }}'
                            },
                        @endforeach
                        {
                            id: 'LAINNYA',
                            text: '➕ Lainnya',
                            style: 'text-blue-700 font-medium dark:text-blue-300' }
                    ],
                    get selectText() {
                        if (!formData.id_jenis_kegiatan) return '-- Pilih Jenis Kegiatan --';
                        let opt = this.options.find(o => o.id == formData.id_jenis_kegiatan);
                        return opt ? opt.text : formData.jenis_kegiatan || '-- Pilih Jenis Kegiatan --';
                    },
                    selectJenis(opt) {
                        formData.id_jenis_kegiatan = opt.id;
                            this.isOther = (opt.id === 'LAINNYA');
                            this.open = false;
                        }
                    }"
                    @open-smart-modal.window="
                        if ($event.detail.modalId === 'modal-penugasan-anggota' && mode === 'edit') {
                            isOther = (formData.id_jenis_kegiatan === 'LAINNYA');
                        }">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Jenis Kegiatan <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" id="jenis_kegiatan_select" name="id_jenis_kegiatan"
                        x-model="formData.id_jenis_kegiatan">

                    <div class="relative mb-4">
                        <button type="button" @click="open = !open" @click.outside="open = false"
                            class="flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800">
                            <span x-text="selectText" class="truncate"
                                :class="!formData.id_jenis_kegiatan ? 'text-gray-400' : 'text-gray-800 dark:text-gray-200'">
                            </span>
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                                <path stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition
                            class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto rounded-lg border bg-white shadow-lg dark:bg-gray-800">
                            <template x-for="opt in options" :key="opt.id">
                                <button type="button" @click="selectJenis(opt)"
                                    class="w-full px-4 py-3 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 border-b last:border-0"
                                    :class="opt.style">
                                    <span x-text="opt.text"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <p class="text-xs text-red-600 mt-1 hidden" data-for="jenis_kegiatan_select">
                        Jenis Kegiatan wajib dipilih
                    </p>

                    <!-- INPUT JENIS KEGIATAN BARU -->
                    <div x-show="isOther" x-transition>
                        <input type="text" name="jenis_kegiatan_baru" placeholder="Masukkan jenis kegiatan baru"
                            class="h-11 w-full mb-4 rounded-lg border px-4 text-sm dark:bg-gray-800">
                    </div>
                </div>

                <!-- TOGGLE DL / TRANSLOK -->
                <div x-data="{
                        butuhDl: false,
                        butuhTranslok: false,

                        wajibJenis: [3, 4, 5, 6],

                        get jenisId() {
                            return Number(formData?.id_jenis_kegiatan || 0)
                        },

                        get showToggle() {
                            return this.wajibJenis.includes(this.jenisId)
                        },

                        syncState() {
                            const dlDB = Boolean(Number(formData?.butuh_dl ?? 0))
                            const translokDB = Boolean(Number(formData?.butuh_translok ?? 0))

                            if (!this.showToggle) {
                                this.butuhDl = false
                                this.butuhTranslok = false
                                return
                            }

                            if (mode === 'create') {
                                this.butuhDl = true
                                this.butuhTranslok = false
                            } else {
                                this.butuhDl = dlDB
                                this.butuhTranslok = translokDB
                            }
                        },

                        toggleDL() {
                            this.butuhDl = true
                            this.butuhTranslok = false
                        },

                        toggleTranslok() {
                            this.butuhTranslok = true
                            this.butuhDl = false
                        }
                    }" x-init="
                        syncState();

                        // 🔥 Watch perubahan jenis kegiatan
                        $watch(() => formData.id_jenis_kegiatan, () => {
                            syncState();
                        });

                        // 🔥 Watch ketika modal edit inject data
                        $watch(() => formData.butuh_dl, () => syncState());
                        $watch(() => formData.butuh_translok, () => syncState());" x-show="showToggle"
                        x-transition class="mb-4">
                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Pilih Salah Satu
                    </label>

                    <div class="flex gap-6">

                        <!-- DL -->
                        <div class="flex items-center gap-3">
                            <button type="button" @click="toggleDL()" :class="butuhDl ? 'bg-blue-500' : 'bg-gray-300'"
                                class="relative inline-flex h-7 w-14 items-center rounded-full transition">
                                <span :class="butuhDl ? 'translate-x-7' : 'translate-x-1'"
                                    class="inline-block h-5 w-5 bg-white rounded-full transition">
                                </span>
                            </button>

                            <span class="text-sm font-medium" :class="butuhDl ? 'text-blue-600' : 'text-gray-500'">
                                DL
                            </span>
                        </div>

                        <!-- TRANSLOK -->
                        <div class="flex items-center gap-3">
                            <button type="button" @click="toggleTranslok()"
                                :class="butuhTranslok ? 'bg-teal-500' : 'bg-gray-300'"
                                class="relative inline-flex h-7 w-14 items-center rounded-full transition">
                                <span :class="butuhTranslok ? 'translate-x-7' : 'translate-x-1'"
                                    class="inline-block h-5 w-5 bg-white rounded-full transition">
                                </span>
                            </button>

                            <span class="text-sm font-medium"
                                :class="butuhTranslok ? 'text-teal-600' : 'text-gray-500'">
                                Translok
                            </span>
                        </div>

                    </div>

                    <p class="mt-2 text-xs text-gray-500">
                        Pilih salah satu: DL atau Translok.
                    </p>

                    <input type="hidden" name="butuh_dl" :value="butuhDl ? 1 : 0">
                    <input type="hidden" name="butuh_translok" :value="butuhTranslok ? 1 : 0">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Target <span class="text-red-500">*</span>
                    </label>
                    <input type="number" x-model="formData.target" name="target" id="target"
                        placeholder="Misalnya : 200"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Satuan Target <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="formData.satuan_target" name="satuan_target" id="satuan_target"
                        placeholder="Misalnya : Dokumen, Kegiatan, dll"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                <div class="flex flex-row justify-between items-center gap-6">
                    <div class="flex-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <x-form.date-picker x-model="formData.tanggal_mulai" id="tanggal_mulai" name="tanggal_mulai"
                            placeholder="Tanggal Mulai" ::minDate="formData.min_date" ::maxDate="formData.max_date"
                            minBind="formData.min_date" maxBind="formData.max_date" />
                    </div>

                    <div class="flex-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tanggal Berakhir (Deadline) <span class="text-red-500">*</span>
                        </label>
                        <x-form.date-picker x-model="formData.tanggal_selesai" id="tanggal_selesai"
                            name="tanggal_selesai" placeholder="Tanggal Selesai" ::minDate="formData.min_date" ::maxDate="formData.max_date"
                            minBind="formData.min_date" maxBind="formData.max_date" />
                    </div>
                </div>

                <div id="tglPenugasanWrapper" class="mt-6 hidden">
                    <h3 class="text-sm font-semibold text-gray-600 mb-3">
                        Tanggal Penugasan Tambahan
                    </h3>

                    <div id="tglPenugasanContainer" class="space-y-4"></div>
                </div>

                <!-- TOMBOL TAMBAH RK ANGGOTA -->
                <div class="flex flex-col gap-2 md:flex-row md:items-center mt-4">
                    <div class="md:w-1/4"></div>
                    <div class="md:w-3/4">
                        <button type="button" @click="tambahTanggalPenugasan()"
                            class="flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M9 3.75C9.41421 3.75 9.75 4.08579 9.75 4.5V8.25H13.5C13.9142 8.25 14.25 8.58579 14.25 9C14.25 9.41421 13.9142 9.75 13.5 9.75H9.75V13.5C9.75 13.9142 9.41421 14.25 9 14.25C8.58579 14.25 8.25 13.9142 8.25 13.5V9.75H4.5C4.08579 9.75 3.75 9.41421 3.75 9C3.75 8.58579 4.08579 8.25 4.5 8.25H8.25V4.5C8.25 4.08579 8.58579 3.75 9 3.75Z"
                                    fill="" />
                            </svg>
                            Tambah Tanggal Penugasan
                        </button>
                    </div>
                </div>

            </div>
            <!-- FOOTER -->
            <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-700">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button @click="open = false" type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        Batal
                    </button>

                    <button x-show="mode === 'create'" id="savePenugasanButton" type="button"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        Simpan Penugasan
                    </button>

                    <button x-show="mode !== 'create'" type="submit"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto dark:bg-brand-600 dark:hover:bg-brand-700">
                        Ubah Data Penugasan
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-ui.smart-modal>
<script>
    function pegawaiDropdown() {
        return {
            open: false,
            search: '',
            selectedId: '',
            mode: 'create',

            pegawais: @js(
    $pegawais->map(
        fn($p) => [
            'id_pegawai' => $p->id_pegawai,
            'nama_pegawai' => $p->nama_pegawai,
        ],
    ),
),

            initFromModal(detail) {
                this.mode = detail.mode ?? 'create';

                if (this.mode === 'edit') {
                    this.selectedId = detail.data.id_anggota;
                    this.search = detail.data.nama_anggota;
                    this.open = false;
                } else {
                    this.selectedId = '';
                    this.search = '';
                    this.open = true;
                }
            },

            get filteredPegawais() {
                if (!this.search) return this.pegawais;

                return this.pegawais.filter(p =>
                    p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            selectPegawai(p) {
                this.selectedId = p.id_pegawai;
                this.search = p.nama_pegawai;
                this.open = false;
            }
        }
    }

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
        const banner = document.getElementById('validationBannerPenugasan');
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

    function validateFormPenugasan() {
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

        // // --- 1. Anggota ---
        // const AnggotaIdInput = document.querySelector('input[name="id_anggota"]');
        // const AnggotaSearchInput = document.getElementById('AnggotaSearchInput');
        // const AnggotaErrorMsg = AnggotaSearchInput?.closest('.relative')?.querySelector('.field-error-msg');
        // if (!AnggotaIdInput?.value) {
        //     addError(
        //         'Anggota wajib dipilih dari daftar dropdown (klik nama dari hasil pencarian)',
        //         AnggotaSearchInput, AnggotaSearchInput,
        //         AnggotaErrorMsg
        //     );
        // }

        // --- 2. Jenis Kegiatan Pada Penugasan  ---
        const jenisKegiatanSelect = document.getElementById('jenis_kegiatan_select');
        const jenisKegiatanSelectErrorMsg = jenisKegiatanSelect?.closest('.md\\:w-3\\/4')?.querySelector(
            '.field-error-msg');
        if (!jenisKegiatanSelect?.value?.trim()) {
            addError(
                'Jenis Kegiatan pada penugasan wajib diisi',
                jenisKegiatanSelect, jenisKegiatanSelect,
                jenisKegiatanSelectErrorMsg
            );
        }

        // // --- 3. Jenis Kegiatan Pada Penugasan (LAINNYA)  ---
        // const jenisKegiatanBaru = document.getElementById('jenis_kegiatan_baru');
        // const jenisKegiatanBaruErrorMsg = jenisKegiatanBaru?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        // if (!jenisKegiatanBaru?.value?.trim()) {
        //     addError(
        //         'Jenis Kegiatan Baru wajib diisi jika opsi ini dipilih',
        //         jenisKegiatanBaru, jenisKegiatanBaru,
        //         jenisKegiatanBaruErrorMsg
        //     );
        // }

        // --- 4. Target Sub Kegiatan ---
        const targetPenugasan = document.getElementById('target');
        const targetPenugasanErrorMsg = targetPenugasan?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        if (!targetPenugasan?.value?.trim()) {
            addError(
                'Target Penugasan wajib diisi',
                targetPenugasan, targetPenugasan,
                targetPenugasanErrorMsg
            );
        }

        // --- 5. Satuan Target Sub Kegiatan ---
        const satuanTargetPenugasan = document.getElementById('satuan_target');
        const satuanTargetPenugasanErrorMsg = satuanTargetPenugasan?.closest('.md\\:w-3\\/4')?.querySelector(
            '.field-error-msg');
        if (!satuanTargetPenugasan?.value?.trim()) {
            addError(
                'Satuan Target Penugasan wajib diisi',
                satuanTargetPenugasan, satuanTargetPenugasan,
                satuanTargetPenugasanErrorMsg
            );
        }

        // --- 6. Tanggal Mulai Sub Kegiatan ---
        const tanggalMulaiPenugasan = document.getElementById('tanggal_mulai');
        const tanggalMulaiPenugasanErrorMsg = tanggalMulaiPenugasan?.closest('.md\\:w-3\\/4')?.querySelector(
            '.field-error-msg');
        if (!tanggalMulaiPenugasan?.value?.trim()) {
            addError(
                'Tanggal Mulai Penugasan wajib dipilih',
                tanggalMulaiPenugasan, tanggalMulaiPenugasan,
                tanggalMulaiPenugasanErrorMsg
            );
        }

        // --- 7. Tanggal Selesai Sub Kegiatan ---
        const tanggalSelesaiPenugasan = document.getElementById('tanggal_selesai');
        const tanggalSelesaiPenugasanErrorMsg = tanggalSelesaiPenugasan?.closest('.md\\:w-3\\/4')?.querySelector(
            '.field-error-msg');
        if (!tanggalSelesaiPenugasan?.value?.trim()) {
            addError(
                'Tanggal Selesai Penugasan wajib dipilih',
                tanggalSelesaiPenugasan, tanggalSelesaiPenugasan,
                tanggalSelesaiPenugasanErrorMsg
            );
        } else if (tanggalMulaiPenugasan?.value && tanggalSelesaiPenugasan.value < tanggalMulaiPenugasan.value) {
            addError(
                'Tanggal Selesai harus sesudah atau sama dengan Tanggal Mulai',
                tanggalSelesaiPenugasan, tanggalSelesaiPenugasan,
                tanggalSelesaiPenugasanErrorMsg
            );
        }

        return errors;
    }

    function showValidationPenugasanBanner(errors) {
        const banner = document.getElementById('validationBannerPenugasan');
        const list = document.getElementById('validationListPenugasan');
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

    function savePenugasan(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // ---- Jalankan validasi ----
        const errors = validateFormPenugasan();
        if (errors.length > 0) {
            showValidationPenugasanBanner(errors);
            return; // Berhenti — tidak buka modal konfirmasi
        } else {
            confirmSavePenugasan();
        }
    }

    function confirmSavePenugasan() {
        const form = document.getElementById('addPenugasanForm');
        if (!form) {
            alert('Form tidak ditemukan');
            return;
        }
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('savePenugasanButton')?.addEventListener('click', savePenugasan);
    });

    let tanggalPenugasanCounter = 0;
    let detailAnggotaCounter = {};

    function tambahTanggalPenugasan() {
        tanggalPenugasanCounter++;
        const sectionIndex = tanggalPenugasanCounter;
        const sectionId = `tgl-penugasan-${sectionIndex}`;
        detailAnggotaCounter[sectionId] = 0;

        const wrapper = document.getElementById('tglPenugasanWrapper');
        const container = document.getElementById('tglPenugasanContainer');

        // tampilkan wrapper kalau pertama kali
        if (wrapper.classList.contains('hidden')) {
            wrapper.classList.remove('hidden');
        }

        const sectionHTML = `
            <div id="${sectionId}" class="relative border border-gray-200 rounded-lg p-4 bg-white">
                <button
                    type="button"
                    onclick="hapusTanggalPenugasan('${sectionId}')"
                    class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition">
                    ✕
                </button>
                <input type="hidden" name="rk_section_keys[]" value="${sectionId}">

                <div class="flex flex-row justify-between items-center gap-6">
                    <div class="flex-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <x-form.date-picker
                            id="tanggal_mulai_${sectionIndex}"
                            name="tanggal_mulai_list[]"
                            placeholder="Tanggal Mulai"
                            ::minDate="formData.min_date"
                            ::maxDate="formData.max_date"
                            minBind="formData.min_date"
                            maxBind="formData.max_date"
                        />
                    </div>

                    <div class="flex-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tanggal Berakhir (Deadline) <span class="text-red-500">*</span>
                        </label>
                        <x-form.date-picker
                            id="tanggal_selesai_${sectionIndex}"
                            name="tanggal_selesai_list[]"
                            placeholder="Tanggal Selesai"
                            ::minDate="formData.min_date"
                            ::maxDate="formData.max_date"
                            minBind="formData.min_date"
                            maxBind="formData.max_date"
                        />
                    </div>
                </div>
                <div id="detail-${sectionId}" class="space-y-4"></div>
            </div>
        `;

        // const container = document.getElementById('tglPenugasanContainer');
        container.insertAdjacentHTML('beforeend', sectionHTML);

        setTimeout(() => {
            document.getElementById(sectionId)?.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }, 100);
    }

    function hapusTanggalPenugasan(sectionId) {
        // hapus section
        document.getElementById(sectionId)?.remove();

        const container = document.getElementById('tglPenugasanContainer');
        const wrapper = document.getElementById('tglPenugasanWrapper');

        // kalau sudah tidak ada item lagi
        if (!container || container.children.length === 0) {
            wrapper.classList.add('hidden');
        }
    }
</script>
