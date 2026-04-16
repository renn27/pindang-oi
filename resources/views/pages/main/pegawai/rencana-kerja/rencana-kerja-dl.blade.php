@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{$title}}" />

    <div id="app" data-pegawais='@json($pegawais)'></div>

    <x-ui.smart-modal
        id="modal-verifikasi-translok"
        class="max-w-md"
        @open-smart-modal.window="
            if ($event.detail.modalId !== 'modal-verifikasi-translok') return;

            itemKey = $event.detail.key ?? null;

            Object.assign(formData, $event.detail.data ?? {});
        ">
        <form
            :action="`/penugasan/${itemKey}/rencana-kerja-translok`"
            method="POST"
            class="grid grid-cols-1 gap-y-4">
            @csrf
            @method('PUT')

            <div class="rounded-3xl bg-white dark:bg-gray-800">

                <!-- HEADER -->
                <div class="border-b dark:border-gray-700 px-6 py-4">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Verifikasi Translok
                    </h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Review singkat sebelum mengambil keputusan
                    </p>
                </div>

                <!-- BODY -->
                <div class="px-6 py-5 space-y-3 text-sm text-gray-700 dark:text-gray-400">
                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-300">Nama Pegawai :</span><br>
                        <span x-text="formData.nama_pegawai"></span>
                    </div>

                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-300">Jenis Kegiatan :</span><br>
                        <span x-text="formData.jenis_kegiatan"></span>
                    </div>

                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-300">Waktu Pelaksanaan :</span><br>
                        <span x-text="formData.tanggal_mulai"></span>
                        s.d.
                        <span x-text="formData.tanggal_selesai"></span>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="border-t dark:border-gray-700 px-6 py-4 flex justify-end gap-2">
                    <button
                        type="button"
                        @click="open = false"
                        class="rounded-lg border dark:border-gray-600 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Batal
                    </button>

                    @if (Auth::user()->active_role === 'Pimpinan')
                        <button
                            type="submit"
                            name="status_translok"
                            value="Ditolak"
                            class="rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2 text-sm text-white">
                            Tolak
                        </button>

                        <button
                            type="submit"
                            name="status_translok"
                            value="ACC"
                            class="rounded-lg bg-teal-600 hover:bg-teal-700 px-4 py-2 text-sm text-white">
                            Setujui
                        </button>
                    @endif

                    @if (Auth::user()->active_role === 'Ketua Tim')
                        <button
                            type="submit"
                            name="status_translok"
                            value="Menunggu"
                            class="rounded-lg bg-orange-600 hover:bg-orange-700 px-4 py-2 text-sm text-white">
                            Ajukan Kembali
                        </button>
                    @endif
                </div>

            </div>
        </form>
    </x-ui.smart-modal>

    <x-ui.smart-modal
        id="modal-verifikasi-dl"
        class="max-w-md"
        @open-smart-modal.window="
            if ($event.detail.modalId !== 'modal-verifikasi-dl') return;

            itemKey = $event.detail.key ?? null;

            Object.assign(formData, $event.detail.data ?? {});
        ">
        <form
            :action="`/penugasan/${itemKey}/rencana-kerja-dl`"
            method="POST"
            class="grid grid-cols-1 gap-y-4">
            @csrf
            @method('PUT')

            <div class="rounded-3xl bg-white dark:bg-gray-800">

                <!-- HEADER -->
                <div class="border-b dark:border-gray-700 px-6 py-4">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                        Verifikasi Dinas Luar
                    </h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Review singkat sebelum mengambil keputusan
                    </p>
                </div>

                <!-- BODY -->
                <div class="px-6 py-5 space-y-3 text-sm text-gray-700 dark:text-gray-400">
                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-300">Nama Pegawai:</span><br>
                        <span x-text="formData.nama_pegawai"></span>
                    </div>

                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-300">Jenis Kegiatan:</span><br>
                        <span x-text="formData.jenis_kegiatan"></span>
                    </div>

                    <div>
                        <span class="font-medium text-gray-800 dark:text-gray-300">Waktu Pelaksanaan:</span><br>
                        <span x-text="formData.tanggal_mulai"></span>
                        s.d
                        <span x-text="formData.tanggal_selesai"></span>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="border-t dark:border-gray-700 px-6 py-4 flex justify-end gap-2">
                    <button
                        type="button"
                        @click="open = false"
                        class="rounded-lg border dark:border-gray-600 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Batal
                    </button>

                    @if (Auth::user()->active_role === 'Pimpinan')
                        <button
                            type="submit"
                            name="status_dl"
                            value="Ditolak"
                            class="rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2 text-sm text-white">
                            Tolak
                        </button>

                        <button
                            type="submit"
                            name="status_dl"
                            value="ACC"
                            class="rounded-lg bg-green-600 hover:bg-green-700 px-4 py-2 text-sm text-white">
                            Setujui
                        </button>
                    @endif

                    @if (Auth::user()->active_role === 'Ketua Tim')
                        <button
                            type="submit"
                            name="status_dl"
                            value="Menunggu"
                            class="rounded-lg bg-orange-600 hover:bg-orange-700 px-4 py-2 text-sm text-white">
                            Ajukan Kembali
                        </button>
                    @endif
                </div>

            </div>
        </form>
    </x-ui.smart-modal>

    <div class="space-y-6">
        <!-- Tampilan Card Fungsi dengan Accordion -->
        <x-common.component-card title="Daftar Rencana Kerja Perlu DL">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="rounded-xl border dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Rencana Kerja</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white">
                        {{ $allPenugasans->count() }}
                    </p>
                </div>

                <div class="rounded-xl border dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Verifikasi</p>
                    <p class="mt-1 text-2xl font-semibold text-yellow-600 dark:text-yellow-500">
                        {{ $menunggu }}
                    </p>
                </div>

                <div class="rounded-xl border dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Diterima</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-500">
                        {{ $diterima }}
                    </p>
                </div>

                <div class="rounded-xl border dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Ditolak</p>
                    <p class="mt-1 text-2xl font-semibold text-red-600 dark:text-red-500">
                        {{ $ditolak }}
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                @foreach ($bidangs as $index => $bidang)
                    <div x-data="{
                        id: 'accordion-{{ $bidang->id_bidang }}',
                        open: false,

                        init() {
                            const saved = JSON.parse(localStorage.getItem(this.id))

                            if (saved) {
                                const now = Date.now()
                                const limit = 1 * 60 * 1000 // 1 menit

                                if (now - saved.time < limit) {
                                    this.open = saved.open
                                } else {
                                    localStorage.removeItem(this.id)
                                }
                            }
                        },

                        toggle() {
                            this.open = !this.open

                            localStorage.setItem(this.id, JSON.stringify({
                                open: this.open,
                                time: Date.now()
                            }))
                        }
                    }"
                        x-init="init()"
                        class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <!-- Header Fungsi -->
                        <button @click="toggle()"
                            class="flex w-full items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="flex items-center gap-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                        {{ $bidang->nama_bidang }}
                                        {{-- Untuk Pimpinan --}}
                                        @if(Auth::user()->active_role === 'Pimpinan' && $bidang->menungguCount > 0)
                                            <span class="animate-pulse inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold text-yellow-800 dark:text-yellow-300 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
                                                {{ $bidang->menungguCount }} menunggu verifikasi
                                            </span>
                                        @endif

                                        {{-- Untuk Ketua Tim --}}
                                        @if(Auth::user()->active_role === 'Ketua Tim' && $bidang->ditolakCount > 0)
                                            <span class="animate-pulse inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold text-red-800 dark:text-red-300 bg-red-100 dark:bg-red-900/30 rounded-full">
                                                {{ $bidang->ditolakCount }} ditolak
                                            </span>
                                        @endif
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Total {{ $bidang->kegiatans->count() }} kegiatan
                                    </p>
                                </div>
                            </div>
                            <svg :class="{ 'rotate-180': open }" class="h-5 w-5 text-gray-500 dark:text-gray-400 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Accordion Content -->
                        <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-gray-700">
                            @if ($bidang->kegiatans->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full border border-gray-200 dark:border-gray-700 table-fixed">
                                        <colgroup>
                                            <col class="w-[15%]"> <!-- Kegiatan -->
                                            <col class="w-[20%]"> <!-- Sub Kegiatan -->
                                            <col class="w-[15%]"> <!-- Nama Pegawai -->
                                            <col class="w-[10%]"> <!-- Jenis Kegiatan -->
                                            <col class="w-[10%]"> <!-- Target dan Satuan -->
                                            <col class="w-[15%]"> <!-- Tanggal -->
                                            <col class="w-[15%]"> <!-- Status DL -->
                                        </colgroup>
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">
                                                    Kegiatan
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">
                                                    Sub Kegiatan
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">
                                                    Nama Anggota
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">
                                                    Jenis Kegiatan
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">
                                                    Target
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">
                                                    Waktu Pelaksanaan
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">
                                                    Status DL / Translok
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $rowCounter = 0;
                                            @endphp

                                            @foreach ($bidang->kegiatans as $kegiatan)
                                                @php
                                                    $kegiatanRowCount = 0;
                                                    // Hitung total baris untuk kegiatan ini
                                                    foreach ($kegiatan->subKegiatans as $subKegiatan) {
                                                        $kegiatanRowCount += $subKegiatan->penugasans->count();
                                                    }
                                                @endphp

                                                @foreach ($kegiatan->subKegiatans as $subIndex => $subKegiatan)
                                                    @php
                                                        $subRowCount = $subKegiatan->penugasans->count();
                                                    @endphp

                                                    @foreach ($subKegiatan->penugasans as $penugasanIndex => $penugasan)
                                                        <tr
                                                            class="{{ !($loop->parent->last && $loop->last) ? 'border-b border-gray-200 dark:border-gray-700' : '' }}">
                                                            <!-- Kolom Kegiatan (rowspan hanya untuk baris pertama tiap kegiatan) -->
                                                            @if ($subIndex === 0 && $penugasanIndex === 0)
                                                                <td class="px-4 py-3 align-top border border-gray-200 dark:border-gray-700"
                                                                    rowspan="{{ $kegiatanRowCount }}">
                                                                    <div class="flex flex-col">
                                                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                                                            {{ $kegiatan->nama_rk_kegiatan }}
                                                                        </span>
                                                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                            Ketua:
                                                                            {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            @endif

                                                            <!-- Kolom Sub Kegiatan (rowspan hanya untuk baris pertama tiap sub kegiatan) -->
                                                            @if ($penugasanIndex === 0)
                                                                <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300 align-top border border-gray-200 dark:border-gray-700"
                                                                    rowspan="{{ $subRowCount }}">
                                                                    {{ $subKegiatan->nama_sub_kegiatan }}
                                                                </td>
                                                            @endif

                                                            <!-- Kolom Nama Pegawai -->
                                                            <td
                                                                class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300 align-top border border-gray-200 dark:border-gray-700">
                                                                {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                                                            </td>

                                                            <!-- Kolom Jenis Kegiatan dengan Badge -->
                                                            <td
                                                                class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300 align-top border border-gray-200 dark:border-gray-700">
                                                                {{ $penugasan->jenisKegiatan->jenis_kegiatan ?? '-' }}
                                                            </td>

                                                            <!-- Kolom Target -->
                                                            <td
                                                                class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300 align-top border border-gray-200 dark:border-gray-700">
                                                                {{ $penugasan->target ?? '-' }} <span class="text-orange-800 dark:text-orange-400">({{ $penugasan->satuan_target ?? '-' }})</span>
                                                            </td>

                                                            <!-- Kolom Satuan -->
                                                            <td
                                                                class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300 align-top border border-gray-200 dark:border-gray-700">
                                                                {{ $penugasan->tanggal_mulai?->translatedFormat('d M Y') ?? '-' }} - {{ $penugasan->tanggal_selesai?->translatedFormat('d M Y') ?? '-' }}
                                                            </td>

                                                            <td class="px-4 py-3 text-sm align-top border border-gray-200 dark:border-gray-700 space-y-4">
                                                                {{-- DARI SINI ADALAH BLOK UNTUK DL --}}
                                                                @if($penugasan->butuh_dl)
                                                                    <div class="flex items-center gap-2">
                                                                        <div class="justify-self-start font-medium text-[10px] text-gray-500 w-12 border-t border-gray-100 dark:border-gray-700 pt-1 mt-1">DL:</div>
                                                                        <div class="justify-self-start">
                                                                            {{-- BADGE STATUS DL --}}
                                                                            @if ($penugasan->status_dl === 'Menunggu')
                                                                                <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2.5 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-400">Menunggu</span>
                                                                            @elseif ($penugasan->status_dl === 'ACC')
                                                                                <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900/30 px-2.5 py-1 text-xs font-medium text-blue-800 dark:text-blue-400">Diterima</span>
                                                                            @elseif ($penugasan->status_dl === 'Ditolak')
                                                                                <div class="flex items-center gap-2">
                                                                                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-1 text-xs font-medium text-red-800 dark:text-red-400">Ditolak</span>
                                                                                    @if (Auth::user()->active_role === 'Ketua Tim')
                                                                                        <button type="button" title="Ajukan Kembali"
                                                                                            @click="$dispatch('open-smart-modal', {
                                                                                                modalId: 'modal-verifikasi-dl',
                                                                                                key: @js($penugasan->id_penugasan),
                                                                                                data: {
                                                                                                    nama_pegawai: @js($penugasan->anggota->nama_pegawai),
                                                                                                    jenis_kegiatan: @js($penugasan->jenisKegiatan->jenis_kegiatan),
                                                                                                    tanggal_mulai: @js($penugasan->tanggal_mulai->format('d M Y')),
                                                                                                    tanggal_selesai: @js($penugasan->tanggal_selesai->format('d M Y')),
                                                                                                }
                                                                                            })"
                                                                                            class="flex items-center px-2 py-1 text-[8px] font-medium bg-gray-200/80 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-200 dark:hover:bg-blue-800/30 hover:text-blue-700 dark:hover:text-blue-400 whitespace-nowrap border-b border-gray-200 dark:border-gray-600">
                                                                                            <span class="mr-1">↻</span> Ajukan Kembali
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            @endif
                                                                        </div>

                                                                        @can('acceptDL', $penugasan)
                                                                            @if ($penugasan->status_dl === 'ACC')
                                                                                @if ($penugasan->sudahMasukKalenderDL())
                                                                                    <span class="items-center px-2 py-1 text-[8px] cursor-not-allowed font-medium bg-blue-100/50 dark:bg-blue-900/20 text-blue-600/50 dark:text-blue-500/50 flex whitespace-nowrap border-b border-blue-200 dark:border-blue-800">
                                                                                        ✓ Sudah masuk kalender
                                                                                    </span>
                                                                                @else
                                                                                    <form action="{{ route('kalenderDL.store') }}" method="POST" class="inline">
                                                                                        @csrf
                                                                                        <input type="hidden" name="id_pegawai" value="{{ $penugasan->id_anggota }}">
                                                                                        <input type="hidden" name="id_penugasan" value="{{ $penugasan->id_penugasan }}">
                                                                                        <input type="hidden" name="tanggal_mulai" value="{{ $penugasan->tanggal_mulai }}">
                                                                                        <input type="hidden" name="tanggal_selesai" value="{{ $penugasan->tanggal_selesai }}">
                                                                                        <button type="submit" class="items-center px-2 py-1 text-[8px] font-medium bg-gray-100/80 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-200 dark:hover:bg-blue-800/30 flex whitespace-nowrap border-b border-gray-200 dark:border-gray-600">
                                                                                            Masukkan Kalender DL
                                                                                        </button>
                                                                                    </form>
                                                                                @endif
                                                                            @elseif ($penugasan->status_dl === 'Ditolak')
                                                                                <span class="items-center px-2 py-1 text-[8px] font-medium cursor-not-allowed bg-red-100/50 dark:bg-red-900/20 text-red-600/50 dark:text-red-500/50 flex whitespace-nowrap border-b border-red-200 dark:border-red-800">
                                                                                    Tunggu Perubahan
                                                                                </span>
                                                                            @else
                                                                                <button type="button" @click="$dispatch('open-smart-modal', {
                                                                                        modalId: 'modal-verifikasi-dl',
                                                                                        key: @js($penugasan->id_penugasan),
                                                                                        data: {
                                                                                            nama_pegawai: @js($penugasan->anggota->nama_pegawai),
                                                                                            jenis_kegiatan: @js($penugasan->jenisKegiatan->jenis_kegiatan),
                                                                                            tanggal_mulai: @js($penugasan->tanggal_mulai->format('d M Y')),
                                                                                            tanggal_selesai: @js($penugasan->tanggal_selesai->format('d M Y')),
                                                                                        }
                                                                                    })"
                                                                                    class="items-center px-2 py-1 text-[8px] font-medium bg-gray-100/80 dark:bg-gray-700 text-gray-700 dark:text-gray-300 flex whitespace-nowrap border-b border-gray-200 dark:border-gray-600 hover:bg-orange-200 dark:hover:bg-orange-800/30">
                                                                                    ✓ Verifikasi
                                                                                </button>
                                                                            @endif
                                                                        @endcan
                                                                    </div>
                                                                @endif

                                                                {{-- DARI SINI ADALAH BLOK UNTUK TRANSLOK --}}
                                                                @if($penugasan->butuh_translok)
                                                                    <div class="flex items-center gap-2">
                                                                        <div class="justify-self-start font-medium text-[10px] text-gray-500 w-12 border-t border-gray-100 dark:border-gray-700 pt-1 mt-1">Translok:</div>
                                                                        <div class="justify-self-start">
                                                                            {{-- BADGE STATUS TRANSLOK --}}
                                                                            @if ($penugasan->status_translok === 'Menunggu')
                                                                                <span class="inline-flex items-center rounded-full bg-yellow-100 dark:bg-yellow-900/30 px-2.5 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-400">Menunggu</span>
                                                                            @elseif ($penugasan->status_translok === 'ACC')
                                                                                <span class="inline-flex items-center rounded-full bg-teal-100 dark:bg-teal-900/30 px-2.5 py-1 text-xs font-medium text-teal-800 dark:text-teal-400">Diterima</span>
                                                                            @elseif ($penugasan->status_translok === 'Ditolak')
                                                                                <div class="flex items-center gap-2">
                                                                                    <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-1 text-xs font-medium text-red-800 dark:text-red-400">Ditolak</span>
                                                                                    @if (Auth::user()->active_role === 'Ketua Tim')
                                                                                        <button type="button" title="Ajukan Kembali"
                                                                                            @click="$dispatch('open-smart-modal', {
                                                                                                modalId: 'modal-verifikasi-translok',
                                                                                                key: @js($penugasan->id_penugasan),
                                                                                                data: {
                                                                                                    nama_pegawai: @js($penugasan->anggota->nama_pegawai),
                                                                                                    jenis_kegiatan: @js($penugasan->jenisKegiatan->jenis_kegiatan),
                                                                                                    tanggal_mulai: @js($penugasan->tanggal_mulai->format('d M Y')),
                                                                                                    tanggal_selesai: @js($penugasan->tanggal_selesai->format('d M Y')),
                                                                                                }
                                                                                            })"
                                                                                            class="flex items-center px-2 py-1 text-[8px] font-medium bg-gray-200/80 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-teal-200 dark:hover:bg-teal-800/30 hover:text-teal-700 dark:hover:text-teal-400 whitespace-nowrap border-b border-gray-200 dark:border-gray-600">
                                                                                            <span class="mr-1">↻</span> Ajukan Kembali
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            @endif
                                                                        </div>

                                                                        @can('acceptTranslok', $penugasan)
                                                                            @if ($penugasan->status_translok === 'ACC')
                                                                                @if ($penugasan->sudahMasukKalenderDL())
                                                                                    <span class="items-center px-2 py-1 text-[8px] cursor-not-allowed font-medium bg-teal-100/50 dark:bg-teal-900/20 text-teal-600/50 dark:text-teal-500/50 flex whitespace-nowrap border-b border-teal-200 dark:border-teal-800">
                                                                                        ✓ Sudah masuk kalender
                                                                                    </span>
                                                                                @else
                                                                                    <form action="{{ route('kalenderDL.store') }}" method="POST" class="inline">
                                                                                        @csrf
                                                                                        <input type="hidden" name="id_pegawai" value="{{ $penugasan->id_anggota }}">
                                                                                        <input type="hidden" name="id_penugasan" value="{{ $penugasan->id_penugasan }}">
                                                                                        <input type="hidden" name="tanggal_mulai" value="{{ $penugasan->tanggal_mulai }}">
                                                                                        <input type="hidden" name="tanggal_selesai" value="{{ $penugasan->tanggal_selesai }}">
                                                                                        <!-- NOTE: Kita buat tombol generic "Masukkan Kalender" -->
                                                                                        <button type="submit" class="items-center px-2 py-1 text-[8px] font-medium bg-gray-100/80 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-teal-200 dark:hover:bg-teal-800/30 flex whitespace-nowrap border-b border-gray-200 dark:border-gray-600">
                                                                                            Masukkan Kalender Translok
                                                                                        </button>
                                                                                    </form>
                                                                                @endif
                                                                            @elseif ($penugasan->status_translok === 'Ditolak')
                                                                                <span class="items-center px-2 py-1 text-[8px] font-medium cursor-not-allowed bg-red-100/50 dark:bg-red-900/20 text-red-600/50 dark:text-red-500/50 flex whitespace-nowrap border-b border-red-200 dark:border-red-800">
                                                                                    Tunggu Perubahan
                                                                                </span>
                                                                            @else
                                                                                <button type="button" @click="$dispatch('open-smart-modal', {
                                                                                        modalId: 'modal-verifikasi-translok',
                                                                                        key: @js($penugasan->id_penugasan),
                                                                                        data: {
                                                                                            nama_pegawai: @js($penugasan->anggota->nama_pegawai),
                                                                                            jenis_kegiatan: @js($penugasan->jenisKegiatan->jenis_kegiatan),
                                                                                            tanggal_mulai: @js($penugasan->tanggal_mulai->format('d M Y')),
                                                                                            tanggal_selesai: @js($penugasan->tanggal_selesai->format('d M Y')),
                                                                                        }
                                                                                    })"
                                                                                    class="items-center px-2 py-1 text-[8px] font-medium bg-gray-100/80 dark:bg-gray-700 text-gray-700 dark:text-gray-300 flex whitespace-nowrap border-b border-gray-200 dark:border-gray-600 hover:bg-orange-200 dark:hover:bg-orange-800/30">
                                                                                    ✓ Verifikasi
                                                                                </button>
                                                                            @endif
                                                                        @endcan
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            @endforeach

                                            @if ($bidang->kegiatans->count() === 0)
                                                <tr>
                                                    <td colspan="6"
                                                        class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                                                        Belum ada kegiatan untuk bidang ini
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-4 text-center border border-gray-200 dark:border-gray-700 border-t-0">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Belum ada kegiatan untuk fungsi ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if ($bidangs->count() === 0)
                    <div class="text-center py-8 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada fungsi/bidang yang dibuat</p>
                    </div>
                @endif
            </div>
        </x-common.component-card>
    </div>
@endsection

