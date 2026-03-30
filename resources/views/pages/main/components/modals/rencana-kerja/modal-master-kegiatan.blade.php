{{-- Modal Master Kegiatan --}}
<x-ui.smart-modal id="modal-master-kegiatan" class="max-w-4xl"
    x-data="{
            formData: { rk_jpt:'', iki_jpt:'', ikiOptions:[] },
            search: '',
            selectedId: '',
            open: false
        }"

    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-master-kegiatan') return;

        mode    = $event.detail.mode ?? 'create'
        itemKey  = $event.detail.key ?? null
        formData = $event.detail.data ??
                {
                    id_bidang: '',
                    nama_bidang: '',
                    id_penanggung_jawab: '',
                    nama_penanggung_jawab: '',
                    tahun_kegiatan: '',
                    rk_jpt : '',
                    iki_jpt : '',
                    nama_rk_kegiatan : '',
                    ikiOptions: [] };

        selectedId = formData.id_penanggung_jawab ?? '';
        search = formData.nama_penanggung_jawab ?? '';

        if(formData.rk_jpt) {
            fetch(`/rencana-indikator-jpt/${formData.rk_jpt}/indikator`)
                .then(res => res.json())
                .then(data => formData.ikiOptions = data);
        }
        ">

    <form id="masterKegiatanForm"
        method="POST"
        action="{{ route('master-kegiatan.store') }}">
        @csrf
        <div class="relative flex h-[90vh] w-full max-w-[900px] flex-col overflow-hidden
                rounded-3xl bg-white dark:bg-gray-800">

            <!-- HEADER -->
            <div class="shrink-0 border-b border-gray-200 dark:border-gray-700 px-6 py-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-2xl font-semibold text-gray-800 dark:text-white">
                            Tambahkan Kegiatan
                        </h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Masukkan data kegiatan yang baru
                        </p>
                    </div>
                </div>
            </div>

            <!-- BODY (SCROLLABLE) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
                <div class="grid grid-cols-1 gap-y-5">

                    {{-- ====== VALIDATION BANNER ====== --}}
                    <div id="validationBanner"
                        class="hidden rounded-xl border border-red-300 dark:border-red-500/40 bg-red-50 dark:bg-red-500/10 px-4 py-3">
                        <p class="text-sm font-medium text-red-700 dark:text-red-400 mb-1">
                            ⚠ Ada beberapa field yang belum diisi atau tidak valid:
                        </p>
                        <ul id="validationList" class="list-disc pl-5 space-y-1"></ul>
                    </div>
                    {{-- ====== END VALIDATION BANNER ====== --}}

                    <!-- Tahun -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Tahun
                        </label>
                        <div class="md:w-3/4">
                            <input type="text" name="tahun_kegiatan" id="tahunInput" value="{{ now()->format('Y') }}"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                            <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="tahunInput">Tahun wajib diisi</p>
                        </div>
                    </div>

                    {{-- Rencana JPT --}}
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Rencana JPT
                        </label>
                        <div class="md:w-3/4">
                            <select
                                id="rk_jpt"
                                name="rk_jpt"
                                x-model="formData.rk_jpt"
                                @change="
                                    formData.iki_jpt = '';
                                    formData.ikiOptions = [];
                                    if(formData.rk_jpt){
                                        fetch(`/rencana-indikator-jpt/${formData.rk_jpt}/indikator`)
                                            .then(res => res.json())
                                            .then(data => formData.ikiOptions = data);
                                }"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10">
                                <option value="">-- Pilih RK JPT --</option>
                                @foreach ($rkJpts as $rk)
                                <option value="{{ $rk->id }}">
                                    {{ $rk->nama_rencana_jpt }}
                                </option>
                                @endforeach
                            </select>
                            <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="rk_jpt">Rencana JPT wajib dipilih</p>
                        </div>
                    </div>

                    {{-- Indikator JPT --}}
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Indikator JPT
                        </label>
                        <div class="md:w-3/4">
                            <select id="iki_jpt" name="iki_jpt" x-model="formData.iki_jpt"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10">
                                <option value=""
                                    x-text="formData.rk_jpt ? '-- Pilih IKI JPT --' : '-- Harap pilih RK JPT dulu --'">
                                </option>
                                <template x-for="iki in formData.ikiOptions" :key="iki.id">
                                    <option :value="iki.id" x-text="iki.nama_indikator_jpt" :selected="formData.iki_jpt == iki.id">
                                    </option>
                                </template>
                            </select>
                            <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="iki_jpt">Indikator JPT wajib dipilih</p>
                        </div>
                    </div>

                    <!-- Kolom Bidang -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Bidang
                        </label>
                        <div class="relative z-20 bg-transparent w-full md:w-3/4">
                            <select
                                id="bidang"
                                name="id_bidang"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10">
                                @foreach ($bidangs as $bidang)
                                <option value="{{ $bidang->id_bidang }}">
                                    {{ $bidang->nama_bidang }}
                                </option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    {{-- Nama Ketua / Penanggung Jawab --}}
                    <div
                        x-data="{
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
                        selectHighlighted() { if (this.highlightedIndex >= 0) this.selectPegawai(this.filtered()[this.highlightedIndex]); }}"
                        class="flex flex-col gap-2 md:flex-row md:items-start">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Nama Ketua
                        </label>

                        <div class="relative md:w-3/4 w-full">
                            <input
                                type="text"
                                x-model="search"
                                placeholder="Ketik untuk cari nama"
                                id="ketuaSearchInput"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10"
                                @focus="open = !!search"
                                @input="open = search.length > 0; selectedId = ''"
                                @keydown.arrow-down.prevent="highlightedIndex++"
                                @keydown.arrow-up.prevent="highlightedIndex--"
                                @keydown.enter.prevent="
                                    if (highlightedIndex >= 0) {
                                        search = filtered()[highlightedIndex].nama_pegawai;
                                        selectedId = filtered()[highlightedIndex].id_pegawai;
                                        open = false;
                                    }
                                "
                            >
                            <input type="hidden" name="id_penanggung_jawab" :value="selectedId" required>

                            <div
                                x-show="open"
                                x-transition
                                class="absolute left-0 top-full z-50 mt-1 w-full
                                    rounded-lg border dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg
                                    max-h-60 overflow-y-auto"
                            >
                                <template x-for="(pegawai, index) in filtered()" :key="pegawai.id_pegawai">
                                    <div
                                        @click="selectPegawai(pegawai)"
                                        class="cursor-pointer px-4 py-2 text-sm dark:text-gray-300"
                                        :class="{
                                            'bg-blue-100 dark:bg-blue-900/30': highlightedIndex === index,
                                            'hover:bg-gray-100 dark:hover:bg-gray-700': highlightedIndex !== index
                                        }"
                                        x-text="pegawai.nama_pegawai"
                                    ></div>
                                </template>

                                <template x-if="filtered().length === 0 && search.length > 0">
                                    <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                        Nama Ketua tidak ditemukan
                                    </div>
                                </template>
                            </div>
                            <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="ketuaSearchInput">Ketua wajib dipilih dari daftar (pastikan klik nama dari dropdown)</p>
                        </div>
                    </div>

                    <!-- Kolom Nama Kegiatan -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300 md:w-1/4">
                            Nama Kegiatan
                        </label>
                        <div class="md:w-3/4">
                            <input type="text" placeholder="Tulis Nama Kegiatan" name="nama_rk_kegiatan" id="rkKetua"
                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                            <p class="field-error-msg text-xs text-red-600 dark:text-red-400 mt-1 hidden" data-for="rkKetua">Nama kegiatan wajib diisi</p>
                        </div>
                    </div>

                    <!-- CONTAINER UNTUK SECTION RK ANGGOTA -->
                    <div id="rkAnggotaContainer" class="space-y-6">
                        <!-- Section akan ditambahkan di sini -->
                    </div>

                    <!-- TOMBOL TAMBAH RK ANGGOTA -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <div class="md:w-1/4"></div>
                        <div class="md:w-3/4">
                            <button type="button" @click="tambahRKAnggota()"
                                class="flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9 3.75C9.41421 3.75 9.75 4.08579 9.75 4.5V8.25H13.5C13.9142 8.25 14.25 8.58579 14.25 9C14.25 9.41421 13.9142 9.75 13.5 9.75H9.75V13.5C9.75 13.9142 9.41421 14.25 9 14.25C8.58579 14.25 8.25 13.9142 8.25 13.5V9.75H4.5C4.08579 9.75 3.75 9.41421 3.75 9C3.75 8.58579 4.08579 8.25 4.5 8.25H8.25V4.5C8.25 4.08579 8.58579 3.75 9 3.75Z" fill="" />
                                </svg>
                                Tambah Sub Kegiatan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER (FIXED) -->
            <div class="shrink-0 border-t border-gray-200 dark:border-gray-700 px-6 py-3">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button @click="open = false" type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:w-auto">
                        Tutup
                    </button>

                    <button id="saveAllButton" type="button"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        Simpan Semua Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-ui.smart-modal>
