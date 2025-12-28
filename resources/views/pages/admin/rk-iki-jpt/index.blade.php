@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{$title}}" />

    <!-- Bagian Tahun -->
    <div class="flex flex-row justify-between items-center rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <!-- Label -->
            <div class="flex items-center h-10">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-400 whitespace-nowrap">
                    Tampilkan Data Tahun
                </label>
            </div>

            <!-- Dropdown Tahun -->
            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent w-full sm:w-auto">
                <select
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-10 w-full sm:w-36 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-4 pr-10 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                    <option value="2025" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                        2025
                    </option>
                    <option value="2024" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                        2024
                    </option>
                    <option value="2023" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                        2023
                    </option>
                    <option value="2022" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
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
            <button type="button"
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
                    modalId: 'modal-rencana-jpt',
                    mode: 'create'
            })">
            Tambah RK JPT
        </button>
    </div>

    <!-- Modal untuk Rencana JPT -->
    <x-ui.smart-modal id="modal-rencana-jpt" class="max-w-xl">
        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
            <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90" x-text="mode === 'create' ? 'Tambah Rencana Kerja JPT' : 'Edit Rencana Kerja JPT'"></h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="mode === 'create' ? 'Masukkan rencana kerja JPT baru' : 'Edit rencana kerja JPT'"></p>
        </div>

        <!-- BODY -->
        <div class="flex-1 px-6 py-5"
            @open-smart-modal.window="
            if ($event.detail.modalId !== 'modal-rencana-jpt') return;
            mode    = $event.detail.mode ?? 'create'
            itemId  = $event.detail.id ?? null
            formData = $event.detail.data ?? { tahun: '', nama_rencana_jpt: '' }">
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

                <!-- OPSIONAL: Tambah IKI saat CREATE -->
                <template x-if="mode === 'create'">
                    <div class="mt-2" x-data="{ addIki: false, ikis: [''] }">

                        <!-- Toggle -->
                        <div class="flex items-center gap-2 mb-3">
                            <input type="checkbox" x-model="addIki"
                                class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                Tambah IKI JPT sekaligus
                            </span>
                        </div>

                        <!-- List IKI -->
                        <template x-if="addIki">
                            <div class="space-y-3 border rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                                <template x-for="(iki, index) in ikis" :key="index">
                                    <div class="flex gap-2 items-center">
                                        <input type="text"
                                                :name="`ikis[${index}]`"
                                                x-model="ikis[index]"
                                                placeholder="Nama Indikator Kinerja Individu (IKI)"
                                                class="flex-1 h-10 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                                        <!-- Remove -->
                                        <button type="button"
                                                @click="ikis.splice(index,1)"
                                                x-show="ikis.length > 1"
                                                class="text-red-500 hover:text-red-700 text-sm">
                                            ✕
                                        </button>
                                    </div>
                                </template>

                                <!-- Add IKI -->
                                <button type="button"
                                        @click="ikis.push('')"
                                        class="text-sm text-brand-600 hover:text-brand-800">
                                    + Tambah IKI
                                </button>
                            </div>
                        </template>
                    </div>
                </template>


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

    <!-- Modal untuk Indikator JPT -->
    <x-ui.smart-modal id="modal-indikator-jpt" class="max-w-xl">
        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-800">
            <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90"
                x-text="mode === 'create' ? 'Tambah Indikator JPT' : 'Edit Indikator JPT'">
            </h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                x-text="mode === 'create'
                    ? 'Tambahkan indikator untuk rencana kerja'
                    : 'Perbarui indikator kinerja'">
            </p>
        </div>

        <!-- BODY -->
        <div class="flex-1 px-6 py-5"
            x-data="{
                mode: 'create',
                rkId: null,
                rkNama: '',
                indikatorId: null,
                formData: {
                    nama_indikator_jpt: ''
                }
            }"
            @open-smart-modal.window="
                if ($event.detail.modalId !== 'modal-indikator-jpt') return;

                mode        = $event.detail.mode ?? 'create'
                rkId        = $event.detail.rk_id
                rkNama      = $event.detail.rk_nama
                indikatorId = $event.detail.id ?? null
                formData    = $event.detail.data ?? { nama_indikator_jpt: '' }">
            <form
                method="POST"
                :action="mode === 'edit'
                ? `{{ url('rencana-indikator-jpt') }}/${rkId}/indikator/${indikatorId}`
                : `{{ url('rencana-indikator-jpt') }}/${rkId}/indikator`"
                class="grid grid-cols-1 gap-y-5">

                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- RK (DISABLED) -->
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                        Rencana Kerja JPT
                    </label>
                    <input
                        type="text"
                        x-model="rkNama"
                        disabled
                        class="md:w-3/4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                            dark:border-gray-700 dark:bg-gray-800 dark:text-white/70 cursor-not-allowed"
                    >
                </div>

                <!-- INPUT IKI -->
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-400 md:w-1/4">
                        Indikator JPT
                    </label>
                    <input
                        type="text"
                        name="nama_indikator_jpt"
                        x-model="formData.nama_indikator_jpt"
                        placeholder="Masukkan indikator kinerja"
                        required
                        class="md:w-3/4 h-11 rounded-lg border border-gray-300 px-4 text-sm
                            focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    >
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

    </x-ui.smart-modal>

    <!-- Tabel Utama -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50">
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                        No.
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Tahun
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Nama Rencana Kerja
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                <!-- Baris Rencana Kerja -->
                @forelse ($rencanaJpts as $index => $rencanaJpt)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $rencanaJpt->tahun }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $rencanaJpt->nama_rencana_jpt }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            <button type="button"
                                class="expand-indicator-btn inline-flex items-center gap-1 rounded-lg bg-gray-100 dark:bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
                                data-target="indicator-{{ $rencanaJpt->id }}">
                                <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                Tampilkan Indikator
                            </button>
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
                                                modalId: 'modal-rencana-jpt',
                                                mode: 'edit',
                                                id: {{ $rencanaJpt->id }},
                                                data: {
                                                    tahun: '{{ $rencanaJpt->tahun }}',
                                                    nama_rencana_jpt: '{{ $rencanaJpt->nama_rencana_jpt }}'
                                                }
                                            })">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>

                                        <form id="delete-rencana-{{ $rencanaJpt->id }}"
                                            action="{{ route('rencana-indikator-jpt.rencana.delete', $rencanaJpt->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="button"
                                                onclick="SwalHelper.confirmDelete(
                                                    'delete-rencana-{{ $rencanaJpt->id }}',
                                                    '{{ $rencanaJpt->nama_rencana_jpt }}'
                                                )"
                                                class="w-full text-left px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>


                                        <button class="w-full text-left px-3 py-2 text-sm text-green-600 dark:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"
                                            @click="$dispatch('open-smart-modal', {
                                            modalId: 'modal-indikator-jpt',
                                            rk_id: '{{ $rencanaJpt->id }}',
                                            rk_nama: '{{ $rencanaJpt->nama_rencana_jpt }}'
                                        })" >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Tambah IKI
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Baris Indikator Kerja (Hidden) -->
                    <tr id="indicator-{{ $rencanaJpt->id }}" class="hidden bg-gray-50 dark:bg-gray-800/30">
                        <td colspan="4" class="px-4 py-4">
                            <div class="ml-8">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">IKI JPT</h4>

                                @if($rencanaJpt->indikatorjpts && $rencanaJpt->indikatorjpts->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead>
                                                <tr class="bg-gray-100 dark:bg-gray-700/50">
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">No.</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Nama Indikator</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($rencanaJpt->indikatorjpts as $indikatorIndex => $indikator)
                                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700/30">
                                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-400">
                                                            {{ $indikatorIndex + 1 }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-400">
                                                            {{ $indikator->nama_indikator_jpt }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm">
                                                            <button
                                                                class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 mr-3"
                                                                @click="$dispatch('open-smart-modal', {
                                                                    modalId: 'modal-indikator-jpt',
                                                                    mode: 'edit',
                                                                    id: {{ $indikator->id }},
                                                                    rk_id: '{{ $rencanaJpt->id }}',
                                                                    rk_nama: '{{ $rencanaJpt->nama_rencana_jpt }}',
                                                                    data: {
                                                                        nama_indikator_jpt: '{{ $indikator->nama_indikator_jpt }}'
                                                                    }
                                                                })">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                                Edit
                                                            </button>

                                                            <form id="delete-indikator-{{ $indikator->id }}"
                                                                action="{{ route('rencana-indikator-jpt.indikator.delete', [
                                                                'rencanaJpt' => $rencanaJpt->id,
                                                                'indikatorJpt' => $indikator->id]) }}"
                                                                method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="button"
                                                                    onclick="SwalHelper.confirmDelete(
                                                                        'delete-indikator-{{ $indikator->id }}',
                                                                        '{{ $indikator->nama_indikator_jpt }}'
                                                                    )"
                                                                    class="inline-flex items-center text-red-600 dark:text-red-400 hover:text-red-800">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                    </svg>
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada indikator kerja.</p>
                                        <a href="#"
                                            class="inline-flex items-center mt-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Tambah Indikator Pertama
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada data rencana kerja.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>



    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle dropdown indikator
        document.querySelectorAll('.expand-indicator-btn').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const targetRow = document.getElementById(targetId);
                const icon = this.querySelector('svg');

                // Toggle visibility
                targetRow.classList.toggle('hidden');

                // Rotate icon
                if (targetRow.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(0deg)';
                    this.innerHTML = `
                        <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        Tampilkan Indikator
                    `;
                } else {
                    icon.style.transform = 'rotate(180deg)';
                    this.innerHTML = `
                        <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="transform: rotate(180deg);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        Sembunyikan Indikator
                    `;
                }
            });
        });
    });
    </script>
    @endpush

@endsection
