@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Bidang {{$title}}" />
<x-common.page-breadcrumb pageTitle="{{$bidang->detail_bidang}}" />

<!-- Bagian Tahun -->
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6 mb-6">
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

        <a href="{{ route('bidang.create') }}"
        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white
                shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Tambah Data
        </a>
    </div>

    <!-- Tabel Utama -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50">
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                        No.
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                        Nama Ketua
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                        Nama Kegiatan
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                        RK JPT
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                        IKI JPT
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">

                @foreach ($kegiatans as $kegiatan)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                            {{ $kegiatan->pegawai->nama_pegawai ?? 'Tidak ada' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                            {{ $kegiatan->nama_rk_kegiatan }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                            {{ $kegiatan->rk_jpt }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                            {{ $kegiatan->iki_jpt }}
                        </td>
                    </tr>
                @endforeach
                    {{-- <tr class="bg-gray-100 dark:bg-gray-800">
                        <td colspan="5" class="px-4 py-3">
                            <span class="text-sm font-semibold text-gray-800 dark:text-white">
                            Pengolahan SNLIK 2026
                            </span>
                        </td>
                    </tr> --}}

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
