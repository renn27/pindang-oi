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
        {{--
        <a href="{{ route('bidang.create') }}"
        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white
                shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Tambah Data
        </a> --}}


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

        <button class="gap-2 rounded-full border border-gray-300
            bg-white px-4 py-3 text-sm font-medium text-gray-700
            shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400
            dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                @click="$dispatch('open-smart-modal', {
                    modalId: 'modal-bidang-kerja',
            })">
            Tambah Bidang Kerja
        </button>
    </div>

    <!-- Modal untuk Bidang Kerja -->
    <x-ui.smart-modal id="modal-bidang-kerja" class="max-w-xl"
            @open-smart-modal.window="

            if ($event.detail.modalId !== 'modal-bidang-kerja') return;

            mode    = $event.detail.mode ?? 'create'
            itemKey  = $event.detail.key ?? null
            formData = $event.detail.data ?? { nama_bidang: '', slug: '', detail_bidang: '' }">
        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
            <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="mode === 'create' ? 'Tambah Bidang Kerja' : 'Edit Bidang Kerja'"></h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="mode === 'create' ? 'Masukkan bidang kerja baru' : 'Edit bidang kerja yang sudah ada'"></p>
        </div>

        <!-- BODY -->
        <div class="flex-1 px-6 py-5">

            <form :action="mode === 'edit'
                    ? `{{ url('bidang-kerja') }}/${itemKey}`
                    : `{{ route('bidang.store') }}`"
                method="POST" class="grid grid-cols-1 gap-y-5">
                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                        Bidang Kerja
                    </label>
                    <input type="text" x-model="formData.nama_bidang" id="nama_bidang" name="nama_bidang"
                        placeholder="Masukkan Bidang Kerja"
                        class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>

                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                        Slug (Otomatis)
                    </label>
                    <input type="text" x-model="formData.slug" id="slug" name="slug"
                        class="md:w-3/4 dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                </div>

                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                        Detail Bidang Kerja
                    </label>
                    <input type="text" x-model="formData.detail_bidang" name="detail_bidang"
                        placeholder="Masukkan Detail Bidang Kerja"
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
    </x-ui.smart-modal>


    <!-- Tabel Utama -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                            No.
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Nama Bidang
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Detail/Kepanjangan Bidang
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    <!-- Baris Rencana Kerja -->
                    @foreach ($bidangs as $index => $bidang)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                {{ $bidang->nama_bidang }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                {{ $bidang->detail_bidang }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                <div class="relative inline-block group">
                                    <!-- Button Minimalis -->
                                    <button class="inline-flex items-center gap-1.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:border-green-400 hover:text-green-600 dark:hover:border-green-600 dark:hover:text-green-400 transition-all duration-200 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Aksi
                                    </button>

                                    <!-- Dropdown Simple -->
                                    <div class="absolute right-0 mt-1 w-36 origin-top-right rounded-md bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                                        <div class="py-1">
                                            <button class="w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"
                                                @click="$dispatch('open-smart-modal', {
                                                    modalId: 'modal-bidang-kerja',
                                                    mode: 'edit',
                                                    key: '{{ $bidang->slug }}',
                                                    data: {
                                                        nama_bidang: '{{ $bidang->nama_bidang }}',
                                                        slug: '{{ $bidang->slug }}',
                                                        detail_bidang: '{{ $bidang->detail_bidang }}'
                                                    }
                                                })">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>

                                            <form id="delete-rencana-{{ $bidang->id_bidang }}"
                                                action="{{ route('bidang.delete', $bidang->slug) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button type="button"
                                                    onclick="SwalHelper.confirmDelete(
                                                        'delete-rencana-{{ $bidang->id_bidang }}',
                                                        '{{ $bidang->nama_bidang }}',
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
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>



            {{-- <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                        No.
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Nama Bidang
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                        Detail Bidang
                    </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">

                    @foreach ($bidangs as $bidang)
                        <!-- Bidang Integrasi Pengolahan Dan Diseminasi Statistik -->
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            <td colspan="5" class="px-4 py-3">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">
                                    {{ $bidang->nama_bidang }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table> --}}
        </div>
    </div>

    {{-- Handle Slug Otomatis --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const namaInput = document.getElementById('nama_bidang');
        const slugInput = document.getElementById('slug');

        namaInput.addEventListener('input', function () {
            if (slugInput.dataset.manual === "true") return;

            slugInput.value = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        });

        slugInput.addEventListener('input', function () {
            // Kalau slug dikosongkan → aktifkan auto lagi
            if (this.value === '' || !isempty(this.value)) {
                this.dataset.manual = "false";
            } else {
                this.dataset.manual = "true";
            }
        });
    });
    </script>

@endsection
