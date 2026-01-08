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
            id_jenis_kegiatan: '',
            target: '',
            satuan_target: '',
            tanggal_mulai: '',
            tanggal_selesai: '',
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
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white/70 cursor-not-allowed">
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

                {{-- <div x-data="{ isOther: false }">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Jenis Kegiatan
                    </label>

                    <!-- SELECT UI -->
                    <select
                        x-model="formData.id_jenis_kegiatan"
                        @change="isOther = ($event.target.value === 'LAINNYA')"
                        required
                        class="
                            dark:bg-dark-900
                            h-11 w-full mb-4
                            rounded-lg border border-gray-300
                            bg-white dark:bg-gray-900
                            px-4 py-2.5 text-sm
                            focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                        ">
                        <option value="">-- Pilih Jenis Kegiatan --</option>

                        @foreach ($jenisKegiatans as $jenis)
                            <option
                                value="{{ $jenis->id }}"
                                class="
                                    @if($jenis->kategori === 'Utama')
                                        text-green-700 font-medium
                                    @elseif($jenis->kategori === 'Tambahan')
                                        text-orange-700
                                    @endif
                                ">
                                {{ $jenis->jenis_kegiatan }}
                                @if($jenis->kategori)
                                    ({{ $jenis->kategori }})
                                @endif
                            </option>
                        @endforeach


                        <option value="LAINNYA" class="text-blue-700 font-medium">
                            ➕ Lainnya
                        </option>
                    </select>

                    <!-- 🔥 INI YANG DIKIRIM KE SERVER -->
                    <input
                        type="hidden"
                        name="id_jenis_kegiatan"
                        :value="formData.id_jenis_kegiatan"
                    />

                    <!-- INPUT JENIS KEGIATAN BARU -->
                    <div x-show="isOther" x-transition>
                        <input
                            type="text"
                            name="jenis_kegiatan_baru"
                            placeholder="Masukkan jenis kegiatan baru"
                            class="dark:bg-dark-900 h-11 w-full mb-4 rounded-lg border px-4 py-2.5 text-sm"
                        />
                    </div>
                </div> --}}

                <div x-data="{ isOther: false }">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Jenis Kegiatan
                    </label>

                    <!-- SELECT UI -->
                    <select
                        name="id_jenis_kegiatan"
                        x-model="formData.id_jenis_kegiatan"
                        @change="
                            formData.id_jenis_kegiatan = $event.target.value;
                            isOther = ($event.target.value === 'LAINNYA')
                        "
                        required
                        class="
                            dark:bg-dark-900
                            h-11 w-full mb-4
                            rounded-lg border border-gray-300
                            bg-white dark:bg-gray-900
                            px-4 py-2.5 text-sm
                            focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                        ">
                        <option value="">-- Pilih Jenis Kegiatan --</option>

                        @foreach ($jenisKegiatans as $jenis)
                            <option value="{{ $jenis->id }}"
                                class="
                                    @if($jenis->kategori === 'Utama')
                                        text-green-700 font-medium
                                    @elseif($jenis->kategori === 'Tambahan')
                                        text-orange-700
                                    @endif">
                                {{ $jenis->jenis_kegiatan }}
                                ({{ $jenis->kategori }})
                            </option>
                        @endforeach

                        <option value="LAINNYA" class="text-blue-700 font-medium">
                            ➕ Lainnya
                        </option>
                    </select>

                    {{-- <!-- YANG DIKIRIM KE SERVER -->
                    <input
                        type="hidden"
                        name="id_jenis_kegiatan"
                        :value="formData.id_jenis_kegiatan"
                    /> --}}

                    <!-- INPUT JENIS KEGIATAN BARU -->
                    <div x-show="isOther" x-transition>
                        <input
                            type="text"
                            name="jenis_kegiatan_baru"
                            placeholder="Masukkan jenis kegiatan baru"
                            class="dark:bg-dark-900 h-11 w-full mb-4 rounded-lg border px-4 py-2.5 text-sm"
                        />
                    </div>
                </div>


                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Target
                    </label>
                    <input type="number" x-model="formData.target" name="target"
                        placeholder="Misalnya : 200"
                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
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
