@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{$title}}" />

    <!-- Bagian Tahun -->
    <div class="flex flex-row items-center justify-between rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <!-- Label -->
            <div class="flex items-center h-10">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-400 whitespace-nowrap">
                Tampilkan Data Tahun
            </label>
            </div>

            <!-- Dropdown -->
            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent w-full sm:w-auto">
            <select
                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-10 w-full sm:w-36 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-4 pr-10 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                2025
                </option>
                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                2024
                </option>
                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                2023
                </option>
                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                2022
                </option>
            </select>
            <span
                class="pointer-events-none absolute top-1/2 right-3.5 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                <svg class="stroke-current" width="16" height="16" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            </div>

            <!-- Tombol -->
            <button
            type="button"
            class="flex justify-center items-center rounded-lg bg-brand-500 px-5 py-2 text-sm font-medium text-white hover:bg-brand-600 w-full sm:w-auto h-10 whitespace-nowrap">
            Tampilkan
            </button>
        </div>

        <div class="flex justify-start mt-5">
            <button class="flex items-center gap-2 rounded-full border border-gray-300
                    bg-white px-4 py-3 text-sm font-medium text-gray-700
                    shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
                    dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400
                    dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                    @click="$dispatch('open-smart-modal', {
                        modalId: 'modal-kegiatan-rk-ketua',
            })">
                <!-- icon -->
                <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                    fill="" />
                </svg>
                Tambah Kegiatan
            </button>
        </div>
    </div>

    <x-ui.smart-modal id="modal-kegiatan-rk-ketua" class="max-w-xl"
        @open-smart-modal.window="
            if ($event.detail.modalId !== 'modal-kegiatan-rk-ketua') return;

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

        <div
            class="relative flex h-[90vh] w-full max-w-[700px] flex-col overflow-hidden
                    rounded-3xl bg-white dark:bg-gray-900">

            <!-- HEADER -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="mode === 'create' ? 'Tambah Kegiatan/RK Ketua' : 'Edit Kegiatan/RK Ketua'"></h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="mode === 'create' ? 'Masukkan kegiatan yang baru' : 'Edit kegiatan yang sudah ada'"></p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
                <form :action="mode === 'edit' ? `{{ url('kegiatan') }}/${itemKey}` : `{{ route('kegiatan.store', $bidang->slug) }}`"
                    method="POST"
                    class="grid grid-cols-1 gap-y-5">
                    @csrf
                    <template x-if="mode === 'edit'">
                        @method('PUT')
                    </template>

                    <!-- Nama Bidang (readonly tampilan) -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Bidang
                        </label>

                        <input
                            type="text"
                            value="{{ $bidang->nama_bidang }}"
                            disabled
                            class="w-full h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                dark:border-gray-700 dark:bg-gray-800 dark:text-white/70 cursor-not-allowed">
                    </div>

                    <!-- ID Bidang (yang benar-benar dikirim ke backend) -->
                    <input type="hidden" name="id_bidang" value="{{ $bidang->id_bidang }}">

                    {{-- Nama Ketua / Penanggung Jawab --}}
                    <div x-data="{
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

                        highlightNext() { if(this.highlightedIndex < this.filtered().length - 1) this.highlightedIndex++; },
                        highlightPrev() { if(this.highlightedIndex > 0) this.highlightedIndex--; },
                        selectHighlighted() { if(this.highlightedIndex >= 0) this.selectPegawai(this.filtered()[this.highlightedIndex]); }
                    }">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Nama Ketua
                        </label>
                        <!-- Input search -->
                        <input type="text" x-model="search" @focus="open = !!search" @input="open = search.length > 0; selectedId = ''"
                        @keydown.arrow-down.prevent="highlightedIndex++"
                        @keydown.arrow-up.prevent="highlightedIndex--"
                        @keydown.enter.prevent="if(highlightedIndex>=0){ search = pegawais[highlightedIndex].nama_pegawai; selectedId = pegawais[highlightedIndex].id_pegawai; open=false; }"
                        placeholder="Ketik untuk cari nama" class="h-11 w-full rounded-lg border px-4 py-2 text-sm">

                        <!-- Hidden input -->
                        <input type="hidden" name="id_penanggung_jawab" :value="selectedId" required>

                        <!-- Dropdown -->
                        <div x-show="open" x-transition class="absolute z-50 mt-1 w-full rounded-lg border bg-white max-h-60 overflow-y-auto">
                            <template x-for="(pegawai, index) in pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase()))" :key="pegawai.id_pegawai">
                                <div @click="search = pegawai.nama_pegawai; selectedId = pegawai.id_pegawai; open = false"
                                    :class="{'bg-blue-100': highlightedIndex===index, 'hover:bg-gray-100': highlightedIndex!==index}" class="cursor-pointer px-4 py-2 text-sm" x-text="pegawai.nama_pegawai"></div>
                            </template>
                            <template x-if="pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase())).length === 0">
                                <div class="px-4 py-2 text-sm text-gray-500">Data tidak ditemukan</div>
                            </template>
                        </div>
                    </div>

                    {{-- Tahun Kegiatan --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Tahun Kegiatan
                        </label>
                        <input type="text" x-model="formData.tahun_kegiatan" name="tahun_kegiatan"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>

                    {{-- Rencana JPT --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Rencana JPT
                        </label>
                        <select
                            id="rk_jpt"
                            name="rk_jpt"
                            x-model="formData.rk_jpt"
                            @change="loadIkiByRk(formData.rk_jpt, formData)"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                            <option value="">-- Pilih RK JPT --</option>
                            @foreach ($rkJpts as $rk)
                                <option value="{{ $rk->id }}">
                                    {{ $rk->nama_rencana_jpt }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Indikator JPT --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Indikator JPT
                        </label>

                        <select
                            id="iki_jpt"
                            name="iki_jpt"
                            x-model="formData.iki_jpt"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">

                            <!-- OPTION DINAMIS -->
                            <option value=""
                                x-text="formData.rk_jpt
                                    ? '-- Pilih IKI JPT --'
                                    : '-- Harap pilih RK JPT dulu --'">
                            </option>

                            <template x-for="iki in formData.ikiOptions" :key="iki.id">
                                <option
                                    :value="iki.id"
                                    x-text="iki.nama_indikator_jpt">
                                </option>
                            </template>
                        </select>
                    </div>


                    {{-- Indikator JPT --}}
                    {{-- <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Indikator JPT
                        </label>
                        <select
                            id="iki_jpt"
                            name="iki_jpt"
                            x-model="formData.iki_jpt"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                            <option value="">-- Harap pilih RK dulu --</option>
                            <template x-for="iki in formData.ikiOptions" :key="iki.id">
                                <option :value="iki.id" x-text="iki.nama_indikator_jpt" :selected="formData.iki_jpt == iki.id"></option>
                            </template>
                        </select>
                    </div> --}}

                    {{-- Nama Kegiatan --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Nama Kegiatan
                        </label>
                        <input type="text" x-model="formData.nama_rk_kegiatan" name="nama_rk_kegiatan" placeholder="Contoh : SNLIK2026"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>

                    <!-- FOOTER -->
                    <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
                        <div class="flex justify-end gap-3">
                            <button type="button"
                                    @click="open = false"
                                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium
                                        text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                Batal
                            </button>

                            <button type="submit"
                                class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                                <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </x-ui.smart-modal>

    <x-ui.smart-modal id="modal-sub-kegiatan-rk-anggota" class="max-w-xl"
        @open-smart-modal.window="
            if ($event.detail.modalId !== 'modal-sub-kegiatan-rk-anggota') return;

            mode = $event.detail.mode ?? 'create';
            itemKey = $event.detail.key ?? null;
            // Ambil data dari dispatch
            formData = $event.detail.data ?? {
                id_kegiatan: '',
                nama_rk_kegiatan: '',
                nama_sub_kegiatan: '',
                jenis_kegiatan: '',
                satuan_target: '',
                tanggal_mulai: '',
                tanggal_selesai: '',
                status: ''
            }">
        <div
            class="relative flex h-[90vh] w-full max-w-[700px] flex-col overflow-hidden
                    rounded-3xl bg-white dark:bg-gray-900">

            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="mode === 'create' ? 'Tambah Sub Kegiatan/RK Anggota' : 'Edit Sub Kegiatan/RK Anggota'"></h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="mode === 'create' ? 'Masukkan sub kegiatan yang baru' : 'Edit sub kegiatan yang sudah ada'"></p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
                <form :action="mode === 'edit'
                        ? `/kegiatan/${formData.id_kegiatan}/sub-kegiatan/${itemKey}`
                        : `/kegiatan/${formData.id_kegiatan}/sub-kegiatan`"
                    method="POST"
                    class="grid grid-cols-1 gap-y-5">

                    @csrf
                    <template x-if="mode === 'edit'">
                        @method('PUT')
                    </template>
                    <!-- Nama Kegiatan (readonly tampilan) -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Kegiatan
                        </label>

                        <input
                            type="text"
                            :value="formData.nama_rk_kegiatan"
                            disabled
                            class="w-full h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                dark:border-gray-700 dark:bg-gray-800 dark:text-white/70 cursor-not-allowed">
                    </div>

                    <!-- ID Kegiatan (yang benar-benar dikirim ke backend) -->
                    {{-- <input type="hidden" name="id_kegiatan" :value="formData.id_kegiatan"> --}}

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Nama Sub Kegiatan
                        </label>
                        <input type="text" name="nama_sub_kegiatan"
                        x-model="formData.nama_sub_kegiatan"
                        placeholder="Contoh : Penyiapan Peta"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Jenis Kegiatan
                        </label>
                        <input type="text" x-model="formData.jenis_kegiatan" name="jenis_kegiatan"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Satuan Target
                        </label>
                        <input type="text" x-model="formData.satuan_target" name="satuan_target" placeholder="Misalnya : Dokumen, Kegiatan, dll"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Tanggal Mulai
                        </label>
                        <x-form.date-picker
                            id="tanggal_mulai"
                            x-model="tanggal_mulai"
                            name="tanggal_mulai"
                            placeholder="Pilih Tanggal"
                            defaultDate="{{ now()->format('Y-m-d') }}" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Tanggal Selesai
                        </label>
                        <x-form.date-picker
                            id="tanggal_selesai"
                            name="tanggal_selesai"
                            x-model="tanggal_selesai"
                            placeholder="Pilih Tanggal"
                            defaultDate="{{ now()->format('Y-m-d') }}" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Status
                        </label>
                        <select
                            name="status"
                            x-model="formData.status"
                            class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
                            <option value="">-- Pilih Status --</option>
                            <option value="Belum Mulai" :selected="formData.status === 'Belum Mulai'">Belum Mulai</option>
                            <option value="Berjalan" :selected="formData.status === 'Berjalan'">Berjalan</option>
                            <option value="Selesai" :selected="formData.status === 'Selesai'">Selesai</option>
                        </select>
                    </div>

                    <!-- FOOTER -->
                    <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
                        <div class="flex justify-end gap-3">
                            <button type="button"
                                    @click="open = false"
                                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium
                                        text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                Batal
                            </button>

                            <button type="submit"
                                class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                                <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </x-ui.smart-modal>

    <!-- Container untuk Card Kegiatan -->
    <div class="space-y-6">
        @foreach ($kegiatans as $kegiatan)
            <!-- CARD PER KEGIATAN -->
            <div class="rounded-2xl border border-gray-200 bg-white
                        dark:border-gray-800 dark:bg-white/[0.03]
                        overflow-hidden">

                <!-- HEADER CARD -->
                <div class="bg-white dark:bg-white/[0.03]
                            px-6 py-4 border-b
                            border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                                {{ $kegiatan->nama_rk_kegiatan }}
                            </h3>

                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Ketua: {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }} •
                                Tahun: {{ $kegiatan->tahun_kegiatan }}
                            </p>

                            {{-- <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                RK JPT: {{ $kegiatan->rencanaJpt->nama_rencana_jpt ?? '-' }}
                            </p>

                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                IKI JPT: {{ $kegiatan->indikatorJpt->nama_indikator_jpt ?? '-' }}
                            </p> --}}
                        </div>

                        <!-- ACTION BUTTON -->
                        <div class="flex flex-wrap gap-2">
                            {{-- Edit --}}
                            <button
                                @click="$dispatch('open-smart-modal', {
                                    modalId: 'modal-kegiatan-rk-ketua',
                                    mode: 'edit',
                                    key: '{{ $kegiatan->id }}',
                                    data: {
                                        id_kegiatan: '{{ $kegiatan->id_kegiatan }}',
                                        nama_rk_kegiatan: @js($kegiatan->nama_rk_kegiatan),
                                        tahun_kegiatan: '{{ $kegiatan->tahun_kegiatan }}',
                                        id_penanggung_jawab: '{{ $kegiatan->id_penanggung_jawab }}',
                                        nama_penanggung_jawab: @js($kegiatan->penanggungJawab->nama_pegawai),
                                        rk_jpt: '{{ $kegiatan->rk_jpt }}',
                                        iki_jpt: '{{ $kegiatan->iki_jpt }}'
                                    }
                                })"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg> Edit
                            </button>

                            {{-- Hapus --}}
                            <button class="border border-red-300 text-red-700 px-4 py-2 rounded-lg">
                                Hapus
                            </button>

                            {{-- Tambah Sub --}}
                            <button
                                @click="$dispatch('open-smart-modal', {
                                    modalId: 'modal-sub-kegiatan-rk-anggota',
                                    data: {
                                        id_kegiatan: '{{ $kegiatan->id_kegiatan }}',
                                        nama_rk_kegiatan: '{{ $kegiatan->nama_rk_kegiatan }}'
                                    }
                                })"
                                class="border border-gray-300 px-4 py-2 rounded-lg">
                                + Sub Kegiatan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TABLE WRAPPER -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                No.
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nama Sub Kegiatan
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                                Satuan Target
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                                Jenis
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                                Jumlah Anggota
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-40">
                                Tanggal Mulai
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-40">
                                Tanggal Selesai
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">
                                Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse ($kegiatan->subKegiatans as $index => $subKegiatan)
                                <!-- Sub-row 1 -->
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                                    {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-400">
                                        <a href="{{ route('sub.kegiatan.show', [
                                                'kegiatan' => $kegiatan->id_kegiatan,
                                                'subKegiatan' => $subKegiatan->id_sub_kegiatan
                                            ]) }}"
                                        title="Lihat detail sub kegiatan"
                                        class="font-medium text-blue-600 hover:text-blue-800 hover:underline
                                                dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ $subKegiatan->nama_sub_kegiatan }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                                    {{ $subKegiatan->satuan_target }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                                    {{ $subKegiatan->jenis_kegiatan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                                    {{ $subKegiatan->penugasans->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                                    {{ $subKegiatan->tanggal_mulai->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                                    {{ $subKegiatan->tanggal_selesai->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400">
                                        <div class="flex gap-2">
                                            {{-- Edit --}}
                                            <button
                                                @click="$dispatch('open-smart-modal', {
                                                    modalId: 'modal-sub-kegiatan-rk-anggota',
                                                    mode: 'edit',
                                                    key: '{{ $subKegiatan->id_sub_kegiatan }}',
                                                    data: {
                                                        id_kegiatan: @js($kegiatan->id_kegiatan),
                                                        nama_rk_kegiatan: @js($kegiatan->nama_rk_kegiatan),
                                                        id_sub_kegiatan: @js($subKegiatan->id_sub_kegiatan),
                                                        nama_sub_kegiatan: @js($subKegiatan->nama_sub_kegiatan),
                                                        jenis_kegiatan: @js($subKegiatan->jenis_kegiatan),
                                                        satuan_target: @js($subKegiatan->satuan_target),
                                                        tanggal_mulai: @js($subKegiatan->tanggal_mulai),
                                                        tanggal_selesai: @js($subKegiatan->tanggal_selesai),
                                                        status: @js($subKegiatan->status),
                                                    }
                                                })"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg> Edit
                                            </button>

                                            <form id="delete-sub-kegiatan-{{ $subKegiatan->id_sub_kegiatan }}"
                                                action="{{ route('sub.kegiatan.delete', [
                                                    'kegiatan' => $kegiatan->id_kegiatan,
                                                    'subKegiatan' => $subKegiatan->id_sub_kegiatan
                                                ]) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button type="button"
                                                    onclick="SwalHelper.confirmDelete(
                                                        'delete-sub-kegiatan-{{ $subKegiatan->id_sub_kegiatan }}',
                                                        '{{ $subKegiatan->nama_sub_kegiatan }}'
                                                    )"
                                                    class="w-full text-left px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada Sub Kegiatan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        @endforeach

    </div>


    <script>
        function loadIkiByRk(rkId, formData) {
            const selectedIki = formData.iki_jpt;

            if (!rkId) {
                formData.ikiOptions = [];
                formData.iki_jpt = '';
                return;
            }

            fetch(`/rencana-indikator-jpt/${rkId}/indikator`)
                .then(res => res.json())
                .then(data => {
                    formData.ikiOptions = data;

                    // 🔥 pastikan value dipilih SETELAH option ada
                    if (selectedIki) {
                        formData.iki_jpt = selectedIki;
                    }
                });
        }


    </script>
@endsection

