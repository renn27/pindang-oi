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
            tanggal_mulai: '',
            tanggal_selesai: '',
            min_date: '',
            max_date: '',
        }">
    <form
        id="addPenugasanForm"
        :action="mode === 'edit'
            ?
            `/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan/${itemKey}` :
            `/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan`"
        method="POST" class="grid grid-cols-1 gap-y-5">
        @csrf
        <template x-if="mode === 'edit'">
            @method('PUT')
        </template>

        <div class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden
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
                    " class="mb-4">

                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Pegawai <span class="text-red-500">*</span>
                    </label>

                    <!-- Hidden ID Pegawai (WAJIB buat submit) -->
                    <input type="hidden" name="id_anggota" x-model="selectedId">

                    <!-- Input Visible -->
                    <div class="relative">
                        <input
                            type="text"
                            x-model="search"
                            @click="mode === 'create' && (open = true)"
                            @input="mode === 'create' && (open = true)"
                            @keydown.escape="open = false"
                            :readonly="mode === 'edit'"
                            placeholder="Pilih pegawai..."
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
                                    Nama Pegawai tidak ditemukan
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
                            {{-- <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="pegawaiSearchInput">Pegawai wajib dipilih dari daftar (pastikan klik nama dari dropdown)</p> --}}
                        </div>
                    </div>
                </div>

                <div x-data="{ isOther: false }">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Jenis Kegiatan <span class="text-red-500">*</span>
                    </label>

                    <!-- SELECT UI -->
                    <select
                        id="jenis_kegiatan_select"
                        name="id_jenis_kegiatan"
                        x-model="formData.id_jenis_kegiatan"
                        @change="isOther = ($event.target.value === 'LAINNYA')"
                        required
                        class="
                            h-11 w-full mb-4
                            rounded-lg border border-gray-300
                            bg-white
                            px-4 py-2.5 text-sm
                            focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <option value="" class="dark:text-gray-400">-- Pilih Jenis Kegiatan --</option>

                        @foreach ($jenisKegiatans as $jenis)
                            <option
                                value="{{ $jenis->id }}"
                                data-text="{{ $jenis->jenis_kegiatan }}"
                                class="
                                    @if($jenis->kategori === 'Utama')
                                        text-green-700 font-medium dark:text-green-300
                                    @elseif($jenis->kategori === 'Tambahan')
                                        text-orange-700 dark:text-orange-300
                                    @endif">
                                {{ $jenis->jenis_kegiatan }} ({{ $jenis->kategori }})
                            </option>
                        @endforeach

                        <option value="LAINNYA" class="text-blue-700 font-medium dark:text-blue-300">
                            ➕ Lainnya
                        </option>
                    </select>
                    <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="jenis_kegiatan_select">Jenis Kegiatan wajib dipilih dari daftar</p>

                    <!-- INPUT JENIS KEGIATAN BARU -->
                    <div x-show="isOther" x-transition>
                        <input
                            id="jenis_kegiatan_baru"
                            type="text"
                            name="jenis_kegiatan_baru"
                            placeholder="Masukkan jenis kegiatan baru"
                            class="h-11 w-full mb-4 rounded-lg border border-gray-300 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500"
                        />
                    </div>
                    {{-- <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="jenis_kegiatan_baru">Jenis Kegiatan baru wajib diisi jika memilih opsi ini</p> --}}
                </div>

                <div
                    x-data="{
                        butuhDl: false,
                        isLocked: true,

                        wajibJenis: [3,4,5,6],

                        get jenisId() {
                            return Number(formData?.id_jenis_kegiatan || 0)
                        },

                        get isLainnya() {
                            return formData?.id_jenis_kegiatan === 'LAINNYA'
                        },

                        get jenisSelected() {
                            return this.jenisId > 0 || this.isLainnya
                        },

                        syncState() {
                            const isWajib = this.wajibJenis.includes(this.jenisId)
                            const fromDB = Boolean(Number(formData?.butuh_dl ?? 0))

                            // ================= CREATE =================
                            if (mode === 'create') {

                                if (this.isLainnya) {
                                    this.butuhDl = false
                                    this.isLocked = false
                                    return
                                }

                                if (!this.jenisSelected) {
                                    this.butuhDl = false
                                    this.isLocked = true
                                } else if (isWajib) {
                                    this.butuhDl = true
                                    this.isLocked = true
                                } else {
                                    this.butuhDl = false
                                    this.isLocked = false
                                }

                                return
                            }

                            // ================= EDIT =================
                            if (this.isLainnya) {
                                this.butuhDl = fromDB
                                this.isLocked = false
                                return
                            }

                            if (!this.jenisSelected) {
                                this.butuhDl = false
                                this.isLocked = true
                                return
                            }

                            if (isWajib) {
                                // 🔒 Wajib DL → selalu ON & locked
                                this.butuhDl = true
                                this.isLocked = true
                            } else {
                                // ✅ OPSIONAL → ambil dari DB
                                this.butuhDl = fromDB
                                this.isLocked = false
                            }
                        }
                    }"
                    x-effect="syncState()"
                    class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Kebutuhan Dinas Luar (DL) 
                    </label>

                    <div class="flex items-center gap-4">
                        <!-- Toggle UI -->
                        <button
                            type="button"
                            @click="if (!isLocked) butuhDl = !butuhDl"
                            :class="{
                                'bg-brand-500 dark:bg-brand-600': butuhDl,
                                'bg-gray-300 dark:bg-gray-600': !butuhDl,
                                'cursor-not-allowed opacity-70': isLocked
                            }"
                            class="relative inline-flex h-7 w-14 items-center rounded-full"
                        >
                            <span
                                :class="butuhDl ? 'translate-x-7' : 'translate-x-1'"
                                class="inline-block h-5 w-5 bg-white dark:bg-gray-300 rounded-full transition"
                            ></span>
                        </button>

                        <span
                            class="text-sm font-medium"
                            :class="butuhDl ? 'text-brand-600 dark:text-brand-400' : 'text-gray-500 dark:text-gray-400'"
                            x-text="
                                !jenisSelected
                                    ? 'Pilih dulu jenis kegiatan'
                                    : (butuhDl ? 'Butuh DL' : 'Tidak Butuh DL')
                            "
                        ></span>
                    </div>

                    <!-- Helper text -->
                    <p x-show="!jenisSelected" class="mt-1 text-xs font-medium text-brand-500/80 dark:text-brand-400">
                        Pilih jenis kegiatan untuk menentukan kebutuhan DL.
                    </p>

                    <p x-show="isLocked && jenisSelected" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Jenis kegiatan ini otomatis membutuhkan DL dan tidak dapat diubah.
                    </p>

                    <!-- Hidden input -->
                    <input type="hidden" name="butuh_dl" :value="butuhDl ? 1 : 0">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Target <span class="text-red-500">*</span>
                    </label>
                    <input type="number" x-model="formData.target" name="target" id="target"
                        placeholder="Misalnya : 200"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                    <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="target">Target wajib diisi</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Satuan Target <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="formData.satuan_target" name="satuan_target" id="satuan_target"
                        placeholder="Misalnya : Dokumen, Kegiatan, dll"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                    <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="satuan_target">Satuan Target wajib diisi</p>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <x-form.date-picker
                        x-model="formData.tanggal_mulai"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        placeholder="Tanggal Mulai"
                        ::minDate="formData.min_date"
                        ::maxDate="formData.max_date"
                        minBind="formData.min_date"
                        maxBind="formData.max_date"
                    />
                    <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="tanggal_mulai">Tanggal mulai wajib dipilih</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tanggal Berakhir (Deadline) <span class="text-red-500">*</span>
                    </label>
                    <x-form.date-picker
                        x-model="formData.tanggal_selesai"
                        id="tanggal_selesai"
                        name="tanggal_selesai"
                        placeholder="Tanggal Selesai"
                        ::minDate="formData.min_date"
                        ::maxDate="formData.max_date"
                        minBind="formData.min_date"
                        maxBind="formData.max_date"
                    />
                    <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="tanggal_selesai">Tanggal selesai wajib dipilih</p>
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
                $pegawais->map(fn($p) => [
                    'id_pegawai'   => $p->id_pegawai,
                    'nama_pegawai' => $p->nama_pegawai
                ])
            ),

            initFromModal(detail) {
                this.mode = detail.mode ?? 'create';

                if (this.mode === 'edit') {
                    this.selectedId = detail.data.id_anggota;
                    this.search     = detail.data.nama_anggota;
                    this.open       = false;
                } else {
                    this.selectedId = '';
                    this.search     = '';
                    this.open       = true;
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
        const jenisKegiatanSelectErrorMsg = jenisKegiatanSelect?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
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
        const satuanTargetPenugasanErrorMsg = satuanTargetPenugasan?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        if (!satuanTargetPenugasan?.value?.trim()) {
            addError(
                'Satuan Target Penugasan wajib diisi',
                satuanTargetPenugasan, satuanTargetPenugasan,
                satuanTargetPenugasanErrorMsg
            );
        }

        // --- 6. Tanggal Mulai Sub Kegiatan ---
        const tanggalMulaiPenugasan = document.getElementById('tanggal_mulai');
        const tanggalMulaiPenugasanErrorMsg = tanggalMulaiPenugasan?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        if (!tanggalMulaiPenugasan?.value?.trim()) {
            addError(
                'Tanggal Mulai Penugasan wajib dipilih',
                tanggalMulaiPenugasan, tanggalMulaiPenugasan,
                tanggalMulaiPenugasanErrorMsg
            );
        }

        // --- 7. Tanggal Selesai Sub Kegiatan ---
        const tanggalSelesaiPenugasan = document.getElementById('tanggal_selesai');
        const tanggalSelesaiPenugasanErrorMsg = tanggalSelesaiPenugasan?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        if (!tanggalSelesaiPenugasan?.value?.trim()) {
            addError(
                'Tanggal Selesai Penugasan wajib dipilih',
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
</script>

