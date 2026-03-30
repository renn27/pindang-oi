<x-ui.smart-modal id="modal-sub-kegiatan" class="max-w-2xl"
    @open-smart-modal.window="
            if ($event.detail.modalId !== 'modal-sub-kegiatan') return;

            mode = $event.detail.mode ?? 'create';
            itemKey = $event.detail.key ?? null;
            // Ambil data dari dispatch
            formData = $event.detail.data ?? {
                id_kegiatan: '',
                nama_rk_kegiatan: '',
                nama_sub_kegiatan: '',
                target: '',
                satuan_target: '',
                tanggal_mulai: '',
                tanggal_selesai: '',
            }">

    <form
            id="addSubKegiatanForm"
            :action="mode === 'edit'
            ?
            `/kegiatan/${formData.id_kegiatan}/sub-kegiatan/${itemKey}` :
            `/kegiatan/${formData.id_kegiatan}/sub-kegiatan`"
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
                    x-text="mode === 'create' ? 'Tambah Sub Kegiatan/RK Anggota' : 'Edit Sub Kegiatan/RK Anggota'"></h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                    x-text="mode === 'create' ? 'Masukkan sub kegiatan yang baru' : 'Edit sub kegiatan yang sudah ada'">
                </p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900">
                {{-- ====== VALIDATION BANNER ====== --}}
                <div id="validationBannerSubKegiatan"
                    class="hidden rounded-xl border border-red-300 dark:border-red-500/40 bg-red-50 dark:bg-red-500/10 px-4 py-3">
                    <p class="text-sm font-medium text-red-700 dark:text-red-400 mb-1">
                        ⚠ Ada beberapa field yang belum diisi atau tidak valid:
                    </p>
                    <ul id="validationListSubKegiatan" class="list-disc pl-5 space-y-1"></ul>
                </div>
                {{-- ====== END VALIDATION BANNER ====== --}}

                <!-- Nama Kegiatan (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Kegiatan
                    </label>

                    <input type="text" :value="formData.nama_rk_kegiatan" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Sub Kegiatan
                    </label>
                    <input type="text" name="nama_sub_kegiatan" id="nama_sub_kegiatan" x-model="formData.nama_sub_kegiatan"
                        placeholder="Contoh : Penyiapan Peta"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                    <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="nama_sub_kegiatan">Sub kegiatan wajib diisi</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Target Sub Kegiatan
                    </label>
                    <input type="number" x-model="formData.target" name="target" id="target"
                        placeholder="Misalnya : 200"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                    <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="target">Target wajib diisi</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Satuan Target
                    </label>
                    <input type="text" x-model="formData.satuan_target" name="satuan_target" id="satuan_target"
                        placeholder="Misalnya : Kegiatan"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                    <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="satuan_target">Satuan target wajib diisi</p>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tanggal Mulai
                    </label>
                    <x-form.date-picker id="tanggal_mulai" x-model="formData.tanggal_mulai" name="tanggal_mulai"
                        placeholder="Pilih Tanggal"/>
                    <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="tanggal_mulai">Tanggal mulai wajib dipilih</p>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tanggal Selesai
                    </label>
                    <x-form.date-picker id="tanggal_selesai" name="tanggal_selesai" x-model="formData.tanggal_selesai"
                        placeholder="Pilih Tanggal" />
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

                    <button id="saveSubKegiatanButton" type="button"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        Simpan Sub Kegiatan
                    </button>

                    {{-- <button type="submit"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto dark:bg-brand-600 dark:hover:bg-brand-700">
                        <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                    </button> --}}
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
        const banner = document.getElementById('validationBannerSubKegiatan');
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

    function validateFormSubKegiatan() {
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

        // --- 1. Nama Sub Kegiatan ---
        const namaRKSubKegiatan = document.getElementById('nama_sub_kegiatan');
        const namaRKSubKegiatanErrorMsg = namaRKSubKegiatan?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        if (!namaRKSubKegiatan?.value?.trim()) {
            addError(
                'Sub Kegiatan wajib diisi',
                namaRKSubKegiatan, namaRKSubKegiatan,
                namaRKSubKegiatanErrorMsg
            );
        }

        // --- 2. Target Sub Kegiatan ---
        const targetSubKegiatan = document.getElementById('target');
        const targetSubKegiatanErrorMsg = targetSubKegiatan?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        if (!targetSubKegiatan?.value?.trim()) {
            addError(
                'Target Sub Kegiatan wajib diisi',
                targetSubKegiatan, targetSubKegiatan,
                targetSubKegiatanErrorMsg
            );
        }

        // --- 3. Satuan Target Sub Kegiatan ---
        const satuanTargetSubKegiatan = document.getElementById('satuan_target');
        const satuanTargetSubKegiatanErrorMsg = satuanTargetSubKegiatan?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        if (!satuanTargetSubKegiatan?.value?.trim()) {
            addError(
                'Satuan Target Sub Kegiatan wajib diisi',
                satuanTargetSubKegiatan, satuanTargetSubKegiatan,
                satuanTargetSubKegiatanErrorMsg
            );
        }

        // --- 4. Tanggal Mulai Sub Kegiatan ---
        const tanggalMulaiSubKegiatan = document.getElementById('tanggal_mulai');
        const tanggalMulaiSubKegiatanErrorMsg = tanggalMulaiSubKegiatan?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        if (!tanggalMulaiSubKegiatan?.value?.trim()) {
            addError(
                'Tanggal Mulai Sub Kegiatan wajib dipilih',
                tanggalMulaiSubKegiatan, tanggalMulaiSubKegiatan,
                tanggalMulaiSubKegiatanErrorMsg
            );
        }

        // --- 5. Tanggal Selesai Sub Kegiatan ---
        const tanggalSelesaiSubKegiatan = document.getElementById('tanggal_selesai');
        const tanggalSelesaiSubKegiatanErrorMsg = tanggalSelesaiSubKegiatan?.closest('.md\\:w-3\\/4')?.querySelector('.field-error-msg');
        if (!tanggalSelesaiSubKegiatan?.value?.trim()) {
            addError(
                'Tanggal Selesai Sub Kegiatan wajib dipilih',
                tanggalSelesaiSubKegiatan, tanggalSelesaiSubKegiatan,
                tanggalSelesaiSubKegiatanErrorMsg
            );
        }

        return errors;
    }

    function showValidationSubKegiatanBanner(errors) {
        const banner = document.getElementById('validationBannerSubKegiatan');
        const list = document.getElementById('validationListSubKegiatan');
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

    function saveSubKegiatan(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        // ---- Jalankan validasi ----
        const errors = validateFormSubKegiatan();
        if (errors.length > 0) {
            showValidationSubKegiatanBanner(errors);
            return; // Berhenti — tidak buka modal konfirmasi
        } else {
            confirmSave();
        }
    }

    function confirmSave() {
        const form = document.getElementById('addSubKegiatanForm');
        if (!form) {
            alert('Form tidak ditemukan');
            return;
        }
        form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('saveSubKegiatanButton')?.addEventListener('click', saveSubKegiatan);
    });
</script>
