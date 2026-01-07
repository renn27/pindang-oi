<!-- Modal Tambah Penerimaan -->
<x-ui.smart-modal id="modal-penerimaan-anggota" class="max-w-2xl"
    @open-smart-modal.window="
                        if ($event.detail.modalId !== 'modal-penerimaan-anggota') return;

                        mode = $event.detail.mode ?? 'create';
                        itemKey = $event.detail.key ?? null;
                        // Ambil data dari dispatch
                        formData = $event.detail.data ?? {
                            id_sub_kegiatan: '',
                            id_penugasan: '',
                            id_pengiriman: '',
                            id_penerima: '',
                            nama_penerima: '',
                            tanggal_penerimaan: '',
                            jumlah_diterima: '',
                            status: '',,
                            catatan: ''
                        }">
    <form
        :action="`/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan/${formData.id_penugasan}/pengirimans/${formData.id_pengiriman}/penerimaan`"
        method="POST" class="grid grid-cols-1 gap-y-5">
        @csrf
        <div
            class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden
               rounded-3xl bg-white dark:bg-gray-900">

            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90">
                    Lakukan Penerimaan
                </h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Terima dan Berikan Penilaian Kerja
                </p>
            </div>
            <div class="text-center m-4">
                <h6 class="text-sm font-semibold text-gray-600 dark:text-white/90" x-text="formData.nama_anggota">
                </h6>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    ID Penugasan:
                    <span class="font-medium" x-text="formData.id_penugasan"></span>
                </p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">

                <!-- Id Pengiriman (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Id Pengiriman
                    </label>

                    <input type="text" :value="formData.id_pengiriman" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                            dark:border-gray-700 dark:bg-gray-800 dark:text-white/70 cursor-not-allowed">
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Tanggal Penerimaan
                    </label>
                    <x-form.date-picker id="tanggal_penerimaan" name="tanggal_penerimaan"
                        x-model="formData.tanggal_penerimaan" placeholder="Date Picker"
                        defaultDate="{{ now()->format('Y-m-d') }}" readonly="true" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Jumlah Diterima
                    </label>
                    <input type="text" name="jumlah_diterima" x-model="formData.jumlah_diterima"
                        placeholder="Masukkan jumlah penerimaan"
                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>
                <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Status
                    </label>
                    <select name="status" x-model="formData.status"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                                            dark:focus:border-brand-800 h-11 w-full mb-4 appearance-none rounded-lg
                                            border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm
                                            placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                                            dark:placeholder:text-white/30"
                        :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                        @change="isOptionSelected = true">
                        <!-- Placeholder -->
                        <option value="" disabled selected class="text-gray-400 dark:bg-gray-900">
                            -- Pilih Status --
                        </option>

                        <option value="Diterima" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                            Diterima
                        </option>

                        <option value="Revisi" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
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

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Catatan
                    </label>
                    <input type="text" name="catatan" placeholder="Masukkan catatan"
                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>

            </div>
            <!-- FOOTER -->
            <!-- FOOTER -->
            <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button @click="open = false" type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                        Batal
                    </button>

                    <button type="submit"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                    </button>
                </div>
            </div>
        </div>
    </form>

</x-ui.smart-modal>
