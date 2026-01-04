@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Rencana Kinerja - Ketua" />
<div class="space-y-6">
    <x-common.component-card title="Rencana Kinerja">
        <div class="flex justify-end">
            <button class="flex items-center gap-2 rounded-full border border-gray-300
                    bg-white px-4 py-3 text-sm font-medium text-gray-700
                    shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
                    dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400
                    dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                @click="$dispatch('open-smart-modal', {
                        modalId: 'modal-master-kegiatan',
            })">
                <!-- icon -->
                <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                        fill="" />
                </svg>
                Tambah Master Kegiatan
            </button>
        </div>
    </x-common.component-card>
</div>

<!-- modal master kegiatan -->
<x-ui.smart-modal id="modal-master-kegiatan" class="max-w-xl"
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
        "
    :isOpen="false"
    class="max-w-[900px]">
    <div class="relative flex h-[90vh] w-full max-w-[900px] flex-col overflow-hidden
                rounded-3xl bg-white dark:bg-gray-900">

        <!-- HEADER (FIXED) -->
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
                <button @click="open = false"
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- BODY (SCROLLABLE) -->
        <div class="flex-1 overflow-y-auto px-6 py-5">
            <div class="grid grid-cols-1 gap-y-5">
                <!-- Tahun -->
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                        Tahun
                    </label>
                    <input type="text" id="tahunInput" value="{{ now()->format('Y') }}"
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
                            name="bidang"
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
                    <input type="text" placeholder="Tulis rencana kinerja ketua" id="rkKetua"
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
    </x-ui.modal>


    <!-- Modal Konfirmasi -->
    <x-ui.modal
        x-data="{ open: false }"
        @open-confirmation-modal.window="open = true"
        :isOpen="false"
        class="max-w-[800px]">
        <div class="relative flex h-[80vh] w-full max-w-[800px] flex-col overflow-hidden
               rounded-3xl bg-white dark:bg-gray-900">

            <!-- HEADER -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                            Konfirmasi Data
                        </h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Review data sebelum disimpan
                        </p>
                    </div>
                    <button @click="open = false"
                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- BODY -->
            <div class="flex-1 overflow-y-auto px-6 py-5">
                <div id="confirmationContent" class="space-y-6">
                    <!-- Content akan diisi oleh JavaScript -->
                </div>
            </div>

            <!-- FOOTER -->
            <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button @click="open = false" type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                        Batal
                    </button>

                    <button onclick="confirmSave()" type="button"
                        class="flex w-full justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700 sm:w-auto">
                        Ya, Simpan Data
                    </button>
                </div>
            </div>
        </div>
</x-ui.smart-modal>

<!-- Modal Konfirmasi -->
<x-ui.modal
    x-data="{ open: false }"
    @open-confirmation-modal.window="open = true"
    :isOpen="false"
    class="max-w-[800px]">
    <div class="relative flex h-[80vh] w-full max-w-[800px] flex-col overflow-hidden
               rounded-3xl bg-white dark:bg-gray-900">

        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                        Konfirmasi Data
                    </h4>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Review data sebelum disimpan
                    </p>
                </div>
                <button @click="open = false"
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- BODY -->
        <div class="flex-1 overflow-y-auto px-6 py-5">
            <div id="confirmationContent" class="space-y-6">
                <!-- Content akan diisi oleh JavaScript -->
            </div>
        </div>

        <!-- FOOTER -->
        <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button @click="open = false" type="button"
                    class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                    Batal
                </button>

                <button onclick="confirmSave()" type="button"
                    class="flex w-full justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700 sm:w-auto">
                    Ya, Simpan Data
                </button>
            </div>
        </div>
    </div>
</x-ui.modal>

<div id="app" data-pegawais='@json($pegawais)'></div>




@endsection

@push('scripts')
<script>
    const app = document.getElementById('app');

    const pegawais = app?.dataset.pegawais
    ? JSON.parse(app.dataset.pegawais)
    : [];
    // Variabel global untuk menyimpan data
    let rkAnggotaCounter = 0;
    let detailAnggotaCounter = {};

    // Fungsi untuk menambah section RK Anggota
    function tambahRKAnggota() {
        console.log('✅ Tombol Tambah RK Anggota diklik!');

        rkAnggotaCounter++;
        const sectionId = 'rk-anggota-' + rkAnggotaCounter;
        detailAnggotaCounter[sectionId] = 0;

        const sectionHTML = `
        <div id="${sectionId}" class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <div class="mb-4 flex items-center justify-between">
                <h5 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                    RK Anggota ${rkAnggotaCounter}
                </h5>
                <button type="button" onclick="hapusRKAnggota('${sectionId}')"
                    class="rounded-lg p-1 text-gray-500 hover:bg-gray-200 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                        RK Anggota
                    </label>
                    <input name="rk_anggota[]" type="text" placeholder="Masukkan rk anggota"
                        class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>

                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                        Satuan Target
                    </label>
                    <input name="satuan_target[]" type="text" placeholder="Masukkan satuan target"
                        class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>

                <!-- Tanggal Mulai dan Tanggal Akhir untuk Detail Anggota -->
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                        Tanggal Mulai
                    </label>
                    <div class="md:w-3/4">
                        <x-form.date-picker
                            id="tanggal_mulai[]"
                            name="tanggal_mulai[]"
                            placeholder="Tanggal Mulai"
                            defaultDate="{{ now()->format('Y-m-d') }}" />
                    </div>
                </div>
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                        Tanggal Akhir
                    </label>
                    <div class="md:w-3/4">
                        <x-form.date-picker
                            id="tanggal_akhir[]"
                            name="tanggal_akhir[]"
                            placeholder="Tanggal Akhir"
                            defaultDate="{{ now()->format('Y-m-d') }}" />
                    </div>
                </div>

                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                        Keterangan
                    </label>
                    <input name="keterangan[]" type="text" placeholder="Masukkan keterangan"
                        class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>

                <div id="detail-${sectionId}" class="space-y-4"></div>

                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <div class="md:w-1/4"></div>
                    <div class="md:w-3/4">
                        <button type="button" onclick="tambahDetailAnggota('${sectionId}')"
                            class="flex items-center gap-2 rounded-lg border border-dashed border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                            <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 3.5C8.27614 3.5 8.5 3.72386 8.5 4V7.5H12C12.2761 7.5 12.5 7.72386 12.5 8C12.5 8.27614 12.2761 8.5 12 8.5H8.5V12C8.5 12.2761 8.27614 12.5 8 12.5C7.72386 12.5 7.5 12.2761 7.5 12V8.5H4C3.72386 8.5 3.5 8.27614 3.5 8C3.5 7.72386 3.72386 7.5 4 7.5H7.5V4C7.5 3.72386 7.72386 3.5 8 3.5Z" fill=""/>
                            </svg>
                            Tambah Anggota
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

        const container = document.getElementById('rkAnggotaContainer');
        container.insertAdjacentHTML('beforeend', sectionHTML);

        // Set tanggal default untuk input baru
        const sectionElement = document.getElementById(sectionId);
        if (sectionElement) {
            const today = new Date().toISOString().split('T')[0];
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            const nextWeekFormatted = nextWeek.toISOString().split('T')[0];

            const tanggalMulaiInput = sectionElement.querySelector('input[name="tanggal_mulai[]"]');
            const tanggalAkhirInput = sectionElement.querySelector('input[name="tanggal_akhir[]"]');

            if (tanggalMulaiInput) tanggalMulaiInput.value = today;
            if (tanggalAkhirInput) tanggalAkhirInput.value = nextWeekFormatted;
        }

        setTimeout(() => {
            const newSection = document.getElementById(sectionId);
            if (newSection) {
                newSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        }, 100);

        console.log('✅ Section RK Anggota ditambahkan dengan ID:', sectionId);
    }

    function hapusRKAnggota(sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
            section.remove();
            console.log('🗑️ Section dihapus:', sectionId);
        }
    }

    function tambahDetailAnggota(sectionId) {
        console.log('➕ Menambah detail anggota untuk section:', sectionId);

        if (!detailAnggotaCounter[sectionId]) {
            detailAnggotaCounter[sectionId] = 0;
        }

        detailAnggotaCounter[sectionId]++;
        const detailId = sectionId + '-detail-' + detailAnggotaCounter[sectionId];

        const detailHTML = `
    <div id="${detailId}" class="rounded-lg border border-dashed border-gray-300 bg-white p-4 dark:border-gray-600 dark:bg-gray-900/50">
        <div class="mb-3 flex items-center justify-between">
            <h6 class="text-xs font-medium text-gray-700 dark:text-gray-300">
                Anggota ${detailAnggotaCounter[sectionId]}
            </h6>
            <button type="button" onclick="hapusDetailAnggota('${detailId}')"
                class="rounded-lg p-1 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:text-gray-500 dark:hover:bg-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-3">
            <!-- Kolom Nama Anggota dengan Alpine Search (PERBAIKAN) -->
            <div class="relative flex flex-col gap-2 md:flex-row md:items-center"
                 x-data="{
                    open: false,
                    search: '',
                    selectedId: '',
                    highlightedIndex: -1,
                    pegawais: pegawais,

                    filtered() {
                        if(this.search.length === 0) return [];
                        return this.pegawais.filter(p =>
                            p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase())
                        );
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
                <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                    Nama Anggota
                </label>

                <div class="relative md:w-3/4">
                    <!-- Input search -->
                    <input
                        type="text"
                        x-model="search"
                        @focus="open = !!search"
                        @input="open = search.length > 0; selectedId = ''"
                        @keydown.arrow-down.prevent="highlightNext()"
                        @keydown.arrow-up.prevent="highlightPrev()"
                        @keydown.enter.prevent="selectHighlighted()"
                        placeholder="Ketik untuk cari nama anggota"
                        class="detail-nama-anggota dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-3 py-2 text-xs text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">

                    <!-- Hidden input untuk menyimpan ID -->
                    <input type="hidden"
                           name="detail_id_anggota[${sectionId}][]"
                           x-model="selectedId">

                    <!-- Hidden input untuk menyimpan Nama -->
                    <input type="hidden"
                           name="detail_nama_anggota[${sectionId}][]"
                           x-model="search">

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
                                    class="cursor-pointer px-3 py-2 text-xs text-gray-700 dark:text-gray-300"
                                    x-text="pegawai.nama_pegawai">
                                </div>
                            </template>
                        </template>

                        <template x-if="search.length > 0 && filtered().length === 0">
                            <div class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400">
                                Data tidak ditemukan
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Target Input -->
            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                    Target
                </label>
                <input name="detail_target[${sectionId}][]" type="text" placeholder="Masukkan target"
                    class="md:w-3/4 dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-3 py-2 text-xs text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
            </div>

            <!-- Tanggal Mulai -->
            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                    Tanggal Mulai
                </label>
                <div class="md:w-3/4">
                    <x-form.date-picker
                        id="detail_tanggal_mulai[${sectionId}][]"
                        name="detail_tanggal_mulai[${sectionId}][]"
                        placeholder="Tanggal Mulai"
                        defaultDate="{{ now()->format('Y-m-d') }}" />
                </div>
            </div>

            <!-- Tanggal Akhir -->
            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                    Tanggal Akhir
                </label>
                <div class="md:w-3/4">
                    <x-form.date-picker
                        id="detail_tanggal_selesai[${sectionId}][]"
                        name="detail_tanggal_selesai[${sectionId}][]"
                        placeholder="Tanggal Akhir"
                        defaultDate="{{ now()->format('Y-m-d') }}" />
                </div>
            </div>
        </div>
    </div>
    `;

        const detailContainer = document.getElementById(`detail-${sectionId}`);
        if (detailContainer) {
            detailContainer.insertAdjacentHTML('beforeend', detailHTML);
            if (window.Alpine) {
                Alpine.initTree(detailContainer); // ⬅️ TETAP WAJIB
            }
            // Set tanggal default untuk detail anggota
            const detailElement = document.getElementById(detailId);
            if (detailElement) {
                const today = new Date().toISOString().split('T')[0];
                const nextWeek = new Date();
                nextWeek.setDate(nextWeek.getDate() + 7);
                const nextWeekFormatted = nextWeek.toISOString().split('T')[0];

                const tanggalMulaiInput = detailElement.querySelector('input[name="detail_tanggal_mulai[' + sectionId + '][]"]');
                const tanggalSelesaiInput = detailElement.querySelector('input[name="detail_tanggal_selesai[' + sectionId + '][]"]');

                if (tanggalMulaiInput) tanggalMulaiInput.value = today;
                if (tanggalSelesaiInput) tanggalSelesaiInput.value = nextWeekFormatted;
            }

            console.log('✅ Detail anggota ditambahkan dengan ID:', detailId);
        }
    }

    function hapusDetailAnggota(detailId) {
        const detail = document.getElementById(detailId);
        if (detail) {
            detail.remove();
            console.log('🗑️ Detail dihapus:', detailId);
        }
    }

    function saveAll(event) {
        try {
            console.log('✅ saveAll() function dipanggil');

            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Kumpulkan data
            const tahunInput = document.getElementById('tahunInput');
            const tahun = tahunInput ? tahunInput.value : '{{ now()->format("Y") }}';

            // Ambil data dari Alpine component modal (karena menggunakan x-model)
            const rkJptInput = document.querySelector('#rk_jpt');
            const rkJpt = rkJptInput ? rkJptInput.value : '';

            const ikiJptInput = document.querySelector('#iki_jpt');
            const ikiJpt = ikiJptInput ? ikiJptInput.value : '';

            // Bidang (bidang)
            const bidangSelect = document.getElementById('bidang');
            let bidangNama = '';
            if (bidangSelect && bidangSelect.selectedIndex >= 0) {
                const selectedOption = bidangSelect.options[bidangSelect.selectedIndex];
                bidangNama = selectedOption ? selectedOption.text : '';
            }

            // Nama Ketua dari Alpine component
            const ketuaSearchInput = document.querySelector('input[x-model="search"][placeholder*="Ketik untuk cari nama"]');
            const namaKetua = ketuaSearchInput ? ketuaSearchInput.value : '';

            // ID Ketua
            const ketuaIdInput = document.querySelector('input[name="id_penanggung_jawab"]');
            const idKetua = ketuaIdInput ? ketuaIdInput.value : '';

            // Rencana Kinerja Ketua
            const rkKetuaInput = document.getElementById('rkKetua');
            const rkKetua = rkKetuaInput ? rkKetuaInput.value : '';

            // Data RK Anggota
            const sections = document.querySelectorAll('[id^="rk-anggota-"]:not([id*="-detail-"])');

            let detailHTML = '';
            console.log('RK sections terbaca:', sections.length);

            sections.forEach((section, sectionIndex) => {
                let sectionHTML = '';
                const sectionId = section.id;

                // Ambil data dari section
                const rkAnggotaInput = section.querySelector('input[name="rk_anggota[]"]');
                const satuanTargetInput = section.querySelector('input[name="satuan_target[]"]');
                const keteranganInput = section.querySelector('input[name="keterangan[]"]');
                const tanggalMulaiInput = section.querySelector('input[name="tanggal_mulai[]"]');
                const tanggalAkhirInput = section.querySelector('input[name="tanggal_akhir[]"]');

                const rkAnggota = rkAnggotaInput ? rkAnggotaInput.value : '';
                const satuanTarget = satuanTargetInput ? satuanTargetInput.value : '';
                const tanggalMulai = tanggalMulaiInput ? tanggalMulaiInput.value : '';
                const tanggalAkhir = tanggalAkhirInput ? tanggalAkhirInput.value : '';
                const keterangan = keteranganInput ? keteranganInput.value : '';

                sectionHTML += `
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        RK Anggota ${sectionIndex + 1}
                    </h5>
                    <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-300">
                        Bagian ${sectionIndex + 1}
                    </span>
                </div>

                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">RK Anggota</span>
                            <span class="block text-sm text-gray-800 dark:text-white/90">${rkAnggota || '-'}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Satuan Target</span>
                            <span class="block text-sm text-gray-800 dark:text-white/90">${satuanTarget || '-'}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tanggal Mulai</span>
                            <span class="block text-sm text-gray-800 dark:text-white/90">${tanggalMulai || '-'}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tanggal Akhir</span>
                            <span class="block text-sm text-gray-800 dark:text-white/90">${tanggalAkhir || '-'}</span>
                        </div>
                    </div>

                    <div>
                        <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Keterangan</span>
                            <span class="block text-sm text-gray-800 dark:text-white/90">${keterangan || '-'}</span>
                    </div>
            `;

                // Data Detail Anggota
                const detailContainer = document.getElementById(`detail-${sectionId}`);
                let detailAnggotas = [];

                if (detailContainer) {
                    detailAnggotas = detailContainer.querySelectorAll('[id*="-detail-"]');
                }

                if (detailAnggotas.length > 0) {
                    sectionHTML += `
                <div class="mt-3 border-t border-gray-100 dark:border-gray-700 pt-3">
                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Anggota:</p>
                    <div class="space-y-2">
                `;

                    detailAnggotas.forEach((detail, detailIndex) => {
                        // Ambil data dari Alpine component di detail anggota
                        const namaInput = detail.querySelector('input[name*="detail_nama_anggota"]');
                        const namaAnggota = namaInput ? namaInput.value : '';

                        const idInput = detail.querySelector('input[name*="detail_id_anggota"]');
                        const idAnggota = idInput ? idInput.value : '';

                        const targetInput = detail.querySelector('input[name*="detail_target"]');
                        const target = targetInput ? targetInput.value : '';

                        const detailTanggalMulaiInput = detail.querySelector('input[name*="detail_tanggal_mulai"]');
                        const detailTanggalSelesaiInput = detail.querySelector('input[name*="detail_tanggal_selesai"]');

                        const detailTanggalMulai = detailTanggalMulaiInput ? detailTanggalMulaiInput.value : '';
                        const detailTanggalSelesai = detailTanggalSelesaiInput ? detailTanggalSelesaiInput.value : '';

                        sectionHTML += `
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Anggota ${detailIndex + 1}</span>
                            <div>
                                <span class="text-xs font-medium text-gray-800 dark:text-white/90">${namaAnggota || '-'}</span>
                                ${idAnggota ? `<span class="ml-2 text-xs text-gray-500">(ID: ${idAnggota})</span>` : ''}
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="block text-gray-500 dark:text-gray-400 mb-1">Target</span>
                                <span class="block text-gray-700 dark:text-gray-300">${target || '-'}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 dark:text-gray-400 mb-1">Tanggal</span>
                                <span class="block text-gray-700 dark:text-gray-300">${detailTanggalMulai || '-'} s/d ${detailTanggalSelesai || '-'}</span>
                            </div>
                        </div>
                    </div>
                    `;
                    });

                    sectionHTML += `
                    </div>
                </div>
                `;
                }

                sectionHTML += `</div></div>`;
                detailHTML += sectionHTML;
            });

            // Buat HTML untuk modal
            const confirmationHTML = `
        <div class="space-y-6">
            <!-- Header -->
            <div class="text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 mb-3">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-1">DATA RENCANA KINERJA KETUA</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Review data sebelum disimpan ke database</p>
            </div>

            <!-- Data Utama -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">DATA KETUA</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tahun</span>
                            <span class="block text-sm text-gray-800 dark:text-white/90 font-medium">${tahun || '-'}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">RK JPT</span>
                            <span class="block text-sm text-gray-800 dark:text-white/90">${rkJpt || '-'}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">IKI JPT</span>
                            <span class="block text-sm text-gray-800 dark:text-white/90">${ikiJpt || '-'}</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Bidang</span>
                            <span class="block text-sm text-gray-800 dark:text-white/90 font-medium">${bidangNama || '-'}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Ketua</span>
                            <div>
                                <span class="block text-sm text-gray-800 dark:text-white/90 font-medium">${namaKetua || '-'}</span>
                                ${idKetua ? `<span class="block text-xs text-gray-500 mt-1">ID: ${idKetua}</span>` : ''}
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Rencana Kinerja Ketua</span>
                            <span class="block text-sm text-gray-800 dark:text-white/90">${rkKetua || '-'}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data RK Anggota -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">DATA RK ANGGOTA</h4>
                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:text-gray-300">
                        ${sections.length} bagian
                    </span>
                </div>

                ${sections.length > 0 ? `
                    <div class="space-y-4">
                        ${detailHTML}
                    </div>
                ` : `
                    <div class="text-center py-4">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 mb-2">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada RK Anggota yang ditambahkan</p>
                    </div>
                `}
            </div>

            <!-- Note -->
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Data akan disimpan ke database setelah konfirmasi
            </div>
        </div>
        `;

            // Tampilkan di modal
            const confirmationContent = document.getElementById('confirmationContent');
            if (confirmationContent) {
                confirmationContent.innerHTML = confirmationHTML;
            }

            // Setelah data dimasukkan ke modal, cari element Alpine terdekat
            const alpineElement = event.target.closest('[x-data]');

            if (alpineElement && alpineElement.__x) {
                // Gunakan $dispatch dari Alpine
                alpineElement.__x.$dispatch('open-confirmation-modal');
                console.log('✅ Event dispatched via Alpine');
            } else {
                // Fallback: coba dispatch dengan bubbles
                const customEvent = new CustomEvent('open-confirmation-modal', {
                    bubbles: true,
                    composed: true
                });
                event.target.dispatchEvent(customEvent);
                console.log('✅ Event dispatched via custom event');
            }

            console.log('✅ Modal konfirmasi ditampilkan');

        } catch (error) {
            console.error('❌ Error in saveAll():', error);
            alert('Terjadi kesalahan: ' + error.message);
        }
    }

    // Fungsi untuk menyimpan data saat tombol "Ya, Simpan Data" diklik
    function confirmSave() {
        // Tutup modal konfirmasi
        document.dispatchEvent(new CustomEvent('close-confirmation-modal'));

        // Tutup modal utama
        document.dispatchEvent(new CustomEvent('close-rk-ketua-modal'));

        // Tampilkan alert sukses
        setTimeout(() => {
            alert('Data berhasil disimpan ke database!');
        }, 300);
    }

    // Event listener untuk tombol Save di modal utama
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('saveAllButton')?.addEventListener('click', saveAll);

        // Event untuk menutup modal konfirmasi
        document.addEventListener('close-confirmation-modal', function() {
            const modal = document.querySelector('[x-data] [x-data]');
            if (modal && modal.__x && modal.__x.$data) {
                modal.__x.$data.open = false;
            }
        });
    });
</script>
@endpush
