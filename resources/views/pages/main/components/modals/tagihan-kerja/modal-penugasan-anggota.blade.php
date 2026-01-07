<!-- Modal Penugasan Anggota -->
<x-ui.smart-modal id="modal-penugasan-anggota" class="max-w-2xl"
    @open-smart-modal.window="
                        if ($event.detail.modalId !== 'modal-penugasan-anggota') return;

                        mode = $event.detail.mode ?? 'create';
                        itemKey = $event.detail.key ?? null;
                        // Ambil data dari dispatch
                        formData = $event.detail.data ?? {
                            id_sub_kegiatan: '',
                            nama_sub_kegiatan: '',
                            id_anggota: '',
                            nama_anggota: '',
                            target: '',
                            tanggal_mulai: '',
                            tanggal_selesai: '',
                            status: ''
                        }">
<form
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
               rounded-3xl bg-white dark:bg-gray-900">

        <!-- HEADER (FIXED) -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
            <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90"
                x-text="mode === 'create' ? 'Tambah Anggota' : 'Edit Data Anggota'"></h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                x-text="mode === 'create' ? 'Tambahkan penugasan kepada anggota' : 'Edit anggota yang sudah ditugaskan'">
            </p>
        </div>

        <!-- BODY (SCROLL DI SINI) -->
        <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
            

                <!-- Nama Sub Kegiatan (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Sub Kegiatan
                    </label>

                    <input type="text" :value="formData.nama_sub_kegiatan" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                            dark:border-gray-700 dark:bg-gray-800 dark:text-white/70 cursor-not-allowed">
                </div>

                {{-- Nama Ketua / Penanggung Jawab --}}
                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: '',
                    highlightedIndex: -1,
                    pegawais: @js($pegawais),
                
                    init() {
                        // ketika modal edit dibuka
                        if (this.$root.formData?.nama_anggota) {
                            this.search = this.$root.formData.nama_anggota;
                            this.selectedId = this.$root.formData.id_anggota;
                        }
                    },
                
                    filtered() {
                        if (this.search.length === 0) return [];
                        return this.pegawais.filter(p =>
                            p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase())
                        );
                    },
                
                    selectPegawai(p) {
                        this.search = p.nama_pegawai;
                        this.selectedId = p.id_pegawai;
                        // sinkron ke formData (PENTING)
                        this.$root.formData.nama_anggota = p.nama_pegawai;
                        this.$root.formData.id_anggota = p.id_pegawai;
                
                        this.open = false;
                        this.highlightedIndex = -1;
                    },
                
                    highlightNext() { if (this.highlightedIndex < this.filtered().length - 1) this.highlightedIndex++; },
                    highlightPrev() { if (this.highlightedIndex > 0) this.highlightedIndex--; },
                    selectHighlighted() { if (this.highlightedIndex >= 0) this.selectPegawai(this.filtered()[this.highlightedIndex]); }
                }">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Nama Anggota
                    </label>
                    <!-- Input search -->
                    <input type="text" x-model="search" class="h-11 w-full mb-4 rounded-lg border px-4 py-2 text-sm"
                        placeholder="Ketik untuk cari nama" {{-- x-model="formData.nama_anggota" --}} @focus="open = true"
                        @input="open = true; selectedId = ''" {{-- @focus="open = !!search" @input="open = search.length > 0; selectedId = ''" --}}
                        @keydown.arrow-down.prevent="highlightedIndex++" @keydown.arrow-up.prevent="highlightedIndex--"
                        @keydown.enter.prevent="if(highlightedIndex>=0){ search = pegawais[highlightedIndex].nama_pegawai; selectedId = pegawais[highlightedIndex].id_pegawai; open=false; }">

                    <!-- Hidden input -->
                    <input type="hidden" name="id_anggota" :value="selectedId" required>

                    <!-- Dropdown -->
                    <div x-show="open" x-transition
                        class="absolute z-50 mt-1 w-full mb-4 rounded-lg border bg-white max-h-60 overflow-y-auto">
                        <template
                            x-for="(pegawai, index) in pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase()))"
                            :key="pegawai.id_pegawai">
                            <div @click="search = pegawai.nama_pegawai; selectedId = pegawai.id_pegawai; open = false"
                                :class="{
                                    'bg-blue-100': highlightedIndex ===
                                        index,
                                    'hover:bg-gray-100': highlightedIndex !== index
                                }"
                                class="cursor-pointer px-4 py-2 text-sm" x-text="pegawai.nama_pegawai"></div>
                        </template>
                        <template
                            x-if="pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase())).length === 0">
                            <div class="px-4 py-2 text-sm text-gray-500">Data tidak ditemukan</div>
                        </template>
                    </div>
                </div>
                {{-- <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Jenis Kegiatan
                                    </label>
                                    <input placeholder="Contoh: Pencacahan, Supervisi, Pengawasan, dll" type="text"
                                        x-model="formData.jenis_kegiatan" name="jenis_kegiatan"
                                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div> --}}

                <div x-data="{ jenis: '' }">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Jenis Kegiatan
                    </label>

                    <select name="jenis_kegiatan_select" x-model="jenis"
                        class="dark:bg-dark-900 h-11 w-full mb-4 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">

                        <option value="">-- Pilih Jenis Kegiatan --</option>
                        <option value="Kegiatan Lapangan">Kegiatan Lapangan</option>
                        <option value="Dokumentasi">Dokumentasi</option>
                        <option value="Penyusunan Dokumen">Penyusunan Dokumen</option>
                        <option value="LAINNYA">➕ Lainnya</option>
                    </select>

                    <!-- Input Lainnya -->
                    <div x-show="jenis === 'LAINNYA'" x-transition class="mt-3">
                        <input type="text" name="jenis_kegiatan_lainnya" placeholder="Tulis jenis kegiatan lainnya"
                            class="dark:bg-dark-900 h-11 w-full mb-4 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Satuan Target
                    </label>
                    <input type="text" x-model="formData.satuan_target" name="satuan_target"
                        placeholder="Misalnya : Dokumen, Kegiatan, dll"
                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Tanggal Mulai
                    </label>
                    <x-form.date-picker id="tanggal_mulai" x-model="formData.tanggal_mulai" name="tanggal_mulai"
                        placeholder="Date Picker" defaultDate="{{ now()->format('Y-m-d') }}" />
                </div>
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Tanggal Berakhir (Deadline)
                    </label>
                    <x-form.date-picker id="tanggal_selesai" x-model="formData.tanggal_selesai" name="tanggal_selesai"
                        placeholder="Date Picker" defaultDate="{{ now()->format('Y-m-d') }}" />
                </div>
                {{-- <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Status
                                    </label>
                                    <select
                                        name="status"
                                        x-model="formData.status"
                                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Belum Dikirim" :selected="formData.status === 'Belum Dikirim'">Belum Dikirim</option>
                                        <option value="Sudah Dikirim" :selected="formData.status === 'Sudah Dikirim'">Sudah Dikirim</option>
                                        <option value="Masih Revisi" :selected="formData.status === 'Masih Revisi'">Masih Revisi</option>
                                        <option value="Sudah Diterima" :selected="formData.status === 'Sudah Diterima'">Sudah Diterima</option>
                                    </select>
                                </div> --}}
                
        </div>
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
