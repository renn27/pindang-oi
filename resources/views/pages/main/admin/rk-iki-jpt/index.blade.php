@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    @php
        $years = $rencanaJpts->pluck('tahun')->unique()->sort()->values();
        $currentYear = date('Y');
        if (!$years->contains($currentYear)) {
            $years->push($currentYear);
            $years = $years->sort()->values();
        }
    @endphp
    <!-- Bagian Tahun -->
    <div
        class="flex flex-row justify-between items-center rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 mb-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <!-- Label -->
            <div class="flex items-center h-10">
                <label class="text-sm font-medium text-gray-700 whitespace-nowrap dark:text-gray-300">
                    Tampilkan Data Tahun
                </label>
            </div>

            <!-- Dropdown Tahun -->
            <div x-data="{ isOptionSelected: true }" class="relative z-20 bg-transparent w-full sm:w-auto">
                <select id="tahunFilter"
                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full sm:w-36 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-4 pr-10 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500">
                    @foreach ($years as $yr)
                        <option value="{{ $yr }}" {{ $yr == $currentYear ? 'selected' : '' }} class="text-gray-700 dark:text-gray-300">
                            {{ $yr }}
                        </option>
                    @endforeach
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
        </div>
        <button
            class="gap-2 rounded-full border border-gray-300
            bg-white px-4 py-3 text-sm font-medium text-gray-700
            shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
            @click="$dispatch('open-smart-modal', {
                    modalId: 'modal-rencana-jpt',
            })">
            Tambah RK JPT
        </button>
    </div>

    <!-- Modal untuk Rencana JPT -->
    <x-ui.smart-modal id="modal-rencana-jpt" class="max-w-xl"
        @open-smart-modal.window="
    if ($event.detail.modalId !== 'modal-rencana-jpt') return;

    mode    = $event.detail.mode ?? 'create'
    itemKey  = $event.detail.key ?? null
    formData = $event.detail.data ?? { tahun: '', nama_rencana_jpt: '' }">

        <!-- HEADER -->
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h4 class="text-xl font-semibold text-gray-800 dark:text-white"
                x-text="mode === 'create' ? 'Tambah Rencana Kerja JPT' : 'Edit Rencana Kerja JPT'"></h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                x-text="mode === 'create' ? 'Masukkan rencana kerja JPT baru' : 'Edit rencana kerja JPT'"></p>
        </div>

        <!-- BODY -->
        <div class="flex-1 px-6 py-6 dark:bg-gray-900">
            <form
                :action="mode === 'edit'
                    ?
                    `{{ url('rencana-indikator-jpt/rencana') }}/${itemKey}` :
                    `{{ route('rencana-indikator-jpt.rencana.store') }}`"
                method="POST" class="space-y-6">
                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Tahun -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <select name="tahun" x-model="formData.tahun"
                        class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800
                    focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors
                    dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <option value="2026">2026</option>
                    </select>
                </div>

                <!-- Nama Rencana -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2 dark:text-gray-300">
                        Rencana Kerja JPT <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="formData.nama_rencana_jpt" name="nama_rencana_jpt"
                        placeholder="Contoh: Pengembangan Sistem Manajemen Kinerja"
                        class="w-full h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800
                    focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors
                    dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500"
                        required />
                </div>

                <!-- OPSIONAL: Tambah IKI saat CREATE -->
                <template x-if="mode === 'create'">
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700" x-data="{ addIki: false, ikis: [''] }">

                        <!-- Toggle -->
                        <div class="flex items-center gap-3 mb-4">
                            <input type="checkbox" id="add-iki-toggle" x-model="addIki"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500
                            dark:border-gray-600 dark:bg-gray-700">
                            <label for="add-iki-toggle" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Tambah IKI JPT sekaligus
                            </label>
                        </div>

                        <!-- List IKI -->
                        <div x-show="addIki" x-transition class="space-y-4">
                            <div
                                class="rounded-lg border border-gray-200 p-4 bg-gray-50
                            dark:border-gray-700 dark:bg-gray-800/50">
                                <div class="space-y-3">
                                    <template x-for="(iki, index) in ikis" :key="index">
                                        <div class="flex flex-col gap-2">

                                            <!-- nama indikator -->
                                            <div class="flex gap-3 items-center">
                                                <input type="text" :name="`ikis[${index}][nama_indikator_jpt]`"
                                                    x-model="ikis[index]" placeholder="Masukkan indikator kinerja individu"
                                                    class="flex-1 h-10 rounded-lg border border-gray-300 px-3 text-sm
                focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors
                dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">

                                                <button type="button" @click="ikis.splice(index,1)"
                                                    x-show="ikis.length > 1"
                                                    class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg
                text-gray-500 hover:text-red-600 hover:bg-red-50
                dark:text-gray-400 dark:hover:text-red-400 dark:hover:bg-red-900/20">
                                                    ✕
                                                </button>
                                            </div>

                                            <!-- input tambahan -->
                                            <div class="grid grid-cols-4 gap-3">

                                                <input type="text" :name="`ikis[${index}][satuan]`" placeholder="Satuan"
                                                    class="h-10 rounded-lg border border-gray-300 px-3 text-sm
                dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">

                                                <input type="number" step="0.00001" :name="`ikis[${index}][target]`" placeholder="Target"
                                                    class="h-10 rounded-lg border border-gray-300 px-3 text-sm
                dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">

                                                <input type="number" step="0.00001" :name="`ikis[${index}][realisasi]`"
                                                    placeholder="Realisasi"
                                                    class="h-10 rounded-lg border border-gray-300 px-3 text-sm
                dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">

                                                <select :name="`ikis[${index}][status]`"
                                                    class="h-10 rounded-lg border border-gray-300 px-3 text-sm
                dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                    <option value="">Status</option>
                                                    <option value="Selesai">Selesai</option>
                                                    <option value="Belum Selesai">Belum Selesai</option>
                                                </select>

                                            </div>

                                        </div>
                                    </template>
                                </div>

                                <!-- Add IKI button -->
                                <button type="button" @click="ikis.push('')"
                                    class="mt-4 flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700
                                dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah IKI
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- FOOTER -->
                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button @click="open = false" type="button"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-lg border border-gray-300 bg-white
                        text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors
                        dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            Batal
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-blue-600
                        text-sm font-medium text-white hover:bg-blue-700 transition-colors
                        dark:bg-blue-600 dark:hover:bg-blue-700">
                            <span x-text="mode === 'create' ? 'Simpan Data' : 'Update Data'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-ui.smart-modal>

    <!-- Modal untuk Indikator JPT -->
    <x-ui.smart-modal id="modal-indikator-jpt" class="max-w-xl"
        @open-smart-modal.window="
                if ($event.detail.modalId !== 'modal-indikator-jpt') return;

                mode        = $event.detail.mode ?? 'create'
                itemKey      = $event.detail.key ?? null
                formData = {
                id_rencana_jpt: null,
                nama_rencana_jpt: '',
                nama_indikator_jpt: '',
                satuan: '',
                target: '',
                realisasi: '',
                status: '',
                ...($event.detail.data ?? {})
            }">
        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
            <h4 class="text-2xl font-semibold text-gray-800 dark:text-white"
                x-text="mode === 'create' ? 'Tambah Indikator JPT' : 'Edit Indikator JPT'">
            </h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                x-text="mode === 'create'
                    ? 'Tambahkan indikator untuk rencana kerja'
                    : 'Perbarui indikator kinerja'">
            </p>
        </div>

        <!-- BODY -->
        <div class="flex-1 px-6 py-5 dark:bg-gray-900">
            <form method="POST"
                :action="mode === 'edit'

                    ?
                    `{{ url('rencana-indikator-jpt') }}/${formData.id_rencana_jpt}/indikator/${itemKey}` :
                    `{{ url('rencana-indikator-jpt') }}/${formData.id_rencana_jpt}/indikator`"
                class="grid grid-cols-1 gap-y-5">

                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>


                <!-- RK (DISABLED) -->
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Rencana Kerja JPT
                    </label>
                    <input type="text" name="nama_rencana_jpt" x-model="formData.nama_rencana_jpt" disabled
                        class="md:w-3/4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                            cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                </div>

                <!-- INPUT IKI -->
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Indikator JPT
                    </label>
                    <input type="text" name="nama_indikator_jpt" x-model="formData.nama_indikator_jpt"
                        placeholder="Masukkan indikator kinerja" required
                        class="md:w-3/4 h-11 rounded-lg border border-gray-300 px-4 text-sm
                            focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10
                            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500">
                </div>

                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Satuan
                    </label>

                    <input type="text" name="satuan" x-model="formData.satuan" placeholder="Contoh: Persen, Dokumen"
                        class="md:w-3/4 h-11 rounded-lg border border-gray-300 px-4 text-sm
        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                </div>

                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Target
                    </label>

                    <input type="number" step="0.00001" name="target" x-model="formData.target" placeholder="Masukkan target"
                        class="md:w-3/4 h-11 rounded-lg border border-gray-300 px-4 text-sm
        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                </div>

                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Realisasi
                    </label>

                    <input type="number" step="0.00001" name="realisasi" x-model="formData.realisasi" placeholder="Masukkan realisasi"
                        class="md:w-3/4 h-11 rounded-lg border border-gray-300 px-4 text-sm
        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                </div>

                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Status
                    </label>

                    <select name="status" x-model="formData.status"
                        class="md:w-3/4 h-11 rounded-lg border border-gray-300 px-4 text-sm
        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">

                        <option value="">Pilih Status</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Belum Selesai">Belum Selesai</option>

                    </select>
                </div>

                <!-- FOOTER -->
                <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-700">
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="open = false"
                            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium
                                    text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            Batal
                        </button>

                        <button type="submit"
                            class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 dark:bg-brand-600 dark:hover:bg-brand-700">
                            <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </x-ui.smart-modal>

    <!-- Tabel Utama -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800">
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16 dark:text-gray-400">
                        No.
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        Tahun
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        Nama Rencana Kerja
                    </th>
                    <th scope="col"
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32 dark:text-gray-400">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                <tr id="noDataRow" class="hidden">
                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Tidak ada data rencana kerja untuk tahun yang dipilih.
                    </td>
                </tr>
                <!-- Baris Rencana Kerja -->
                @forelse ($rencanaJpts as $index => $rencanaJpt)
                    <tr class="rk-row hover:bg-gray-50 dark:hover:bg-gray-800" data-tahun="{{ $rencanaJpt->tahun }}" data-id="{{ $rencanaJpt->id }}">
                        <td
                            class="rk-index px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center dark:text-gray-300">
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
                                class="expand-indicator-btn inline-flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200 transition-colors dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                                data-target="indicator-{{ $rencanaJpt->id }}">
                                <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                                Tampilkan Indikator
                            </button>
                            <div class="relative inline-block group">
                                <!-- Button Minimalis -->
                                <button
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:border-green-400 hover:text-green-600 transition-all duration-200 shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:border-green-500 dark:hover:text-green-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Aksi
                                </button>

                                <!-- Dropdown Simple -->
                                <div
                                    class="absolute right-0 mt-1 w-36 origin-top-right rounded-md bg-white border border-gray-200 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50 dark:bg-gray-800 dark:border-gray-700">
                                    <div class="py-1">
                                        <button
                                            class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 dark:text-gray-300 dark:hover:bg-gray-700"
                                            @click="$dispatch('open-smart-modal', {
                                                modalId: 'modal-rencana-jpt',
                                                mode: 'edit',
                                                key: {{ $rencanaJpt->id }},
                                                data: {
                                                    tahun: '{{ $rencanaJpt->tahun }}',
                                                    nama_rencana_jpt: '{{ $rencanaJpt->nama_rencana_jpt }}'
                                                }
                                            })">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
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
                                                    {{ json_encode($rencanaJpt->nama_rencana_jpt) }}
                                                )"
                                                class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2 dark:text-red-400 dark:hover:bg-gray-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>

                                        <button
                                            class="w-full text-left px-3 py-2 text-sm text-green-600 hover:bg-gray-100 flex items-center gap-2 dark:text-green-400 dark:hover:bg-gray-700"
                                            @click="$dispatch('open-smart-modal', {
                                            modalId: 'modal-indikator-jpt',
                                            data: {
                                                id_rencana_jpt: '{{ $rencanaJpt->id }}',
                                                nama_rencana_jpt: '{{ $rencanaJpt->nama_rencana_jpt }}'
                                            }
                                        })">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Tambah IKI
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Baris Indikator Kerja (Hidden) -->
                    <tr id="indicator-{{ $rencanaJpt->id }}" class="hidden bg-gray-50 dark:bg-gray-800">
                        <td colspan="4" class="px-4 py-4">
                            <div class="ml-8">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 dark:text-gray-300">IKI JPT</h4>

                                @if ($rencanaJpt->indikatorjpts && $rencanaJpt->indikatorjpts->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead>
                                                <tr class="bg-gray-100 dark:bg-gray-700">
                                                    <th
                                                        class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300">
                                                        No.</th>
                                                    <th
                                                        class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300">
                                                        Nama Indikator</th>
                                                    <th
                                                        class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300">
                                                        Satuan
                                                    </th>

                                                    <th
                                                        class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300">
                                                        Target
                                                    </th>

                                                    <th
                                                        class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300">
                                                        Realisasi
                                                    </th>

                                                    <th
                                                        class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300">
                                                        Status
                                                    </th>
                                                    <th
                                                        class="px-4 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-300">
                                                        Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach ($rencanaJpt->indikatorjpts as $indikatorIndex => $indikator)
                                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                                            {{ $indikatorIndex + 1 }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                                            {{ $indikator->nama_indikator_jpt }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                                            {{ $indikator->satuan ?? '-' }}
                                                        </td>

                                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                                            {{ $indikator->target ?? '-' }}
                                                        </td>

                                                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                                            {{ $indikator->realisasi ?? '-' }}
                                                        </td>

                                                        <td class="px-4 py-2 text-sm">
                                                            @if ($indikator->status == 'Selesai')
                                                                <span
                                                                    class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">
                                                                    Selesai
                                                                </span>
                                                            @elseif($indikator->status == 'Belum Selesai')
                                                                <span
                                                                    class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">
                                                                    Belum Selesai
                                                                </span>
                                                            @else
                                                                <span class="text-gray-700 text-xs">-</span>
                                                            @endif
                                                        </td>

                                                        <td class="px-4 py-2 text-sm">
                                                            <button
                                                                class="inline-flex items-center text-blue-600 hover:text-blue-800 mr-3 dark:text-blue-400 dark:hover:text-blue-300"
                                                                @click="$dispatch('open-smart-modal', {
                                                                    modalId: 'modal-indikator-jpt',
                                                                    mode: 'edit',
                                                                    key: {{ $indikator->id }},
                                                                    data: {
                                                                            id_rencana_jpt: '{{ $rencanaJpt->id }}',
                                                                            nama_rencana_jpt: '{{ $rencanaJpt->nama_rencana_jpt }}',
                                                                            nama_indikator_jpt: '{{ $indikator->nama_indikator_jpt }}',
                                                                            satuan: '{{ $indikator->satuan }}',
                                                                            target: '{{ $indikator->target }}',
                                                                            realisasi: '{{ $indikator->realisasi }}',
                                                                            status: '{{ $indikator->status }}'
                                                                        }
                                                                })">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                                    </path>
                                                                </svg>
                                                                Edit
                                                            </button>

                                                            <form id="delete-indikator-{{ $indikator->id }}"
                                                                action="{{ route('rencana-indikator-jpt.indikator.delete', [
                                                                    'rencanaJpt' => $rencanaJpt->id,
                                                                    'indikatorJpt' => $indikator->id,
                                                                ]) }}"
                                                                method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="button"
                                                                    onclick="SwalHelper.confirmDelete(
                                                                        'delete-indikator-{{ $indikator->id }}',
                                                                        {{ json_encode($indikator->nama_indikator_jpt) }}
                                                                    )"
                                                                    class="inline-flex items-center text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
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
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="noDataInitial">
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

                // Client-side year filtering for RK & IKI JPT
                function filterTableByTahun(selectedYear) {
                    const rows = document.querySelectorAll('.rk-row');
                    const noDataRow = document.getElementById('noDataRow');
                    const noDataInitial = document.getElementById('noDataInitial');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const rowTahun = row.getAttribute('data-tahun');
                        const rkId = row.getAttribute('data-id');
                        const indicatorRow = document.getElementById(`indicator-${rkId}`);

                        if (rowTahun === selectedYear) {
                            row.classList.remove('hidden');
                            visibleCount++;
                        } else {
                            row.classList.add('hidden');
                            if (indicatorRow) {
                                indicatorRow.classList.add('hidden');
                                // Reset expand button text & icon
                                const btn = row.querySelector('.expand-indicator-btn');
                                if (btn) {
                                    const icon = btn.querySelector('svg');
                                    if (icon) icon.style.transform = 'rotate(0deg)';
                                    btn.innerHTML = `
                                        <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        Tampilkan Indikator
                                    `;
                                }
                            }
                        }
                    });

                    // Update index numbers
                    let indexNum = 1;
                    rows.forEach(row => {
                        if (!row.classList.contains('hidden')) {
                            const indexCell = row.querySelector('.rk-index');
                            if (indexCell) {
                                indexCell.innerText = indexNum++;
                            }
                        }
                    });

                    if (noDataInitial) {
                        if (visibleCount > 0) {
                            noDataInitial.classList.add('hidden');
                        } else {
                            noDataInitial.classList.remove('hidden');
                        }
                    }

                    if (visibleCount === 0) {
                        if (noDataRow) noDataRow.classList.remove('hidden');
                    } else {
                        if (noDataRow) noDataRow.classList.add('hidden');
                    }
                }

                // Bind select change
                const selectFilter = document.getElementById('tahunFilter');
                if (selectFilter) {
                    filterTableByTahun(selectFilter.value);
                    selectFilter.addEventListener('change', function() {
                        filterTableByTahun(this.value);
                    });
                }
            });
        </script>
    @endpush

@endsection
