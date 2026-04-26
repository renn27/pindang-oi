<!-- Modal Tambah Pengiriman -->
<x-ui.smart-modal id="modal-pengiriman-anggota" class="max-w-2xl">
    <div x-data="{
        bulanPengiriman: '',
        tipePengiriman: 'Pelunasan',
        get bulanOptions() {
            if (!formData.tanggal_mulai || !formData.tanggal_selesai) return [];
            const start = new Date(formData.tanggal_mulai);
            const end = new Date(formData.tanggal_selesai);
            const options = [];
            const bulanNama = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            const bulanDiterima = formData.bulanDiterima || [];
            const now = new Date();
            const currentYM = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
            let current = new Date(start.getFullYear(), start.getMonth(), 1);
            while (current <= end) {
                const y = current.getFullYear();
                const m = String(current.getMonth() + 1).padStart(2, '0');
                const val = y + '-' + m;
                const label = bulanNama[current.getMonth()] + ' ' + y;
                let disabled = false;
                let reason = '';
                if (bulanDiterima.includes(val)) {
                    disabled = true;
                    reason = 'Bulan ini sudah memiliki pengiriman yang Diterima';
                } else if (val > currentYM) {
                    disabled = true;
                    reason = 'Belum bisa mengirim tugas untuk bulan yang belum tiba';
                }
                options.push({ value: val, label, disabled, reason });
                current.setMonth(current.getMonth() + 1);
            }
            return options;
        },
        get activeOptions() {
            return this.bulanOptions.filter(o => !o.disabled);
        },
        get isLastMonth() {
            const active = this.activeOptions;
            if (!this.bulanPengiriman || active.length === 0) return false;
            // Cek apakah bulan ini adalah bulan terakhir dari rentang penugasan (bukan dari active)
            const allOptions = this.bulanOptions;
            return this.bulanPengiriman === allOptions[allOptions.length - 1].value;
        },
        get bolehCicilan() {
            return this.bulanOptions.length > 1 && !this.isLastMonth;
        }
    }"
    x-effect="
        // Auto-select bulan if only 1 active option
        if (activeOptions.length === 1 && !bulanPengiriman) {
            bulanPengiriman = activeOptions[0].value;
        }
        // Force Pelunasan when Cicilan not allowed
        if (!bolehCicilan && tipePengiriman === 'Cicilan') {
            tipePengiriman = 'Pelunasan';
        }
        // Reset when modal re-opens with new data
        if (bulanOptions.length === 0) {
            bulanPengiriman = '';
            tipePengiriman = 'Pelunasan';
        }
    ">
    <form id="addPengirimanForm"
        :action="`/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan/${formData.id_penugasan}/pengirimans`"
        method="POST" class="grid grid-cols-1 gap-y-5">
        @csrf
        <div
            class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden
                rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">

            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white">
                    Buat Pengiriman
                </h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kirimkan hasil kerja disini
                </p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900">
                {{-- ====== VALIDATION BANNER ====== --}}
                <div id="validationBannerPengiriman"
                    class="hidden rounded-xl border border-red-300 dark:border-red-500/40 bg-red-50 dark:bg-red-500/10 px-4 py-3 mb-4">
                    <p class="text-sm font-medium text-red-700 dark:text-red-400 mb-1">
                        ⚠ Ada beberapa field yang belum diisi atau tidak valid:
                    </p>
                    <ul id="validationListPengiriman" class="list-disc pl-5 space-y-1"></ul>
                </div>
                {{-- ====== END VALIDATION BANNER ====== --}}

                <!-- Id Penugasan (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Id Penugasan :
                    </label>

                    <input type="text" :value="formData.id_penugasan" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                        cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">

                    {{-- Hidden input untuk mendapatkan value target penugasan untuk dibandingkan dengan jumlah dikirim di validasi frontend --}}
                    <input type="hidden" id="target_penugasan" :value="formData.target_penugasan">
                    <input type="hidden" id="satuan_target" :value="formData.satuan_target">
                </div>

                <!-- Nama Anggota (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Anggota
                    </label>

                    <input type="text" :value="formData.nama_anggota" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                        cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                </div>

                <!-- Bulan Pengiriman -->
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Bulan Pengiriman <span class="text-red-500">*</span>
                    </label>
                    <select name="bulan_pengiriman" id="bulan_pengiriman" x-model="bulanPengiriman"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10">
                        <option value="">-- Pilih Bulan --</option>
                        <template x-for="opt in bulanOptions" :key="opt.value">
                            <option :value="opt.value" 
                                    x-text="opt.disabled ? opt.label + ' — ' + opt.reason : opt.label"
                                    :disabled="opt.disabled"
                                    :class="opt.disabled ? 'text-gray-400 bg-gray-100 dark:text-gray-600 dark:bg-gray-800' : ''"></option>
                        </template>
                    </select>
                </div>

                <!-- Tipe Pengiriman -->
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tipe Pengiriman <span class="text-red-500">*</span>
                    </label>
                    <select name="tipe_pengiriman" id="tipe_pengiriman" x-model="tipePengiriman"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10">
                        <option value="">-- Pilih Tipe Pengiriman --</option>
                        <option value="Pelunasan">Pelunasan (Pengiriman Terakhir)</option>
                        <template x-if="bolehCicilan">
                            <option value="Cicilan">Cicilan (Masih Ada Lanjutan)</option>
                        </template>
                    </select>
                    <template x-if="bulanOptions.length <= 1">
                        <p class="mt-1.5 text-xs text-blue-500 dark:text-blue-400">
                            Penugasan ini hanya 1 bulan, otomatis Pelunasan.
                        </p>
                    </template>
                    <template x-if="bulanOptions.length > 1 && isLastMonth">
                        <p class="mt-1.5 text-xs text-amber-500 dark:text-amber-400">
                            Bulan terakhir dalam rentang penugasan — hanya bisa Pelunasan.
                        </p>
                    </template>
                    <template x-if="bolehCicilan">
                        <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">
                            Pilih <strong>Cicilan</strong> jika masih akan mengirim lagi, atau <strong>Pelunasan</strong> jika ini pengiriman terakhir.
                        </p>
                    </template>
                </div>

                {{-- lock tanggal setelah 31 maret --}}
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tanggal Pengiriman <span class="text-red-500">*</span>
                    </label>

                    <!-- Wrapper dengan Alpine untuk handle lock -->
                    <div x-data="{
                        get isLocked() {
                            if (!formData.tanggal_mulai) return false;
                            return new Date(formData.tanggal_mulai) >= new Date('2026-04-01');
                        }
                    }">
                        <!-- Input LOCKED: tanggal hari ini, tidak bisa diubah -->
                        <template x-if="isLocked">
                            <input type="text" name="tanggal_pengiriman" id="tanggal_pengiriman"
                                :value="'{{ now()->format('Y-m-d') }}'" readonly
                                class="h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm
                        text-gray-600 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" />
                        </template>

                        <!-- Input BEBAS: pakai date-picker biasa -->
                        <template x-if="!isLocked">
                            <div>
                                <x-form.date-picker id="tanggal_pengiriman" name="tanggal_pengiriman"
                                    placeholder="Tanggal Pengiriman" defaultDate="{{ now()->format('Y-m-d') }}" />
                            </div>
                        </template>

                        <!-- Badge info kalau locked -->
                        <p x-show="isLocked" class="mt-1.5 text-xs text-amber-600 dark:text-amber-400">
                            ⚠ Tanggal pengiriman dikunci ke hari ini karena penugasan dimulai setelah 31 Maret 2026.
                        </p>
                    </div>
                </div>

                <!-- Jumlah Dikirim dengan Info Target dan Warning -->
                <div x-data="{
                    get target() { return Number(formData.target_penugasan) || 0; },
                    get satuan() { return formData.satuan_target || ''; },
                    jumlah: '',
                    get isBelowTarget() {
                        return this.jumlah && Number(this.jumlah) > 0 && Number(this.jumlah) < this.target;
                    },
                    get isExceedTarget() {
                        return this.jumlah && Number(this.jumlah) > 0 && Number(this.jumlah) > this.target;
                    },
                    get sisaTarget() {
                        return this.target - Number(this.jumlah);
                    }
                }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Jumlah Dikirim <span class="text-red-500">*</span>
                        </label>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Target: <span x-text="target" class="font-semibold text-gray-700 dark:text-gray-300"></span>
                            <span x-show="satuan" x-text="satuan" class="ml-0.5"></span>
                        </span>
                    </div>

                    <input type="number" 
                        name="jumlah_dikirim" 
                        id="jumlah_dikirim"
                        x-model="jumlah"
                        placeholder="Masukkan jumlah pengiriman (hanya angka)"
                        class="h-11 w-full appearance-none rounded-lg border text-sm shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500"
                        :class="{
                            'border-gray-300 bg-transparent px-4 py-2.5 text-gray-800 dark:border-gray-700': !isBelowTarget && !isExceedTarget,
                            'border-yellow-400 bg-yellow-50 px-4 py-2.5 text-gray-800 dark:border-yellow-600 dark:bg-yellow-900/20': isBelowTarget,
                            'border-red-400 bg-red-50 px-4 py-2.5 text-gray-800 dark:border-red-600 dark:bg-red-900/20': isExceedTarget
                        }">

                    <!-- Warning jika di bawah target -->
                    <div x-show="isBelowTarget" 
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-1"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="mt-2 flex items-start gap-2 rounded-lg border border-yellow-300 bg-yellow-50 px-3 py-2 dark:border-yellow-600/40 dark:bg-yellow-900/20">
                        <svg class="h-4 w-4 shrink-0 text-yellow-600 dark:text-yellow-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300">
                            Jumlah yang dikirim <span class="font-medium">kurang dari target</span> penugasan. 
                            <span x-text="sisaTarget + ' ' + satuan + ' lagi untuk mencapai target'"></span>
                        </p>
                    </div>

                    <!-- Warning jika melebihi target -->
                    <div x-show="isExceedTarget" 
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-1"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="mt-2 flex items-start gap-2 rounded-lg border border-red-300 bg-red-50 px-3 py-2 dark:border-red-600/40 dark:bg-red-900/20">
                        <svg class="h-4 w-4 shrink-0 text-red-600 dark:text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-red-700 dark:text-red-300">
                            Jumlah yang dikirim <span class="font-medium">melebihi target</span> penugasan. 
                            Maksimal <span x-text="target + ' ' + satuan"></span>
                        </p>
                    </div>
                </div>

                <!-- Media Pengiriman -->
                <div class="mt-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Media Pengiriman <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="media_pengiriman" id="media_pengiriman"
                        placeholder="Masukkan jenis media pengiriman"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                <!-- Bukti Dukung -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Bukti Dukung <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bukti_dukung" id="bukti_dukung"
                        placeholder="Masukkan link bukti dukung pengiriman"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
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

                    <button id="savePengirimanButton" type="button"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        Kirim Tugas Saya
                    </button>
                </div>
            </div>
        </div>
    </form>
    </div> {{-- close inner x-data wrapper --}}

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
        const banner = document.getElementById('validationBannerPengiriman');
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

    function validateFormPengiriman() {
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

        // --- 1. Tanggal Pengiriman ---
        const tanggalPengiriman = document.getElementById('tanggal_pengiriman');
        const tanggalPengirimanErrorMsg = tanggalPengiriman?.closest('.md\\:w-3\\/4')?.querySelector(
        '.field-error-msg');
        if (!tanggalPengiriman?.value?.trim()) {
            addError(
                'Tanggal Pengiriman wajib dipilih',
                tanggalPengiriman, tanggalPengiriman,
                tanggalPengirimanErrorMsg
            );
        }

        // --- 2. Jumlah Dikirim ---
        const jumlahDikirim = document.getElementById('jumlah_dikirim');
        const jumlahDikirimErrorMsg = jumlahDikirim?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');

        // ambil dari hidden input target_penugasan
        const targetPenugasan = Number(document.getElementById('target_penugasan')?.value || 0);

        if (!jumlahDikirim?.value?.trim()) {
            addError(
                'Jumlah Dikirim wajib diisi (hanya angka)',
                jumlahDikirim, jumlahDikirim,
                jumlahDikirimErrorMsg
            );
        } else if (isNaN(jumlahDikirim.value) || Number(jumlahDikirim.value) <= 0) {
            addError(
                'Jumlah Dikirim harus berupa angka lebih besar dari 0',
                jumlahDikirim, jumlahDikirim,
                jumlahDikirimErrorMsg
            );
        } else {
            const jumlah = Number(jumlahDikirim.value);

            if (targetPenugasan > 0 && jumlah > targetPenugasan) {
                addError(
                    `Jumlah Dikirim tidak boleh melebihi target penugasan sebanyak ${targetPenugasan}`,
                    jumlahDikirim,
                    jumlahDikirim,
                    jumlahDikirimErrorMsg
                );
            }
        }

        // --- 3. Media Pengiriman ---
        const mediaPengiriman = document.getElementById('media_pengiriman');
        const mediaPengirimanErrorMsg = mediaPengiriman?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        if (!mediaPengiriman?.value?.trim()) {
            addError(
                'Media Pengiriman wajib diisi',
                mediaPengiriman, mediaPengiriman,
                mediaPengirimanErrorMsg
            );
        }

        // --- 4. Bukti Dukung ---
        const buktiDukung = document.getElementById('bukti_dukung');
        const buktiDukungValue = buktiDukung?.value?.trim();
        const buktiDukungErrorMsg = buktiDukung?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');

        // Regex untuk validasi URL
        const urlPattern = /^(https?:\/\/)?([\w\d-]+\.)+[\w-]+(\/[\w\d-._~:/?#[\]@!$&'()*+,;=]*)?$/i;

        if (!buktiDukungValue) {
            // Validasi jika kosong
            addError(
                'Bukti Dukung wajib disertakan',
                buktiDukung, buktiDukung,
                buktiDukungErrorMsg
            );
        } else if (!urlPattern.test(buktiDukungValue)) {
            // Validasi jika format bukan URL
            addError(
                'Bukti Dukung harus berupa URL yang valid (contoh: https://drive.google.com/drive/)',
                buktiDukung, buktiDukung,
                buktiDukungErrorMsg
            );
        }

        return errors;
    }

    function showValidationPengirimanBanner(errors) {
        const banner = document.getElementById('validationBannerPengiriman');
        const list = document.getElementById('validationListPengiriman');
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

    function savePengiriman(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // ---- Jalankan validasi ----
        const errors = validateFormPengiriman();
        if (errors.length > 0) {
            showValidationPengirimanBanner(errors);
            return; // Berhenti — tidak buka modal konfirmasi
        } else {
            confirmSavePengiriman();
        }
    }

    function confirmSavePengiriman() {
        const form = document.getElementById('addPengirimanForm');
        if (!form) {
            alert('Form tidak ditemukan');
            return;
        }
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('savePengirimanButton')?.addEventListener('click', savePengiriman);
    });
</script>