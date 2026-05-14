<!-- Modal Update Jenis Kegiatan -->
<x-ui.smart-modal id="modal-update-jenis-kegiatan" class="max-w-lg"
    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-update-jenis-kegiatan') return;
        
        formData = $event.detail.data ?? {
            id_penugasan: '',
            id_sub_kegiatan: '',
            id_jenis_kegiatan: '',
            jenis_kegiatan: ''
        };
    ">
    
    <form id="updateJenisKegiatanForm"
        :action="`/sub-kegiatan/${formData.id_sub_kegiatan}/penugasan/${formData.id_penugasan}/jenis-kegiatan`"
        method="POST" class="grid grid-cols-1 gap-y-5">
        @csrf
        @method('PUT')

        <div class="relative flex max-h-[90vh] w-full flex-col overflow-visible rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">

            <!-- HEADER -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h4 class="text-xl font-semibold text-gray-800 dark:text-white">Update Jenis Kegiatan</h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Pilih jenis kegiatan pengganti untuk penugasan ini.
                </p>
            </div>

            <!-- BODY -->
            <div class="flex-1 overflow-visible px-6 py-5 custom-scrollbar dark:bg-gray-900">
                
                <!-- Dropdown Alpine -->
                <div x-data="{
                    open: false,
                    isOther: false,
                    search: '',
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
                            style: 'text-blue-700 font-medium dark:text-blue-300'
                        }
                    ],
                    get filteredOptions() {
                        if (!this.search) return this.options;
                        return this.options.filter(o => o.text.toLowerCase().includes(this.search.toLowerCase()));
                    },
                    get selectText() {
                        if (!formData.id_jenis_kegiatan) return '-- Pilih Jenis Kegiatan --';
                        let opt = this.options.find(o => o.id == formData.id_jenis_kegiatan);
                        return opt ? opt.text : '-- Pilih Jenis Kegiatan --';
                    },
                    selectJenis(opt) {
                        formData.id_jenis_kegiatan = opt.id;
                        this.isOther = (opt.id === 'LAINNYA');
                        this.open = false;
                        this.search = '';
                    }
                }"
                @open-smart-modal.window="
                    if ($event.detail.modalId !== 'modal-update-jenis-kegiatan') return;
                    isOther = (formData.id_jenis_kegiatan === 'LAINNYA');
                ">
                
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Jenis Kegiatan <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" name="id_jenis_kegiatan" x-model="formData.id_jenis_kegiatan" required>

                    <div class="relative mb-4">
                        <button type="button" @click="open = !open; if(open) search = '';" @click.outside="open = false"
                            class="flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800">
                            
                            <span x-text="selectText" class="truncate" :class="!formData.id_jenis_kegiatan ? 'text-gray-400' : 'text-gray-800 dark:text-gray-200'"></span>
                            
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition class="absolute z-50 mt-1 w-full rounded-lg border bg-white shadow-lg dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                            <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                <input type="text" x-model="search" placeholder="Cari jenis kegiatan..." class="w-full px-3 py-2 text-sm rounded-md border focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                            </div>

                            <div class="max-h-48 overflow-y-auto">
                                <template x-for="opt in filteredOptions" :key="opt.id">
                                    <button type="button" @click="selectJenis(opt)"
                                        class="w-full px-4 py-3 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0"
                                        :class="opt.style">
                                        <span x-text="opt.text"></span>
                                    </button>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                    Tidak ditemukan
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="isOther" x-transition>
                        <input type="text" name="jenis_kegiatan_baru" placeholder="Masukkan jenis kegiatan baru" class="h-11 w-full mb-4 rounded-lg border border-gray-300 dark:border-gray-700 px-4 text-sm dark:bg-gray-800 dark:text-gray-300 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10">
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="shrink-0 border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button @click="open = false" type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex w-full justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 sm:w-auto transition shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-ui.smart-modal>
