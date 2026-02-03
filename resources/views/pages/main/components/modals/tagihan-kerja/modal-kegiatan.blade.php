<x-ui.smart-modal id="modal-kegiatan" class="max-w-2xl"
    @open-smart-modal.window="
            if ($event.detail.modalId !== 'modal-kegiatan') return;

            mode    = $event.detail.mode ?? 'create';
            itemKey = $event.detail.key ?? null;

            const payload = $event.detail.data ?? {
                id_bidang: {{ $bidang->id_bidang }},
                nama_bidang: {{ $bidang->nama_bidang }},
                id_penanggung_jawab: '',
                nama_penanggung_jawab: '',
                tahun_kegiatan: '',
                rk_jpt: '',
                iki_jpt: '',
                nama_rk_kegiatan: ''
            };

            // ✅ PENTING: mutate, jangan replace
            Object.assign(formData, payload);

            // autocomplete pegawai
            selectedId = formData.id_penanggung_jawab ?? '';
            search     = formData.nama_penanggung_jawab ?? '';

            // ✅ PENTING: tunggu DOM & Alpine sync
            $nextTick(() => {
                if (formData.rk_jpt) {
                    loadIkiByRk(formData.rk_jpt, formData);
                }
            });
        ">
    <form
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

                {{-- Nama Ketua / Penanggung Jawab --}}
                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: '',
                    highlightedIndex: -1,
                    ketuaTims: @js($ketuaTims),
                
                    filtered() {
                        if (this.search.length === 0) return [];
                        return this.ketuaTims.filter(p => p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase()));
                    },
                
                    selectPegawai(p) {
                        this.search = p.nama_pegawai;
                        this.selectedId = p.id_pegawai;
                        this.open = false;
                        this.highlightedIndex = -1;
                    },
                
                    highlightNext() { if (this.highlightedIndex < this.filtered().length - 1) this.highlightedIndex++; },
                    highlightPrev() { if (this.highlightedIndex > 0) this.highlightedIndex--; },
                    selectHighlighted() { if (this.highlightedIndex >= 0) this.selectPegawai(this.filtered()[this.highlightedIndex]); }
                }" class="relative">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Ketua
                    </label>
                    <!-- Input search -->
                    <input type="text" x-model="search" @focus="open = !!search"
                        @input="open = search.length > 0; selectedId = ''"
                        @keydown.arrow-down.prevent="highlightedIndex++" @keydown.arrow-up.prevent="highlightedIndex--"
                        @keydown.enter.prevent="if(highlightedIndex>=0){ search = ketuaTims[highlightedIndex].nama_pegawai; selectedId = ketuaTims[highlightedIndex].id_pegawai; open=false; }"
                        placeholder="Ketik untuk cari nama" 
                        class="h-11 w-full mb-4 rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500">

                    <!-- Hidden input -->
                    <input type="hidden" name="id_penanggung_jawab" :value="selectedId" required>

                    <!-- Dropdown -->
                    <div x-show="open" x-transition
                        class="absolute z-50 mt-1 w-full mb-4 rounded-lg border border-gray-300 bg-white max-h-60 overflow-y-auto dark:border-gray-700 dark:bg-gray-800">
                        <template
                            x-for="(pegawai, index) in ketuaTims.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase()))"
                            :key="pegawai.id_pegawai">
                            <div @click="search = pegawai.nama_pegawai; selectedId = pegawai.id_pegawai; open = false"
                                :class="{
                                    'bg-blue-100 dark:bg-blue-900/40': highlightedIndex === index,
                                    'hover:bg-gray-100 dark:hover:bg-gray-700': highlightedIndex !== index
                                }"
                                class="cursor-pointer px-4 py-2 text-sm dark:text-gray-300" x-text="pegawai.nama_pegawai"></div>
                        </template>
                        <template
                            x-if="ketuaTims.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase())).length === 0">
                            <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">Data tidak ditemukan</div>
                        </template>
                    </div>
                </div>

                {{-- Tahun Kegiatan --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tahun Kegiatan
                    </label>
                    <input type="text" x-model="formData.tahun_kegiatan" name="tahun_kegiatan"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                {{-- Rencana JPT --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Rencana JPT
                    </label>
                    <select id="rk_jpt" name="rk_jpt" x-model="formData.rk_jpt"
                        @change="loadIkiByRk(formData.rk_jpt, formData)"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <option value="" class="dark:text-gray-400">-- Pilih RK JPT --</option>
                        @foreach ($rkJpts as $rk)
                            <option value="{{ $rk->id }}" class="dark:text-gray-300">
                                {{ $rk->nama_rencana_jpt }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Indikator JPT --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Indikator JPT
                    </label>

                    <select id="iki_jpt" name="iki_jpt" x-model="formData.iki_jpt"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">

                        <!-- OPTION DINAMIS -->
                        <option value=""
                            x-text="formData.rk_jpt
                                    ? '-- Pilih IKI JPT --'
                                    : '-- Harap pilih RK JPT dulu --'"
                            class="dark:text-gray-400">
                        </option>

                        <template x-for="iki in formData.ikiOptions" :key="iki.id">
                            <option :value="iki.id" x-text="iki.nama_indikator_jpt" class="dark:text-gray-300">
                            </option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Kegiatan
                    </label>
                    <input type="text" x-model="formData.nama_rk_kegiatan" name="nama_rk_kegiatan"
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

                    <button type="submit"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto dark:bg-brand-600 dark:hover:bg-brand-700">
                        <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                    </button>
                </div>
            </div>
            
        </div>
    </form>
</x-ui.smart-modal>