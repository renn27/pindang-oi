<!-- Modal Tambah Penerimaan -->
<x-ui.smart-modal id="modal-penerimaan-anggota" class="max-w-2xl" 
    x-data="{
        formData: {
            id_sub_kegiatan: '',
            id_penugasan: '',
            id_pengiriman: '',
            jumlah_pengiriman: 0,
            id_penerima: '',
            nama_penerima: '',
            tanggal_mulai: '',
            tanggal_penerimaan: '',
            jumlah_diterima: '',
            status: '',
            catatan: ''
        },
        mode: 'create',
        itemKey: null
    }"
    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-penerimaan-anggota') return;

        mode = $event.detail.mode ?? 'create';
        itemKey = $event.detail.key ?? null;
        
        // Update formData dengan data yang dikirim
        if ($event.detail.data) {
            Object.assign(formData, $event.detail.data);
        }
        
        console.log('formData setelah update:', formData);
    ">
    <form id="addPenerimaanForm"
        :action="`/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan/${formData.id_penugasan}/pengirimans/${formData.id_pengiriman}/penerimaan`"
        method="POST" class="grid grid-cols-1 gap-y-5">
        @csrf
        <div
            class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden
                rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">

            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white">
                    Lakukan Penerimaan
                </h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Terima dan Berikan Penilaian Kerja
                </p>
            </div>
            <div class="text-center m-4">
                <h6 class="text-sm font-semibold text-gray-600 dark:text-gray-300" x-text="formData.nama_anggota">
                </h6>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    ID Penugasan :
                    <span class="font-medium dark:text-gray-300" x-text="formData.id_penugasan"></span>
                </p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900">

                {{-- ====== VALIDATION BANNER ====== --}}
                <div id="validationBannerPenerimaan"
                    class="hidden rounded-xl border border-red-300 dark:border-red-500/40 bg-red-50 dark:bg-red-500/10 px-4 py-3 mb-4">
                    <p class="text-sm font-medium text-red-700 dark:text-red-400 mb-1">
                        ⚠ Ada beberapa field yang belum diisi atau tidak valid:
                    </p>
                    <ul id="validationListPenerimaan" class="list-disc pl-5 space-y-1"></ul>
                </div>
                {{-- ====== END VALIDATION BANNER ====== --}}

                <!-- Id Pengiriman (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Id Pengiriman
                    </label>

                    <input type="text" :value="formData.id_pengiriman" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">

                    {{-- Hidden input untuk mendapatkan value jumlah pengiriman untuk dibandingkan dengan jumlah diterima di validasi frontend --}}
                    <input type="hidden" id="jumlah_pengiriman" :value="formData.jumlah_pengiriman">
                </div>

                {{-- lock tanggal setelah 31 maret --}}
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tanggal Penerimaan <span class="text-red-500">*</span>
                    </label>

                    <div x-data="{
                        get isLocked() {
                            if (!formData.tanggal_mulai) return false;
                            return new Date(formData.tanggal_mulai) >= new Date('2026-04-01');
                        }
                    }">
                        <template x-if="isLocked">
                            <input type="text" name="tanggal_penerimaan" id="tanggal_penerimaan"
                                :value="'{{ now()->format('Y-m-d') }}'" readonly
                                class="h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm
                        text-gray-600 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" />
                        </template>

                        <template x-if="!isLocked">
                            <div>
                                <x-form.date-picker id="tanggal_penerimaan" name="tanggal_penerimaan"
                                    placeholder="Tanggal Penerimaan" defaultDate="{{ now()->format('Y-m-d') }}" />
                            </div>
                        </template>

                        <p x-show="isLocked" class="mt-1.5 text-xs text-amber-600 dark:text-amber-400">
                            ⚠ Tanggal penerimaan dikunci ke hari ini karena penugasan dimulai setelah 31 Maret 2026.
                        </p>
                    </div>
                </div>

                <!-- Jumlah Diterima dengan Info Pengiriman dan Warning -->
                <div x-data="{
                    get jumlahKirim() { return Number(formData.jumlah_pengiriman) || 0; },
                    jumlah: '',
                    get isBelowKirim() {
                        return this.jumlah && Number(this.jumlah) > 0 && Number(this.jumlah) < this.jumlahKirim;
                    },
                    get isExceedKirim() {
                        return this.jumlah && Number(this.jumlah) > 0 && Number(this.jumlah) > this.jumlahKirim;
                    },
                    get isEqualKirim() {
                        return this.jumlah && Number(this.jumlah) > 0 && Number(this.jumlah) === this.jumlahKirim;
                    },
                    get sisaKirim() {
                        return this.jumlahKirim - Number(this.jumlah);
                    }
                }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Jumlah Diterima <span class="text-red-500">*</span>
                        </label>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Dikirim: <span x-text="jumlahKirim" class="font-semibold text-gray-700 dark:text-gray-300"></span>
                        </span>
                    </div>

                    <input type="number" 
                        name="jumlah_diterima" 
                        id="jumlah_diterima"
                        x-model="jumlah"
                        placeholder="Masukkan jumlah penerimaan"
                        class="h-11 w-full appearance-none rounded-lg border text-sm shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500"
                        :class="{
                            'border-gray-300 bg-transparent px-4 py-2.5 text-gray-800 dark:border-gray-700': !isBelowKirim && !isExceedKirim,
                            'border-yellow-400 bg-yellow-50 px-4 py-2.5 text-gray-800 dark:border-yellow-600 dark:bg-yellow-900/20': isBelowKirim,
                            'border-red-400 bg-red-50 px-4 py-2.5 text-gray-800 dark:border-red-600 dark:bg-red-900/20': isExceedKirim,
                            'border-green-400 bg-green-50 px-4 py-2.5 text-gray-800 dark:border-green-600 dark:bg-green-900/20': isEqualKirim
                        }">

                    <!-- Warning jika di bawah jumlah dikirim -->
                    <div x-show="isBelowKirim" 
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-1"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="mt-2 flex items-start gap-2 rounded-lg border border-yellow-300 bg-yellow-50 px-3 py-2 dark:border-yellow-600/40 dark:bg-yellow-900/20">
                        <svg class="h-4 w-4 shrink-0 text-yellow-600 dark:text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300">
                            Jumlah yang diterima <span class="font-medium">kurang dari jumlah dikirim</span>. 
                            <span x-text="sisaKirim + ' lagi untuk menyamai jumlah dikirim'"></span>
                        </p>
                    </div>

                    <!-- Warning jika melebihi jumlah dikirim -->
                    <div x-show="isExceedKirim" 
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-1"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="mt-2 flex items-start gap-2 rounded-lg border border-red-300 bg-red-50 px-3 py-2 dark:border-red-600/40 dark:bg-red-900/20">
                        <svg class="h-4 w-4 shrink-0 text-red-600 dark:text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-red-700 dark:text-red-300">
                            Jumlah yang diterima <span class="font-medium">melebihi jumlah dikirim</span>. 
                            Maksimal <span x-text="jumlahKirim"></span>
                        </p>
                    </div>

                    <!-- Info jika sama dengan jumlah dikirim -->
                    <div x-show="isEqualKirim" 
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-1"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="mt-2 flex items-start gap-2 rounded-lg border border-green-300 bg-green-50 px-3 py-2 dark:border-green-600/40 dark:bg-green-900/20">
                        <svg class="h-4 w-4 shrink-0 text-green-600 dark:text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-green-700 dark:text-green-300">
                            Jumlah yang diterima <span class="font-medium">sesuai dengan jumlah dikirim</span>.
                        </p>
                    </div>
                </div>

                <!-- Status -->
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent mt-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" x-model="formData.status"
                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                        h-11 w-full mb-4 appearance-none rounded-lg
                        border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm
                        placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500"
                        :class="isOptionSelected && 'text-gray-800'" @change="isOptionSelected = true">
                        <!-- Placeholder -->
                        <option value="" disabled selected class="text-gray-400 dark:text-gray-500">
                            -- Pilih Status --
                        </option>

                        <option value="Diterima" class="text-gray-700 dark:text-gray-300">
                            Diterima
                        </option>

                        <option value="Revisi" class="text-gray-700 dark:text-gray-300">
                            Revisi
                        </option>
                    </select>

                    <span
                        class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2
                                            text-gray-700 dark:text-gray-400">
                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>

                <!-- Catatan -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Catatan (opsional)
                    </label>
                    <input type="text" name="catatan" id="catatan" x-model="formData.catatan"
                        placeholder="Masukkan catatan"
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

                    <button id="savePenerimaanButton" type="button"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        Terima Tugas Anggota Saya
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-ui.smart-modal>

<script>
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
        const banner = document.getElementById('validationBannerPenerimaan');
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

    function validateFormPenerimaan() {
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

        // --- 1. Tanggal Penerimaan ---
        const tanggalPenerimaan = document.getElementById('tanggal_penerimaan');
        const tanggalPenerimaanErrorMsg = tanggalPenerimaan?.closest('.md\\:w-3\\/4')?.querySelector(
        '.field-error-msg');
        if (!tanggalPenerimaan?.value?.trim()) {
            addError(
                'Tanggal Penerimaan wajib dipilih',
                tanggalPenerimaan, tanggalPenerimaan,
                tanggalPenerimaanErrorMsg
            );
        }

        // --- 2. Jumlah Diterima ---
        const jumlahDiterima = document.getElementById('jumlah_diterima');
        const jumlahDiterimaErrorMsg = jumlahDiterima?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');

        // ambil dari hidden input jumlah_pengiriman
        const jumlahPengiriman = Number(document.getElementById('jumlah_pengiriman')?.value || 0);

        if (!jumlahDiterima?.value?.trim()) {
            addError(
                'Jumlah Diterima wajib diisi (hanya angka)',
                jumlahDiterima, jumlahDiterima,
                jumlahDiterimaErrorMsg
            );
        } else if (isNaN(jumlahDiterima.value) || Number(jumlahDiterima.value) <= 0) {
            addError(
                'Jumlah Diterima harus berupa angka lebih besar dari 0',
                jumlahDiterima, jumlahDiterima,
                jumlahDiterimaErrorMsg
            );
        } else {
            const jumlah = Number(jumlahDiterima.value);

            if (jumlahPengiriman > 0 && jumlah > jumlahPengiriman) {
                addError(
                    `Jumlah Diterima tidak boleh melebihi jumlah dikirim sebanyak ${jumlahPengiriman}`,
                    jumlahDiterima,
                    jumlahDiterima,
                    jumlahDiterimaErrorMsg
                );
            }
        }

        // --- 3. Status Penerimaan ---
        const status = document.getElementById('status');
        const statusErrorMsg = status?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        if (!status?.value?.trim()) {
            addError(
                'Status penerimaan wajib dipilih',
                status, status,
                statusErrorMsg
            );
        }

        return errors;
    }

    function showValidationPenerimaanBanner(errors) {
        const banner = document.getElementById('validationBannerPenerimaan');
        const list = document.getElementById('validationListPenerimaan');
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

    function savePenerimaan(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // ---- Jalankan validasi ----
        const errors = validateFormPenerimaan();
        if (errors.length > 0) {
            showValidationPenerimaanBanner(errors);
            return; // Berhenti — tidak buka modal konfirmasi
        } else {
            confirmSavePenerimaan();
        }
    }

    function confirmSavePenerimaan() {
        const form = document.getElementById('addPenerimaanForm');
        if (!form) {
            alert('Form tidak ditemukan');
            return;
        }
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('savePenerimaanButton')?.addEventListener('click', savePenerimaan);
    });
</script>