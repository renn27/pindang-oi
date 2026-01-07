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

        // Prefill selectedId untuk autocomplete pegawai
        selectedId = formData.id_penanggung_jawab ?? '';
        search = formData.nama_penanggung_jawab ?? '';

        // Prefill IKI jika edit
        if(formData.rk_jpt) {
            fetch(`/rencana-indikator-jpt/${formData.rk_jpt}/indikator`)
                .then(res => res.json())
                .then(data => formData.ikiOptions = data);
        }
        ">

    @if (session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
    @endif

    @if (session('error'))
    <script>
        alert("{{ session('error') }}");
    </script>
    @endif

    <form id="masterKegiatanForm"
        method="POST"
        action="{{ route('master-kegiatan.store') }}">
        @csrf
        <div class="relative flex h-[90vh] w-full max-w-[900px] flex-col overflow-hidden
               rounded-3xl bg-white dark:bg-gray-900">

            <!-- HEADER -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90">
                            Add Rencana Kinerja Ketua
                        </h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Tambahkan rencana kinerja ketua baru
                        </p>
                    </div>
                </div>
            </div>

            <!-- BODY (SCROLLABLE) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
                <div class="grid grid-cols-1 gap-y-5">
                    <!-- Tahun -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Tahun
                        </label>
                        <input type="text" name="tahun_kegiatan" id="tahunInput" value="{{ now()->format('Y') }}"
                            class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>

                    {{-- Rencana JPT --}}
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            RK JPT
                        </label>
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
                            class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                            <option value="">-- Pilih RK JPT --</option>
                            @foreach ($rkJpts as $rk)
                            <option value="{{ $rk->id }}">
                                {{ $rk->nama_rencana_jpt }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Indikator JPT --}}
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            IKI JPT
                        </label>
                        <select
                            id="iki_jpt"
                            name="iki_jpt"
                            x-model="formData.iki_jpt"
                            class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                            <option value="">-- Harap pilih RK dulu --</option>
                            <template x-for="iki in formData.ikiOptions" :key="iki.id">
                                <option :value="iki.id" x-text="iki.nama_indikator_jpt" :selected="formData.iki_jpt == iki.id"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Kolom Bidang -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Bidang
                        </label>
                        <div class="relative z-20 bg-transparent w-full md:w-3/4">
                            <select
                                id="bidang"
                                name="id_bidang"
                                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
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

                    {{-- Kolom Ketua --}}
                    <div class="relative flex flex-col gap-2 md:flex-row md:items-center"
                        x-data="{
                        open: false,
                        search: '',
                        selectedId: '',
                        highlightedIndex: -1,
                        pegawais: @js($pegawais),

                        filtered() {
                            if(this.search.length === 0) return [];
                            return this.pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase()));
                        },

                        selectPegawai(p) {
                            this.search = p.nama_pegawai;
                            this.selectedId = p.id_pegawai;
                            this.open = false;
                            this.highlightedIndex = -1;
                        },

                        highlightNext() {
                            if(this.highlightedIndex < this.filtered().length - 1) this.highlightedIndex++;
                        },
                        highlightPrev() {
                            if(this.highlightedIndex > 0) this.highlightedIndex--;
                        },
                        selectHighlighted() {
                            if(this.highlightedIndex >= 0) this.selectPegawai(this.filtered()[this.highlightedIndex]);
                        }
                    }">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Nama Ketua
                        </label>

                        <div class="relative md:w-3/4">
                            <!-- Input search -->
                            <input
                                type="text"
                                x-model="search"
                                @focus="open = !!search"
                                @input="open = search.length > 0; selectedId = ''"
                                @keydown.arrow-down.prevent="highlightedIndex++"
                                @keydown.arrow-up.prevent="highlightedIndex--"
                                @keydown.enter.prevent="if(highlightedIndex>=0){ search = pegawais[highlightedIndex].nama_pegawai; selectedId = pegawais[highlightedIndex].id_pegawai; open=false; }"
                                placeholder="Ketik untuk cari nama"
                                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">

                            <!-- Hidden input -->
                            <input type="hidden" name="id_penanggung_jawab" :value="selectedId" required>

                            <!-- Dropdown -->
                            <div
                                x-show="open && search.length > 0"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                @click.away="open = false"
                                class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800 max-h-60 overflow-y-auto">

                                <template x-if="filtered().length > 0">
                                    <template x-for="(pegawai, index) in filtered()" :key="pegawai.id_pegawai">
                                        <div
                                            @click="selectPegawai(pegawai)"
                                            :class="{
                            'bg-brand-50 dark:bg-brand-900/30': highlightedIndex===index,
                            'hover:bg-gray-50 dark:hover:bg-gray-700': highlightedIndex!==index
                        }"
                                            class="cursor-pointer px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                                            x-text="pegawai.nama_pegawai">
                                        </div>
                                    </template>
                                </template>

                                <template x-if="search.length > 0 && filtered().length === 0">
                                    <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        Data tidak ditemukan
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom RK Ketua-->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Rencana Kinerja Ketua
                        </label>
                        <input type="text" placeholder="Tulis rencana kinerja ketua" name="nama_rk_kegiatan" id="rkKetua"
                            class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
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
                                class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9 3.75C9.41421 3.75 9.75 4.08579 9.75 4.5V8.25H13.5C13.9142 8.25 14.25 8.58579 14.25 9C14.25 9.41421 13.9142 9.75 13.5 9.75H9.75V13.5C9.75 13.9142 9.41421 14.25 9 14.25C8.58579 14.25 8.25 13.9142 8.25 13.5V9.75H4.5C4.08579 9.75 3.75 9.41421 3.75 9C3.75 8.58579 4.08579 8.25 4.5 8.25H8.25V4.5C8.25 4.08579 8.58579 3.75 9 3.75Z" fill="" />
                                </svg>
                                Tambah RK Anggota
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER (FIXED) -->
            <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button @click="open = false" type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                        Close
                    </button>

                    <button id="saveAllButton" type="button"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        Save All Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-ui.smart-modal>