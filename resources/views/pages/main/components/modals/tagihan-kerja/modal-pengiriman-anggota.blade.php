<!-- Modal Tambah Pengiriman -->
<x-ui.smart-modal id="modal-pengiriman-anggota" class="max-w-2xl"
    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-pengiriman-anggota') return;

        mode = $event.detail.mode ?? 'create';
        itemKey = $event.detail.key ?? null;
        // Ambil data dari dispatch
        formData = $event.detail.data ?? {
            id_sub_kegiatan: '',
            id_penugasan: '',
            nama_anggota: '',
            target_penugasan: '',
            tanggal_pengiriman: '',
            jumlah_dikirim: '',
            media_dikirim: '',
            bukti_dukung: ''
        }">
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
                    class="hidden rounded-xl border border-red-300 dark:border-red-500/40 bg-red-50 dark:bg-red-500/10 px-4 py-3">
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

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Jumlah Dikirim <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jumlah_dikirim" id="jumlah_dikirim"
                        placeholder="Masukkan jumlah pengiriman (hanya angka)"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Media Pengiriman <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="media_pengiriman" id="media_pengiriman"
                        placeholder="Masukkan jenis media pengiriman"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Bukti Dukung <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bukti_dukung" id="bukti_dukung"
                        placeholder="Masukkan link bukti dukung pengiriman"
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
