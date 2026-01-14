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
                tanggal_mulai: '',
                tanggal_selesai: '',
            }">

    <form :action="mode === 'edit'
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
                rounded-3xl bg-white">

            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3">
                <h4 class="text-2xl font-semibold text-gray-800"
                    x-text="mode === 'create' ? 'Tambah Sub Kegiatan/RK Anggota' : 'Edit Sub Kegiatan/RK Anggota'"></h4>
                <p class="mt-1 text-sm text-gray-500"
                    x-text="mode === 'create' ? 'Masukkan sub kegiatan yang baru' : 'Edit sub kegiatan yang sudah ada'">
                </p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">

                <!-- Nama Kegiatan (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Kegiatan
                    </label>

                    <input type="text" :value="formData.nama_rk_kegiatan" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                cursor-not-allowed">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Nama Sub Kegiatan
                    </label>
                    <input type="text" name="nama_sub_kegiatan" x-model="formData.nama_sub_kegiatan"
                        placeholder="Contoh : Penyiapan Peta"
                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Target Kegiatan
                    </label>
                    <input type="number" x-model="formData.target" name="target"
                        placeholder="Misalnya : 200"
                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Tanggal Mulai
                    </label>
                    <x-form.date-picker id="tanggal_mulai" x-model="formData.tanggal_mulai" name="tanggal_mulai"
                        placeholder="Pilih Tanggal"/>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Tanggal Selesai
                    </label>
                    <x-form.date-picker id="tanggal_selesai" name="tanggal_selesai" x-model="formData.tanggal_selesai"
                        placeholder="Pilih Tanggal" defaultDate="{{ now()->format('Y-m-d') }}" />
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
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-ui.smart-modal>
