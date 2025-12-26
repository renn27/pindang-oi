@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Rencana Kinerja - Ketua" />

<div class="space-y-6">
    <x-common.component-card title="Rencana Kinerja">
        <!-- Elements -->
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Tahun
            </label>
            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
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
                    class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </div>

            <div class="class=" flex flex-col gap-2 md:flex-row md:items-center"">

            </div>
            <label class="mb-1.5 mt-2 block text-sm font-medium text-gray-700 dark:text-gray-400">
                RK JPT/Ka. BPS Kab/Kota
            </label>
            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
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
                    class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </div>

            <label class="mb-1.5 mt-2 block text-sm font-medium text-gray-700 dark:text-gray-400">
                IKI JPT/Ka. BPS Kab/Kota
            </label>
            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                <select
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
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
                    class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </div>
        </div>

        <div class="flex justify-end">
            <button @click="$dispatch('open-rk-ketua-modal')" class="flex items-center gap-2 rounded-full border border-gray-300
                bg-white px-4 py-3 text-sm font-medium text-gray-700
                shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
                dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400
                dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                <!-- icon -->
                <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                        fill="" />
                </svg>
                Tambah Rencana Kinerja
            </button>
        </div>
    </x-common.component-card>

    {{-- <x-ui.smart-modal id="modal-rencana-jpt" class="max-w-xl">
        <div class="relative flex h-[90vh] w-full max-w-[700px] flex-col overflow-hidden
                rounded-3xl bg-white dark:bg-gray-900">
            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="mode === 'create' ? 'Tambah Rencana Kerja Ketua' : 'Edit Rencana Kerja Ketua'"></h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="mode === 'create' ? 'Masukkan rencana kerja ketua baru' : 'Edit rencana kerja ketua'"></p>
            </div>

            <!-- BODY -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
                <form x-bind:action="mode === 'edit'
                        ? '{{ url('rencana-kerja/') }}/' + itemId
                        : '{{ route('rencana.store') }}'"
                    method="POST" class="grid grid-cols-1 gap-y-5">
                    @csrf
                    <template x-if="mode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Tahun
                        </label>
                        <div class="md:w-3/4 h-11 flex items-center rounded-lg border border-gray-300 bg-gray-50 px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            2025
                        </div>
                    </div>

                    <!-- sebelahan -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Rencana Kinerja JPT
                        </label>
                        <textarea readonly oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'" class="w-full md:w-3/4 resize-none overflow-hidden rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">Terwujudnya penyedia data dan insight statistik kesejahteraan rakyat yang berkualitas</textarea>
                    </div>

                    <!-- sebelahan -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            IKI JPT
                        </label>
                        <textarea
                            readonly
                            oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"
                            class="w-full md:w-3/4 resize-none overflow-hidden rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">Persentase Publikasi/Laporan statistik kesejahteraan rakyat yang berkualitas
                        </textarea>
                    </div>

                    <!-- sebelahan -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Unit Kerja
                        </label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent w-full md:w-3/4">
                            <select
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                    tim 1
                                </option>
                                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                    tim 2
                                </option>
                                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                    tim 3
                                </option>
                                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                    tim 3
                                </option>
                            </select>
                            <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- sebelahan -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Nama Ketua
                        </label>
                        <input readonly ="text" value="Ifone Arma"
                            class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />

                    </div>

                    <!-- sebelahan -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            *Rencana Kinerja Ketua
                        </label>
                        <textarea placeholder="Enter a description..." type="text" rows="6" class="w-full md:w-3/4 dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
                    </div>

                    <!-- sebelahan (CONTOH) -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Tim Kerja
                        </label>
                        <input type="text" placeholder="Tulis nama tim"
                            class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>


                </form>

                <form x-bind:action="mode === 'edit'
                        ? '{{ url('rencana-indikator-jpt/rencana') }}/' + itemId
                        : '{{ route('rencana-indikator-jpt.rencana.store') }}'"
                    method="POST" class="grid grid-cols-1 gap-y-5">
                    @csrf
                    <template x-if="mode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>


                    <!-- Field 1: Tahun -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Tahun
                        </label>
                        <div class="md:w-3/4">
                            <select name="tahun" x-model="formData.tahun"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    required>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                            </select>
                        </div>
                    </div>

                    <!-- Field 2: Nama Rencana -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Rencana Kerja JPT
                        </label>
                        <input type="text" x-model="formData.nama_rencana_jpt" name="nama_rencana_jpt"
                            placeholder="Masukkan rencana kerja JPT"
                            class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
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
                                <span x-text="mode === 'create' ? 'Simpan Data' : 'Update Data'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </x-ui.smart-modal> --}}

    <x-ui.modal x-data="{ open: false }" @open-rk-ketua-modal.window="open = true" :isOpen="false"
        class="max-w-[700px]">
        <div class="relative flex h-[90vh] w-full max-w-[700px] flex-col overflow-hidden
                rounded-3xl bg-white dark:bg-gray-900">

            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90" >Tambah Rencana Kerja Ketua</h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Masukkan rencana kerja ketua baru</p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
                <form class="grid grid-cols-1 gap-y-5">
                    @csrf
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Tahun
                        </label>
                        <div class="md:w-3/4 h-11 flex items-center rounded-lg border border-gray-300 bg-gray-50 px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            2025
                        </div>
                    </div>

                    <!-- sebelahan -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Rencana Kinerja JPT
                        </label>
                        <textarea readonly oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'" class="w-full md:w-3/4 resize-none overflow-hidden rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">Terwujudnya penyedia data dan insight statistik kesejahteraan rakyat yang berkualitas</textarea>
                    </div>

                    <!-- sebelahan -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            IKI JPT
                        </label>
                        <textarea
                            readonly
                            oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"
                            class="w-full md:w-3/4 resize-none overflow-hidden rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">Persentase Publikasi/Laporan statistik kesejahteraan rakyat yang berkualitas
                        </textarea>
                    </div>

                    <!-- sebelahan -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Unit Kerja
                        </label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent w-full md:w-3/4">
                            <select
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                    tim 1
                                </option>
                                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                    tim 2
                                </option>
                                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                    tim 3
                                </option>
                                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                    tim 3
                                </option>
                            </select>
                            <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <!-- sebelahan -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Nama Ketua
                        </label>
                        <input readonly ="text" value="Ifone Arma"
                            class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />

                    </div>

                    <!-- sebelahan -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            *Rencana Kinerja Ketua
                        </label>
                        <textarea placeholder="Enter a description..." type="text" rows="6" class="w-full md:w-3/4 dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
                    </div>

                    <!-- sebelahan (CONTOH) -->
                    <div class="flex flex-col gap-2 md:flex-row md:items-center">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                            Tim Kerja
                        </label>
                        <input type="text" placeholder="Tulis nama tim"
                            class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                    </div>

                </form>
            </div>

            <!-- FOOTER (FIXED) -->
            <div class="shrink-0 border-t border-gray-200 px-6 py-3
                       dark:border-gray-800">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button @click="open = false" type="button" class="flex w-full justify-center rounded-lg border
                               border-gray-300 bg-white px-4 py-2.5 text-sm
                               font-medium text-gray-700 hover:bg-gray-50
                               dark:border-gray-700 dark:bg-gray-800
                               dark:text-gray-400 dark:hover:bg-white/[0.03]
                               sm:w-auto">
                        Close
                    </button>

                    <button @click="saveProfile" type="button" class="flex w-full justify-center rounded-lg
                               bg-brand-500 px-4 py-2.5 text-sm font-medium
                               text-white hover:bg-brand-600 sm:w-auto">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </x-ui.modal>


    <!-- Tabel Utama -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                            No.
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-36">
                            Nama Bidang
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                            Nama Ketua
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                            Nama Kegiatan
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-40">
                            IKI JPT
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    <!-- Bidang Integrasi Pengolahan Dan Diseminasi Statistik -->
                    @foreach ($kegiatans as $kegiatan)
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                                {{ $kegiatan->bidang->nama_bidang }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                                {{ $kegiatan->pegawai->nama_pegawai ?? '-'}}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                                {{ $kegiatan->nama_rk_kegiatan }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                                {{ $kegiatan->iki_jpt }}
                            </td>
                        </tr>
                    @endforeach
                        <!-- Sub-row 1 -->
                        {{-- <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                                1
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                                Penyiapan Peta
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                                2450
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                                950
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                                820
                            </td>
                        </tr> --}}

                </tbody>
        </table>
    </div>
</div>

@endsection
