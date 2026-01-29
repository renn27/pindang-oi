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
                rounded-3xl bg-white">

            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3">
                <h4 class="text-2xl font-semibold text-gray-800"
                    x-text="mode === 'create' ? 'Tambah Anggota' : 'Edit Data Anggota'"></h4>
                <p class="mt-1 text-sm text-gray-500"
                    x-text="mode === 'create' ? 'Tambahkan penugasan kepada anggota' : 'Edit anggota yang sudah ditugaskan'">
                </p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
                <!-- Nama Sub Kegiatan (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Sub Kegiatan
                    </label>

                    <input type="text" :value="formData.nama_sub_kegiatan" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800 cursor-not-allowed">
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
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
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

                <div x-data="{ isOther: false }">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
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
                            focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Pilih Jenis Kegiatan --</option>

                        @foreach ($jenisKegiatans as $jenis)
                            <option
                                value="{{ $jenis->id }}"
                                data-text="{{ $jenis->jenis_kegiatan }}"
                                class="
                                    @if($jenis->kategori === 'Utama')
                                        text-green-700 font-medium
                                    @elseif($jenis->kategori === 'Tambahan')
                                        text-orange-700
                                    @endif">
                                {{ $jenis->jenis_kegiatan }} ({{ $jenis->kategori }})
                            </option>
                        @endforeach

                        <option value="LAINNYA" class="text-blue-700 font-medium">
                            ➕ Lainnya
                        </option>
                    </select>

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

                <div
                    x-data="{
                        butuhDl: false,
                        isLocked: true,

                        wajibJenis: [2,3,4,5],
                        {{-- wajibJenis: ['Pendataan', 'Pengawasan', 'Supervisi', 'Perjalanan Dinas'], --}}

                        get jenisId() {
                            return Number(formData?.id_jenis_kegiatan || 0)
                        },

                        get jenisSelected() {
                            return this.jenisId > 0
                        },

                        {{-- get jenisNama() {
                            return formData?.jenis_kegiatan || ''
                        },

                        get jenisSelected() {
                            return this.jenisNama !== ''
                        }, --}}

                        syncState() {
                            console.log('jenis_kegiatan dari formData:', formData?.jenis_kegiatan)
                            {{-- console.log('hasil get jenisNama:', this.jenisNama) --}}
                            console.log('hasil get jenisNama:', this.jenisId)
                            {{-- const isWajib = this.wajibJenis.includes(this.jenisNama) --}}
                            const isWajib = this.wajibJenis.includes(this.jenisId)
                            {{-- const dbButuh = Number(formData?.butuh_dl) === 1 --}}

                            // ================= CREATE =================
                            if (mode === 'create') {
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
                            if (!this.jenisSelected) {
                                // edge case: edit tapi jenis dikosongkan
                                this.butuhDl = false
                                this.isLocked = true
                                return
                            }

                            if (isWajib) {
                                // jenis wajib → selalu ON & terkunci
                                this.butuhDl = true
                                this.isLocked = true
                            } else {
                                // jenis tidak wajib → toggle bebas
                                // ⚠️ PERTAHANKAN nilai user, JANGAN reset ke db
                                this.butuhDl = false
                                this.isLocked = false
                            }
                        }
                    }"
                    x-effect="syncState()"
                    class="mb-4">

                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Kebutuhan Dinas Luar (DL)
                    </label>

                    <div class="flex items-center gap-4">
                        <!-- Toggle UI -->
                        <button
                            type="button"
                            @click="if (!isLocked) butuhDl = !butuhDl"
                            :class="{
                                'bg-brand-500': butuhDl,
                                'bg-gray-300': !butuhDl,
                                'cursor-not-allowed opacity-70': isLocked
                            }"
                            class="relative inline-flex h-7 w-14 items-center rounded-full"
                        >
                            <span
                                :class="butuhDl ? 'translate-x-7' : 'translate-x-1'"
                                class="inline-block h-5 w-5 bg-white rounded-full transition"
                            ></span>
                        </button>

                        <span
                            class="text-sm font-medium"
                            :class="butuhDl ? 'text-brand-600' : 'text-gray-500'"
                            x-text="
                                !jenisSelected
                                    ? 'Pilih dulu jenis kegiatan'
                                    : (butuhDl ? 'Butuh DL' : 'Tidak Butuh DL')
                            "
                        ></span>
                    </div>

                    <!-- Helper text -->
                    <p x-show="!jenisSelected" class="mt-1 text-xs font-medium text-brand-500/80">
                        Pilih jenis kegiatan untuk menentukan kebutuhan DL.
                    </p>

                    <p x-show="isLocked && jenisSelected" class="mt-1 text-xs text-gray-500">
                        Jenis kegiatan ini otomatis membutuhkan DL dan tidak dapat diubah.
                    </p>

                    <!-- Hidden input -->
                    <input type="hidden" name="butuh_dl" :value="butuhDl ? 1 : 0">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Target
                    </label>
                    <input type="number" x-model="formData.target" name="target"
                        placeholder="Misalnya : 200"
                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Satuan Target
                    </label>
                    <input type="text" x-model="formData.satuan_target" name="satuan_target"
                        placeholder="Misalnya : Dokumen, Kegiatan, dll"
                        class="dark:bg-dark-900 h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
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
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
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

<!-- TOGGLE BUTUH DL -->
                {{-- <div
                    x-data="{
                        butuhDl: false,
                        isLocked: false,
                        jenisSelected: false,

                        wajibDl: ['Perjalanan Dinas', 'Supervisi', 'Pengawasan', 'Pendataan'],

                        checkJenisKegiatan() {
                            const select = document.getElementById('jenis_kegiatan_select');
                            const selectedValue = select.value;

                            // belum pilih jenis kegiatan
                            if (!selectedValue) {
                                this.jenisSelected = false;
                                this.butuhDl = false;
                                this.isLocked = false;
                                return;
                            }

                            this.jenisSelected = true;

                            const selectedOption = select.options[select.selectedIndex];
                            const text = selectedOption.dataset.text ?? '';

                            if (this.wajibDl.includes(text)) {
                                // wajib DL
                                this.butuhDl = true;
                                this.isLocked = true;
                            } else {
                                // bukan wajib DL → reset
                                this.butuhDl = false;
                                this.isLocked = false;
                            }
                        }
                    }"
                    x-init="
                        butuhDl = formData.butuh_dl ?? false;
                        $nextTick(() => checkJenisKegiatan());
                    "
                    @change.window="
                        if ($event.target.id === 'jenis_kegiatan_select') {
                            checkJenisKegiatan();
                        }
                    " class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Kebutuhan Dinas Luar (DL)
                    </label>

                    <div class="flex items-center gap-4">
                        <!-- Toggle UI -->
                        <button
                            type="button"
                            @click="
                                if (!jenisSelected) return;
                                if (!isLocked) butuhDl = !butuhDl
                            "
                            :class="{
                                'bg-brand-500': butuhDl,
                                'bg-gray-300': !butuhDl,
                                'cursor-not-allowed opacity-70': !jenisSelected || isLocked
                            }"
                            class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors duration-300"
                            :title="!jenisSelected ? 'Pilih jenis kegiatan terlebih dahulu' : ''">
                            <span
                                :class="{
                                    'translate-x-7': butuhDl,
                                    'translate-x-1': !butuhDl
                                }"
                                class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform duration-300"
                            ></span>
                        </button>

                        <!-- Text status -->
                        <span
                            class="text-sm font-medium"
                            :class="butuhDl ? 'text-brand-600' : 'text-gray-500'"
                            x-text="
                                !jenisSelected
                                    ? 'Pilih jenis kegiatan dulu'
                                    : (butuhDl ? 'Butuh DL' : 'Tidak Butuh DL')
                            "
                        ></span>
                    </div>

                    <!-- Helper text -->
                    <p x-show="!jenisSelected" class="mt-1 text-xs font-medium text-brand-500/80">
                        Pilih jenis kegiatan untuk menentukan kebutuhan DL.
                    </p>

                    <p x-show="isLocked" class="mt-1 text-xs text-gray-500">
                        Jenis kegiatan ini otomatis membutuhkan DL dan tidak dapat diubah.
                    </p>

                    <!-- Hidden input -->
                    <input type="hidden" name="butuh_dl" :value="butuhDl ? 1 : 0">
                </div> --}}
                {{-- <div
                    x-data="{
                        butuhDl: false,
                        isLocked: true,
                        wajibJenis: [1,2,3,4],

                        syncState() {
                            const jenisId = Number(formData?.id_jenis_kegiatan);
                            const isWajib = this.wajibJenis.includes(jenisId);
                            const dbButuh = Number(formData?.butuh_dl) === 1;

                            // CREATE
                            if (mode === 'create') {
                                if (!jenisId) {
                                    this.butuhDl = false;
                                    this.isLocked = true;
                                } else if (isWajib) {
                                    this.butuhDl = true;
                                    this.isLocked = true;
                                } else {
                                    this.butuhDl = false;
                                    this.isLocked = false;
                                }
                                return;
                            }

                            // EDIT
                            this.butuhDl = dbButuh;

                            if (dbButuh && isWajib) {
                                this.isLocked = true;
                            } else {
                                this.isLocked = false;
                            }
                        }
                    }"

                    x-effect="syncState()"
                    class="mb-4">

                    <label class="mb-1.5 block text-sm font-medium text-gray-700">
                        Kebutuhan Dinas Luar (DL)
                    </label>

                    <div class="flex items-center gap-4">
                        <!-- Toggle UI -->
                        <button
                            type="button"
                            @click="if (!isLocked) butuhDl = !butuhDl"
                            :class="{
                                'bg-brand-500': butuhDl,
                                'bg-gray-300': !butuhDl,
                                'cursor-not-allowed opacity-70': isLocked
                            }"
                            class="relative inline-flex h-7 w-14 items-center rounded-full">
                            <span
                                :class="butuhDl ? 'translate-x-7' : 'translate-x-1'"
                                class="inline-block h-5 w-5 bg-white rounded-full transition"
                            ></span>
                        </button>

                        <span
                            class="text-sm font-medium"
                            :class="butuhDl ? 'text-brand-600' : 'text-gray-500'"
                            x-text="butuhDl ? 'Butuh DL' : 'Tidak Butuh DL'"
                        ></span>
                    </div>

                    <!-- Helper text -->
                    <p x-show="!jenisSelected" class="mt-1 text-xs font-medium text-brand-500/80">
                        Pilih jenis kegiatan untuk menentukan kebutuhan DL.
                    </p>

                    <p x-show="isLocked" class="mt-1 text-xs text-gray-500">
                        Jenis kegiatan ini otomatis membutuhkan DL dan tidak dapat diubah.
                    </p>

                    <!-- Hidden input -->
                    <input type="hidden" name="butuh_dl" :value="butuhDl ? 1 : 0">
                </div> --}}
