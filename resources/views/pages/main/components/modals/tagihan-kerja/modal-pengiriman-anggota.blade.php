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
                        nama_anggota: ''
                        tanggal_pengiriman: '',
                        jumlah_dikirim: '',
                        media_dikirim: '',
                        bukti_dukung: ''
                    }">
    <form :action="`/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan/${formData.id_penugasan}/pengirimans`"
        method="POST" class="grid grid-cols-1 gap-y-5">
        @csrf
        <div
            class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden
               rounded-3xl bg-white">

            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3">
                <h4 class="text-2xl font-semibold text-gray-800">
                    Buat Pengiriman
                </h4>
                <p class="mt-1 text-sm text-gray-500">
                    Kirimkan hasil kerja disini
                </p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">

                <!-- Id Penugasan (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Id Penugasan
                    </label>

                    <input type="text" :value="formData.id_penugasan" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                        cursor-not-allowed">
                </div>

                <!-- Nama Anggota (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Nama Anggota
                    </label>

                    <input type="text" :value="formData.nama_anggota" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                        cursor-not-allowed">
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Tanggal Pengiriman
                    </label>
                    <x-form.date-picker id="tanggal_pengiriman" name="tanggal_pengiriman" placeholder="Date Picker"
                        defaultDate="{{ now()->format('Y-m-d') }}" readonly="true" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Jumlah Dikirim
                    </label>
                    <input type="text" name="jumlah_dikirim" placeholder="Masukkan jumlah pengiriman"
                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Media Pengiriman
                    </label>
                    <input type="text" name="media_pengiriman" placeholder="Masukkan jenis media pengiriman"
                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Bukti Dukung
                    </label>
                    <input type="text" name="bukti_dukung" placeholder="Masukkan link bukti dukung pengiriman"
                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                </div>

            </div>
            <!-- FOOTER -->
            <div class="shrink-0 border-t border-gray-200 px-6 py-3">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button @click="open = false" type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto">
                        Batal
                    </button>

                    <button type="submit"
                        class="flex w-fulljustify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                    </button>
                </div>
            </div>
        </div>
    </form>

</x-ui.smart-modal>
