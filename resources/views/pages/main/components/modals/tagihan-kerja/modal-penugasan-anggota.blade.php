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
            jenis_kegiatan: '',
            target: '',
            satuan_target: '',
            butuh_dl: 0,
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
                rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">

            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white"
                    x-text="mode === 'create' ? 'Tambah Anggota' : 'Edit Data Anggota'"></h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                    x-text="mode === 'create' ? 'Tambahkan penugasan kepada anggota' : 'Edit anggota yang sudah ditugaskan'">
                </p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900">
                <!-- Nama Sub Kegiatan (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Sub Kegiatan
                    </label>

                    <input type="text" :value="formData.nama_sub_kegiatan" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
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
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Anggota
                    </label>
                    <!-- Input search -->
                    <input type="text" x-model="search" class="h-11 w-full mb-4 rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500"
                        placeholder="Ketik untuk cari nama" {{-- x-model="formData.nama_anggota" --}} @focus="open = true"
                        @input="open = true; selectedId = ''" {{-- @focus="open = !!search" @input="open = search.length > 0; selectedId = ''" --}}
                        @keydown.arrow-down.prevent="highlightedIndex++" @keydown.arrow-up.prevent="highlightedIndex--"
                        @keydown.enter.prevent="if(highlightedIndex>=0){ search = pegawais[highlightedIndex].nama_pegawai; selectedId = pegawais[highlightedIndex].id_pegawai; open=false; }">

                    <!-- Hidden input -->
                    <input type="hidden" name="id_anggota" :value="selectedId" required>

                    <!-- Dropdown -->
                    <div x-show="open" x-transition
                        class="absolute z-50 mt-1 w-full mb-4 rounded-lg border border-gray-300 bg-white max-h-60 overflow-y-auto dark:border-gray-700 dark:bg-gray-800">
                        <template
                            x-for="(pegawai, index) in pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase()))"
                            :key="pegawai.id_pegawai">
                            <div @click="search = pegawai.nama_pegawai; selectedId = pegawai.id_pegawai; open = false"
                                :class="{
                                    'bg-blue-100 dark:bg-blue-900/40': highlightedIndex === index,
                                    'hover:bg-gray-100 dark:hover:bg-gray-700': highlightedIndex !== index
                                }"
                                class="cursor-pointer px-4 py-2 text-sm dark:text-gray-300" x-text="pegawai.nama_pegawai"></div>
                        </template>
                        <template
                            x-if="pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase())).length === 0">
                            <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">Data tidak ditemukan</div>
                        </template>
                    </div>
                </div>

                <div x-data="{ isOther: false }">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Jenis Kegiatan
                    </label>

                    <!-- SELECT UI -->
                    <select
                        id="jenis_kegiatan_select"
                        name="id_jenis_kegiatan"
                        x-model="formData.id_jenis_kegiatan"
                        @change="isOther = ($event.target.value === 'LAINNYA')"
                        required
                        class="
                            h-11 w-full mb-4
                            rounded-lg border border-gray-300
                            bg-white
                            px-4 py-2.5 text-sm
                            focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <option value="" class="dark:text-gray-400">-- Pilih Jenis Kegiatan --</option>

                        @foreach ($jenisKegiatans as $jenis)
                            <option
                                value="{{ $jenis->id }}"
                                data-text="{{ $jenis->jenis_kegiatan }}"
                                class="
                                    @if($jenis->kategori === 'Utama')
                                        text-green-700 font-medium dark:text-green-300
                                    @elseif($jenis->kategori === 'Tambahan')
                                        text-orange-700 dark:text-orange-300
                                    @endif">
                                {{ $jenis->jenis_kegiatan }} ({{ $jenis->kategori }})
                            </option>
                        @endforeach

                        <option value="LAINNYA" class="text-blue-700 font-medium dark:text-blue-300">
                            ➕ Lainnya
                        </option>
                    </select>

                    <!-- INPUT JENIS KEGIATAN BARU -->
                    <div x-show="isOther" x-transition>
                        <input
                            type="text"
                            name="jenis_kegiatan_baru"
                            placeholder="Masukkan jenis kegiatan baru"
                            class="h-11 w-full mb-4 rounded-lg border border-gray-300 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500"
                        />
                    </div>
                </div>

                <div
                    x-data="{
                        butuhDl: false,
                        isLocked: true,

                        wajibJenis: [3,4,5,6],

                        get jenisId() {
                            return Number(formData?.id_jenis_kegiatan || 0)
                        },

                        get isLainnya() {
                            return formData?.id_jenis_kegiatan === 'LAINNYA'
                        },

                        get jenisSelected() {
                            return this.jenisId > 0 || this.isLainnya
                        },

                        syncState() {
                            const isWajib = this.wajibJenis.includes(this.jenisId)
                            const fromDB = Boolean(Number(formData?.butuh_dl ?? 0))

                            // ================= CREATE =================
                            if (mode === 'create') {

                                if (this.isLainnya) {
                                    this.butuhDl = false
                                    this.isLocked = false
                                    return
                                }

                                if (!this.jenisSelected) {
                                    this.butuhDl = false
                                    this.isLocked = true
                                } else if (isWajib) {
                                    this.butuhDl = true
                                    this.isLocked = true
                                } else {
                                    this.butuhDl = false
                                    this.isLocked = false
                                }

                                return
                            }

                            // ================= EDIT =================
                            if (this.isLainnya) {
                                this.butuhDl = fromDB
                                this.isLocked = false
                                return
                            }

                            if (!this.jenisSelected) {
                                this.butuhDl = false
                                this.isLocked = true
                                return
                            }

                            if (isWajib) {
                                // 🔒 Wajib DL → selalu ON & locked
                                this.butuhDl = true
                                this.isLocked = true
                            } else {
                                // ✅ OPSIONAL → ambil dari DB
                                this.butuhDl = fromDB
                                this.isLocked = false
                            }
                        }
                    }"
                    x-effect="syncState()"
                    class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Kebutuhan Dinas Luar (DL)
                    </label>

                    <div class="flex items-center gap-4">
                        <!-- Toggle UI -->
                        <button
                            type="button"
                            @click="if (!isLocked) butuhDl = !butuhDl"
                            :class="{
                                'bg-brand-500 dark:bg-brand-600': butuhDl,
                                'bg-gray-300 dark:bg-gray-600': !butuhDl,
                                'cursor-not-allowed opacity-70': isLocked
                            }"
                            class="relative inline-flex h-7 w-14 items-center rounded-full"
                        >
                            <span
                                :class="butuhDl ? 'translate-x-7' : 'translate-x-1'"
                                class="inline-block h-5 w-5 bg-white dark:bg-gray-300 rounded-full transition"
                            ></span>
                        </button>

                        <span
                            class="text-sm font-medium"
                            :class="butuhDl ? 'text-brand-600 dark:text-brand-400' : 'text-gray-500 dark:text-gray-400'"
                            x-text="
                                !jenisSelected
                                    ? 'Pilih dulu jenis kegiatan'
                                    : (butuhDl ? 'Butuh DL' : 'Tidak Butuh DL')
                            "
                        ></span>
                    </div>

                    <!-- Helper text -->
                    <p x-show="!jenisSelected" class="mt-1 text-xs font-medium text-brand-500/80 dark:text-brand-400">
                        Pilih jenis kegiatan untuk menentukan kebutuhan DL.
                    </p>

                    <p x-show="isLocked && jenisSelected" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Jenis kegiatan ini otomatis membutuhkan DL dan tidak dapat diubah.
                    </p>

                    <!-- Hidden input -->
                    <input type="hidden" name="butuh_dl" :value="butuhDl ? 1 : 0">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Target
                    </label>
                    <input type="number" x-model="formData.target" name="target"
                        placeholder="Misalnya : 200"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Satuan Target
                    </label>
                    <input type="text" x-model="formData.satuan_target" name="satuan_target"
                        placeholder="Misalnya : Dokumen, Kegiatan, dll"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tanggal Mulai
                    </label>
                    <x-form.date-picker
                        x-model="formData.tanggal_mulai"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        placeholder="Tanggal Mulai"
                    />
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tanggal Berakhir (Deadline)
                    </label>
                    <x-form.date-picker
                        x-model="formData.tanggal_selesai"
                        id="tanggal_selesai"
                        name="tanggal_selesai"
                        placeholder="Tanggal Selesai"
                    />
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