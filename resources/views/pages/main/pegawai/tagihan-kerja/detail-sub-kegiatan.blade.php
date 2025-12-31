@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detail Kegiatan" />

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6 mb-6">
        <!-- Header Kegiatan -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
            {{$subKegiatan->nama_sub_kegiatan}}
            </h1>

            <!-- Informasi Kegiatan dalam Tabel Format -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50 w-32">
                    Sub Kegiatan
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                    {{$subKegiatan->nama_sub_kegiatan}}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                    Jenis Kegiatan
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                    {{$subKegiatan->jenis_kegiatan}}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                    Tanggal Mulai
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                    {{$subKegiatan->tanggal_mulai->format('d M Y')}}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                    Tanggal Berakhir
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                    {{$subKegiatan->tanggal_selesai->format('d M Y')}}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                    Satuan Target
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                    {{$subKegiatan->satuan_target}}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                    Status
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                    {{$subKegiatan->status}}
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Section Progres -->
        <div class="mb-8 max-w-full overflow-hidden">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Progres</h2>
            <div class="flex justify-start mb-6">
            <button @click="$dispatch('open-add-anggota-modal')"
                class="flex items-center gap-2 rounded-full border border-gray-300
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
                Tambah Anggota
            </button>
            </div>

            <!-- Tabel Detail Pengiriman - VERSI BERSIH -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div class="grid grid-cols-1">
                <div class="col-span-1 overflow-x-auto">
                <table class="max-w-[1400px] w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                        <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        No.
                        </th>
                        <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Nama
                        </th>
                        <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Target
                        </th>
                        <th colspan="3" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Pengiriman
                        </th>
                        <th colspan="4" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Penerimaan
                        </th>
                        <th rowspan="2" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Aksi
                        </th>
                    </tr>
                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Detail
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        RR (%)
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Ketepatan Waktu
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Detail
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        RR (%)
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Ketepatan Waktu
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Bukti Dukung
                        </th>

                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                        1
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                        Ifone Arma
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                        302
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                        <div>
                            <div class="flex items-center">
                            <span class="text-gray-500 dark:text-gray-400 mr-2">•</span>
                            <span>Thu, 09 Jan 2025</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 pl-4">
                            Jumlah: 302 (webentry)
                            </div>
                        </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                        100%
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                        <div class="flex justify-center">
                            @for($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            @endfor
                        </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                        <div>
                            <div class="flex items-center">
                            <span class="text-gray-500 dark:text-gray-400 mr-2">•</span>
                            <span>Fri, 10 Jan 2025</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 pl-4">
                            Jumlah: 302
                            </div>
                        </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                        100%
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                        <div class="flex justify-center">
                            @for($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            @endfor
                        </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                        https://drive.com
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                        <button
                            class="inline-flex items-center gap-2 rounded-full border border-gray-300
                    bg-white px-4 py-2 text-sm font-medium text-gray-700
                    shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
                    dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400
                    dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                            <svg class="fill-current" width="16" height="16" viewBox="0 0 18 18" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" />
                            </svg>
                            Jadikan CKP
                        </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
        </div>
    </div>


    <x-ui.modal
        x-data="{ open: false }"
        @open-add-anggota-modal.window="open = true"
        :isOpen="false"
        class="max-w-[700px]">
        <div
            class="relative flex h-[90vh] w-full max-w-[700px] flex-col overflow-hidden
                    rounded-3xl bg-white dark:bg-gray-900">

            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
            <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90">
                Tambah Anggota
            </h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Tambahkan data anggota baru
            </p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar">
            <form class="grid grid-cols-1 gap-y-5">
                <!-- input-input kamu, ga aku ubah -->
                <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Nama Anggota
                </label>
                <input type="text" placeholder="Ketik untuk cari nama anggota"
                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>

                <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Target
                </label>
                <input type="text" placeholder="Masukkan Target"
                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>
                <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Tanggal Mulai
                </label>
                <x-form.date-picker
                    id="tanggal_mulai"
                    name="date_pick"
                    placeholder="Date Picker"
                    defaultDate="{{ now()->format('Y-m-d') }}" />
                </div>
                <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    Tanggal Akhir
                </label>
                <x-form.date-picker
                    id="tanggal_akhir"
                    name="date_pick"
                    placeholder="Date Picker"
                    defaultDate="{{ now()->format('Y-m-d') }}" />
                </div>
            </form>
            </div>

            <!-- FOOTER (FIXED) -->
            <div
            class="shrink-0 border-t border-gray-200 px-6 py-3
                        dark:border-gray-800">
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button
                @click="open = false"
                type="button"
                class="flex w-full justify-center rounded-lg border
                                border-gray-300 bg-white px-4 py-2.5 text-sm
                                font-medium text-gray-700 hover:bg-gray-50
                                dark:border-gray-700 dark:bg-gray-800
                                dark:text-gray-400 dark:hover:bg-white/[0.03]
                                sm:w-auto">
                Close
                </button>

                <button
                @click="saveProfile"
                type="button"
                class="flex w-full justify-center rounded-lg
                                bg-brand-500 px-4 py-2.5 text-sm font-medium
                                text-white hover:bg-brand-600 sm:w-auto">
                Save Changes
                </button>
            </div>
            </div>
        </div>
    </x-ui.modal>

@endsection
