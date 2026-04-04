<x-ui.smart-modal id="modal-kegiatan" class="max-w-2xl"
    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-kegiatan') return;

        mode = $event.detail.mode ?? 'create';
        itemKey = $event.detail.key ?? null;

        let baseData = $event.detail.data || {};
        formData = {
            id_bidang: '{{ $bidang->id_bidang }}',
            nama_bidang: '{{ $bidang->nama_bidang }}',
            id_penanggung_jawab: '',
            nama_penanggung_jawab: '',
            tahun_kegiatan: '',
            rk_jpt: '',
            iki_jpt: '',
            nama_rk_kegiatan: '',
            ikiOptions: [],
            ...baseData
        };

        if(formData.rk_jpt) {
            fetch(`/rencana-indikator-jpt/${formData.rk_jpt}/indikator`)
                .then(res => res.json())
                .then(data => {
                    formData.ikiOptions = data;
                    const selectedIki = formData.iki_jpt;
                    setTimeout(() => {
                        formData.iki_jpt = selectedIki;
                    }, 50);
                });
        }

        // Sinkronkan state untuk dropdown autocomplete
        selectedId = formData.id_penanggung_jawab ?? '';
        search = formData.nama_penanggung_jawab ?? '';
    ">

    <form
        id="addKegiatanForm"
        :action="mode === 'edit'
            ? `/kegiatan/${itemKey}`
            : '{{ route('kegiatan.store', $bidang->slug) }}'"
        method="POST" class="grid grid-cols-1 gap-y-5">
        @csrf
        <template x-if="mode === 'edit'">
            @method('PUT')
        </template>
        <div class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden
                rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">
            <!-- HEADER -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white"
                    x-text="mode === 'create' ? 'Tambah Kegiatan/RK Ketua' : 'Edit Kegiatan/RK Ketua'"></h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                    x-text="mode === 'create' ? 'Masukkan kegiatan yang baru' : 'Edit kegiatan yang sudah ada'"></p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900">
                {{-- ====== VALIDATION BANNER ====== --}}
                <div id="validationBannerKegiatan"
                    class="hidden rounded-xl border border-red-300 dark:border-red-500/40 bg-red-50 dark:bg-red-500/10 px-4 py-3">
                    <p class="text-sm font-medium text-red-700 dark:text-red-400 mb-1">
                        ⚠ Ada beberapa field yang belum diisi atau tidak valid:
                    </p>
                    <ul id="validationListKegiatan" class="list-disc pl-5 space-y-1"></ul>
                </div>
                {{-- ====== END VALIDATION BANNER ====== --}}

                <!-- Nama Bidang (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Bidang
                    </label>

                    <input type="text" value="{{ $bidang->nama_bidang }}" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                </div>

                <!-- ID Bidang (yang benar-benar dikirim ke backend) -->
                <input type="hidden" name="id_bidang" value="{{ $bidang->id_bidang }}">

                {{-- Nama Ketua Tim --}}
                <div x-data="ketuaDropdown()"
                    @open-smart-modal.window="
                        if ($event.detail.modalId !== 'modal-kegiatan') return;
                        initFromModal($event.detail);
                    " class="mb-4">

                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Ketua Tim <span class="text-red-500">*</span>
                    </label>

                    <!-- Hidden ID Pegawai (WAJIB buat submit) -->
                    <input type="hidden" name="id_penanggung_jawab" x-model="selectedId">

                    <!-- Input Visible -->
                    <div class="relative">
                        <input
                            type="text"
                            x-model="search"
                            @click="mode === 'create' && (open = true)"
                            @input="mode === 'create' && (open = true)"
                            @keydown.escape="open = false"
                            :readonly="mode === 'edit'"
                            placeholder="Pilih Ketua Kegiatan..."
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">

                        <!-- Dropdown -->
                        <div x-show="open && mode === 'create'"
                            x-transition
                            @click.outside="open = false"
                            class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto
                                    rounded-lg border border-gray-200 bg-white shadow-lg
                                    dark:border-gray-700 dark:bg-gray-800">
                            <template x-if="filteredPegawais.length === 0">
                                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 italic">
                                    Nama Ketua tidak ditemukan
                                </div>
                            </template>

                            <template x-for="pegawai in filteredPegawais" :key="pegawai.id_pegawai">
                                <button type="button"
                                        @click="selectPegawai(pegawai)"
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

                {{-- Tahun Kegiatan --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tahun Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="formData.tahun_kegiatan" name="tahun_kegiatan" id="tahun_kegiatan" placeholder="Contoh : 2026"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                {{-- Rencana JPT --}}
                <div x-data="{
                    open: false,
                    highlightedIndex: -1,
                    options: [
                        @foreach ($rkJpts as $rk)
                        { id: '{{ $rk->id }}', text: '{{ addslashes($rk->nama_rencana_jpt) }}' },
                        @endforeach
                    ],
                    get selectText() {
                        if (!formData.rk_jpt) return '-- Pilih RK JPT --';
                        let opt = this.options.find(o => o.id == formData.rk_jpt);
                        return opt ? opt.text : '-- Pilih RK JPT --';
                    },
                    selectRk(opt) {
                        formData.rk_jpt = opt.id;
                        formData.iki_jpt = '';
                        formData.ikiOptions = [];
                        if(formData.rk_jpt){
                            fetch(`/rencana-indikator-jpt/${formData.rk_jpt}/indikator`)
                                .then(res => res.json())
                                .then(data => formData.ikiOptions = data);
                        }
                        this.open = false;
                        this.highlightedIndex = -1;
                    },
                    highlightNext() { if (this.highlightedIndex < this.options.length - 1) this.highlightedIndex++; },
                    highlightPrev() { if (this.highlightedIndex > 0) this.highlightedIndex--; },
                    selectHighlighted() { if (this.highlightedIndex >= 0) this.selectRk(this.options[this.highlightedIndex]); }
                }">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Rencana JPT <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" id="rk_jpt" name="rk_jpt" x-model="formData.rk_jpt">
                    <div class="relative mb-4"
                        @keydown.arrow-down.prevent="if(!open) open = true; else highlightNext()"
                        @keydown.arrow-up.prevent="highlightPrev()"
                        @keydown.enter.prevent="if(open) selectHighlighted(); else open = true"
                        @keydown.escape="open = false">
                        <button type="button" @click="open = !open" @click.outside="open = false"
                            class="flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <span x-text="selectText" class="truncate" :class="!formData.rk_jpt ? 'text-gray-400' : 'text-gray-800 dark:text-gray-300'"></span>
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"><path stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                        </button>
                        <div x-show="open" x-transition class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto rounded-lg border bg-white shadow-lg dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                            <template x-for="(opt, index) in options" :key="opt.id">
                                <button type="button" @click="selectRk(opt)"
                                    class="w-full px-4 py-3 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0"
                                    :class="highlightedIndex === index ? 'bg-gray-50 dark:bg-gray-700' : ''">
                                    <span x-text="opt.text" class="text-gray-800 dark:text-gray-300"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Indikator JPT --}}
                <div x-data="{
                    open: false,
                    highlightedIndex: -1,
                    get selectText() {
                        if (!formData.iki_jpt) return formData.rk_jpt ? '-- Pilih IKI JPT --' : '-- Harap pilih RK JPT dulu --';
                        let opt = formData.ikiOptions.find(o => o.id == formData.iki_jpt);
                        return opt ? opt.nama_indikator_jpt : '-- Pilih IKI JPT --';
                    },
                    selectIki(opt) {
                        formData.iki_jpt = opt.id;
                        this.open = false;
                        this.highlightedIndex = -1;
                    },
                    highlightNext() { if (this.highlightedIndex < formData.ikiOptions.length - 1) this.highlightedIndex++; },
                    highlightPrev() { if (this.highlightedIndex > 0) this.highlightedIndex--; },
                    selectHighlighted() { if (this.highlightedIndex >= 0) this.selectIki(formData.ikiOptions[this.highlightedIndex]); }
                }">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Indikator JPT <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" id="iki_jpt" name="iki_jpt" x-model="formData.iki_jpt">
                    <div class="relative mb-4"
                        @keydown.arrow-down.prevent="if(!open) open = true; else highlightNext()"
                        @keydown.arrow-up.prevent="highlightPrev()"
                        @keydown.enter.prevent="if(open) selectHighlighted(); else open = true"
                        @keydown.escape="open = false">
                        <button type="button" @click="if(formData.ikiOptions.length > 0) open = !open" @click.outside="open = false"
                            class="flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <span x-text="selectText" class="truncate" :class="!formData.iki_jpt ? 'text-gray-400' : 'text-gray-800 dark:text-gray-300'"></span>
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"><path stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                        </button>
                        <div x-show="open" x-transition class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto rounded-lg border bg-white shadow-lg dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                            <template x-for="(opt, index) in formData.ikiOptions" :key="opt.id">
                                <button type="button" @click="selectIki(opt)"
                                    class="w-full px-4 py-3 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0"
                                    :class="highlightedIndex === index ? 'bg-gray-50 dark:bg-gray-700' : ''">
                                    <span x-text="opt.nama_indikator_jpt" class="text-gray-800 dark:text-gray-300"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="formData.nama_rk_kegiatan" name="nama_rk_kegiatan" id="nama_rk_kegiatan"
                        placeholder="Contoh : SNLIK2026"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>
            </div>

            <!-- FOOTER -->
            <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-700">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button @click="open = false" type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        Batal
                    </button>

                    <button x-show="mode === 'create'" id="saveKegiatanButton" type="button"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        Simpan Kegiatan
                    </button>

                    <button x-show="mode !== 'create'" type="submit"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto dark:bg-brand-600 dark:hover:bg-brand-700">
                        Ubah Data Kegiatan
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-ui.smart-modal>

<script>
    function ketuaDropdown() {
        @php
            $user = auth()->user();
            $filteredKetua = $ketuaTims;

            if ($user && method_exists($user, 'isActiveRole')) {
                if ($user->isActiveRole('Admin') || $user->isActiveRole('Pimpinan')) {
                    $filteredKetua = $ketuaTims;
                } elseif ($user->isActiveRole('Ketua Tim')) {
                    $filteredKetua = $ketuaTims->filter(fn($p) => $p->id_pegawai == $user->id_pegawai);
                }
            }
        @endphp

        return {
            open: false,
            search: '',
            selectedId: '',
            mode: 'create',

            ketuaTims: @js(
                $filteredKetua->map(fn($p) => [
                    'id_pegawai'   => $p->id_pegawai,
                    'nama_pegawai' => $p->nama_pegawai
                ])->values()
            ),

            initFromModal(detail) {
                this.mode = detail.mode ?? 'create';

                if (this.mode === 'edit') {
                    this.selectedId = detail.data.id_penanggung_jawab;
                    this.search     = detail.data.nama_penanggung_jawab;
                    this.open       = false;
                } else {
                    if (this.ketuaTims.length === 1) {
                        this.selectedId = this.ketuaTims[0].id_pegawai;
                        this.search     = this.ketuaTims[0].nama_pegawai;
                        this.open       = false;
                    } else {
                        this.selectedId = '';
                        this.search     = '';
                        this.open       = true;
                    }
                }
            },

            get filteredPegawais() {
                if (!this.search) return this.ketuaTims;

                return this.ketuaTims.filter(p =>
                    p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            selectPegawai(p) {
                this.selectedId = p.id_pegawai;
                this.search     = p.nama_pegawai;
                this.open       = false;
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
        const banner = document.getElementById('validationBannerKegiatan');
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

    function validateFormKegiatan() {
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
        const tahunKegiatan = document.getElementById('tahun_kegiatan');
        if (!tahunKegiatan?.value?.trim()) {
            addError(
                'Tahun Kegiatan wajib diisi',
                tahunKegiatan, tahunKegiatan,
                tahunKegiatan?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg')
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
        const ikiJptHidden = document.getElementById('iki_jpt_hidden');
        if (!ikiJptSelect?.value || !ikiJptHidden?.value) {
            addError(
                'Indikator JPT wajib dipilih',
                ikiJptSelect, ikiJptSelect
            );
        }

        // // --- 4. Ketua ---
        // const ketuaIdInput = document.querySelector('input[name="id_penanggung_jawab"]');
        // const ketuaSearchInput = document.getElementById('ketuaSearchInput');
        // const ketuaErrorMsg = ketuaSearchInput?.closest('.relative')?.querySelector('.field-error-msg');
        // if (!ketuaIdInput?.value) {
        //     addError(
        //         'Ketua wajib dipilih dari daftar dropdown (klik nama dari hasil pencarian)',
        //         ketuaSearchInput, ketuaSearchInput,
        //         ketuaErrorMsg
        //     );
        // }

        // --- 5. Nama Kegiatan ---
        const namaRKKegiatan = document.getElementById('nama_rk_kegiatan');
        const namaRKKegiatanErrorMsg = namaRKKegiatan?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        if (!namaRKKegiatan?.value?.trim()) {
            addError(
                'Nama Kegiatan wajib diisi',
                namaRKKegiatan, namaRKKegiatan,
                namaRKKegiatanErrorMsg
            );
        }

        return errors;
    }

    function showValidationKegiatanBanner(errors) {
        const banner = document.getElementById('validationBannerKegiatan');
        const list = document.getElementById('validationListKegiatan');
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

    function saveKegiatan(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // ---- Jalankan validasi ----
        const errors = validateFormKegiatan();
        if (errors.length > 0) {
            showValidationKegiatanBanner(errors);
            return; // Berhenti — tidak buka modal konfirmasi
        } else {
            confirmSaveKegiatan();
        }
    }

    function confirmSaveKegiatan() {
        const form = document.getElementById('addKegiatanForm');
        if (!form) {
            alert('Form tidak ditemukan');
            return;
        }
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('saveKegiatanButton')?.addEventListener('click', saveKegiatan);
        
        // Initialization successful

</script>
