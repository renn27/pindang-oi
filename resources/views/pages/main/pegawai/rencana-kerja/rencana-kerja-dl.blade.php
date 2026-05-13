@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{$title}}" />

    <div id="app" data-pegawais='@json($pegawais)'></div>

    <x-ui.smart-modal id="modal-verifikasi-translok" class="max-w-md" @open-smart-modal.window="
                if ($event.detail.modalId !== 'modal-verifikasi-translok') return;

                itemKey = $event.detail.key ?? null;

                Object.assign(formData, $event.detail.data ?? {});
            ">
        <form :action="`/penugasan/${itemKey}/rencana-kerja-translok`" method="POST" class="grid grid-cols-1 gap-y-4">
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
                    <button type="button" @click="open = false"
                        class="rounded-lg border dark:border-gray-600 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Batal
                    </button>

                    @if (Auth::user()->active_role === 'Pimpinan')
                        <button type="submit" name="status_translok" value="Ditolak"
                            class="rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2 text-sm text-white">
                            Tolak
                        </button>

                        <button type="submit" name="status_translok" value="ACC"
                            class="rounded-lg bg-teal-600 hover:bg-teal-700 px-4 py-2 text-sm text-white">
                            Setujui
                        </button>
                    @endif

                    @if (Auth::user()->active_role === 'Ketua Tim')
                        <button type="submit" name="status_translok" value="Menunggu"
                            class="rounded-lg bg-orange-600 hover:bg-orange-700 px-4 py-2 text-sm text-white">
                            Ajukan Kembali
                        </button>
                    @endif
                </div>

            </div>
        </form>
    </x-ui.smart-modal>

    <x-ui.smart-modal id="modal-verifikasi-dl" class="max-w-md" @open-smart-modal.window="
                if ($event.detail.modalId !== 'modal-verifikasi-dl') return;

                itemKey = $event.detail.key ?? null;

                Object.assign(formData, $event.detail.data ?? {});
            ">
        <form :action="`/penugasan/${itemKey}/rencana-kerja-dl`" method="POST" class="grid grid-cols-1 gap-y-4">
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
                    <button type="button" @click="open = false"
                        class="rounded-lg border dark:border-gray-600 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Batal
                    </button>

                    @if (Auth::user()->active_role === 'Pimpinan')
                        <button type="submit" name="status_dl" value="Ditolak"
                            class="rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2 text-sm text-white">
                            Tolak
                        </button>

                        <button type="submit" name="status_dl" value="ACC"
                            class="rounded-lg bg-green-600 hover:bg-green-700 px-4 py-2 text-sm text-white">
                            Setujui
                        </button>
                    @endif

                    @if (Auth::user()->active_role === 'Ketua Tim')
                        <button type="submit" name="status_dl" value="Menunggu"
                            class="rounded-lg bg-orange-600 hover:bg-orange-700 px-4 py-2 text-sm text-white">
                            Ajukan Kembali
                        </button>
                    @endif
                </div>

            </div>
        </form>
    </x-ui.smart-modal>

    <div class="space-y-8">
        <!-- Tampilan Card Fungsi dengan Accordion -->
        <x-common.component-card title="Daftar Rencana Kerja Perlu DL / Translok">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Card: Total -->
                <div class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-theme-xs relative overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="absolute -right-4 -top-4 opacity-5 dark:opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-24 h-24 text-gray-600 dark:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 9h-2V7h-2v5H6v2h2v5h2v-5h2v-2z"/></svg>
                    </div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Penugasan</p>
                            <p class="mt-0.5 text-2xl font-bold text-gray-900 dark:text-white">{{ $allPenugasans->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card: Menunggu -->
                <div class="rounded-2xl border border-yellow-100 dark:border-yellow-900/30 bg-white dark:bg-gray-800 p-5 shadow-theme-xs relative overflow-hidden group hover:shadow-md hover:border-yellow-200 dark:hover:border-yellow-800/50 transition-all duration-300">
                    <div class="absolute -right-4 -top-4 opacity-5 dark:opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-24 h-24 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    </div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Menunggu Verif</p>
                            <p class="mt-0.5 text-2xl font-bold text-yellow-600 dark:text-yellow-500">{{ $menunggu }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card: Diterima -->
                <div class="rounded-2xl border border-green-100 dark:border-green-900/30 bg-white dark:bg-gray-800 p-5 shadow-theme-xs relative overflow-hidden group hover:shadow-md hover:border-green-200 dark:hover:border-green-800/50 transition-all duration-300">
                    <div class="absolute -right-4 -top-4 opacity-5 dark:opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-24 h-24 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                    </div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Diterima</p>
                            <p class="mt-0.5 text-2xl font-bold text-green-600 dark:text-green-500">{{ $diterima }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card: Ditolak -->
                <div class="rounded-2xl border border-red-100 dark:border-red-900/30 bg-white dark:bg-gray-800 p-5 shadow-theme-xs relative overflow-hidden group hover:shadow-md hover:border-red-200 dark:hover:border-red-800/50 transition-all duration-300">
                    <div class="absolute -right-4 -top-4 opacity-5 dark:opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-24 h-24 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>
                    </div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Ditolak</p>
                            <p class="mt-0.5 text-2xl font-bold text-red-600 dark:text-red-500">{{ $ditolak }}</p>
                        </div>
                    </div>
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
                            }" x-init="init()"
                        class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                        <!-- Header Fungsi -->
                        <button @click="toggle()"
                            class="flex w-full items-center justify-between p-4 md:p-5 text-left bg-gray-50/50 hover:bg-blue-50/50 dark:bg-gray-800 dark:hover:bg-gray-700/80 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="hidden sm:flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-800 dark:text-white flex flex-wrap items-center gap-2 tracking-wide">
                                        {{ $bidang->nama_bidang }}
                                        
                                        {{-- Untuk Pimpinan --}}
                                        @if(Auth::user()->active_role === 'Pimpinan')
                                            @if($bidang->menungguCount > 0)
                                                <span class="animate-pulse inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold text-yellow-800 dark:text-yellow-300 bg-yellow-100 dark:bg-yellow-900/30 rounded-full uppercase tracking-tighter shadow-sm border border-yellow-200 dark:border-yellow-800">
                                                    {{ $bidang->menungguCount }} Menunggu
                                                </span>
                                            @endif
                                            @if($bidang->accBelumMasukKalenderCount > 0)
                                                <span class="animate-pulse inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold text-orange-800 dark:text-orange-300 bg-orange-100 dark:bg-orange-900/30 rounded-full uppercase tracking-tighter shadow-sm border border-orange-200 dark:border-orange-800">
                                                    {{ $bidang->accBelumMasukKalenderCount }} Blm Kalender
                                                </span>
                                            @endif
                                        @endif

                                        {{-- Untuk Ketua Tim --}}
                                        @if(Auth::user()->active_role === 'Ketua Tim' && $bidang->ditolakCount > 0)
                                            <span class="animate-pulse inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold text-red-800 dark:text-red-300 bg-red-100 dark:bg-red-900/30 rounded-full uppercase tracking-tighter shadow-sm border border-red-200 dark:border-red-800">
                                                {{ $bidang->ditolakCount }} Ditolak
                                            </span>
                                        @endif
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Total <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $bidang->kegiatans->count() }}</span> master kegiatan
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-center h-8 w-8 rounded-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm text-gray-500 dark:text-gray-400">
                                <svg :class="{ 'rotate-180': open }" class="h-4 w-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <!-- Accordion Content -->
                        <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-gray-700">
                            @if ($bidang->kegiatans->count() > 0)
                                <div class="overflow-x-auto custom-scrollbar">
                                    <table class="min-w-[1350px] w-full border-collapse border border-gray-200 dark:border-gray-700 table-fixed bg-white dark:bg-gray-800">
                                        <colgroup>
                                            <col class="w-[200px]"> <!-- Kegiatan -->
                                            <col class="w-[220px]"> <!-- Sub Kegiatan -->
                                            <col class="w-[180px]"> <!-- Nama Anggota -->
                                            <col class="w-[120px]"> <!-- Jenis -->
                                            <col class="w-[90px]">  <!-- Target -->
                                            <col class="w-[190px]"> <!-- Waktu -->
                                            <col class="w-[130px]"> <!-- Status -->
                                            <col class="w-[150px]"> <!-- Aksi -->
                                        </colgroup>
                                        <thead>
                                            <tr class="bg-gray-50/80 dark:bg-gray-900/50">
                                                <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">Kegiatan</th>
                                                <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">Sub Kegiatan</th>
                                                <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">Nama Anggota</th>
                                                <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">Jenis</th>
                                                <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700 text-center">Target</th>
                                                <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">Waktu Pelaksanaan</th>
                                                <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">Status</th>
                                                <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border border-gray-200 dark:border-gray-700">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach ($bidang->kegiatans as $kegiatan)
                                                @php
                                                    $kegiatanRowCount = 0;
                                                    foreach ($kegiatan->subKegiatans as $subKeg) {
                                                        $kegiatanRowCount += $subKeg->penugasans->count();
                                                    }
                                                @endphp

                                                @foreach ($kegiatan->subKegiatans as $subIndex => $subKegiatan)
                                                    @php $subRowCount = $subKegiatan->penugasans->count(); @endphp

                                                    @foreach ($subKegiatan->penugasans as $penugasanIndex => $penugasan)
                                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                                            {{-- KOLOM KEGIATAN --}}
                                                            @if ($subIndex === 0 && $penugasanIndex === 0)
                                                                <td class="px-4 py-3 align-top border border-gray-200 dark:border-gray-700" rowspan="{{ $kegiatanRowCount }}">
                                                                    <div class="space-y-1">
                                                                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100 leading-tight">
                                                                            {{ $kegiatan->nama_rk_kegiatan }}
                                                                        </div>
                                                                        <div class="flex items-center gap-1.5 text-[10px] text-gray-500 dark:text-gray-400 font-medium">
                                                                            <span class="p-0.5 bg-gray-100 dark:bg-gray-700 rounded text-gray-400">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                                            </span>
                                                                            {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            @endif

                                                            {{-- KOLOM SUB KEGIATAN --}}
                                                            @if ($penugasanIndex === 0)
                                                                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 align-top border border-gray-200 dark:border-gray-700" rowspan="{{ $subRowCount }}">
                                                                    {{ $subKegiatan->nama_sub_kegiatan }}
                                                                </td>
                                                            @endif

                                                            {{-- KOLOM ANGGOTA --}}
                                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 align-top border border-gray-200 dark:border-gray-700">
                                                                {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                                                            </td>

                                                            {{-- KOLOM JENIS --}}
                                                            <td class="px-4 py-3 align-top border border-gray-200 dark:border-gray-700">
                                                                <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded uppercase tracking-tighter">
                                                                    {{ $penugasan->jenisKegiatan->jenis_kegiatan ?? '-' }}
                                                                </span>
                                                            </td>

                                                            {{-- KOLOM TARGET --}}
                                                            <td class="px-4 py-3 text-center align-top border border-gray-200 dark:border-gray-700">
                                                                <div class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $penugasan->target }}</div>
                                                                <div class="text-[9px] text-gray-400 uppercase font-medium">{{ $penugasan->satuan_target }}</div>
                                                            </td>

                                                            {{-- KOLOM WAKTU --}}
                                                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 align-top border border-gray-200 dark:border-gray-700 leading-relaxed font-medium">
                                                                <div class="flex items-start gap-1.5">
                                                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                                    {{ $penugasan->tanggal_mulai->format('d M Y') }} - {{ $penugasan->tanggal_selesai->format('d M Y') }}
                                                                </div>
                                                            </td>

                                                            {{-- KOLOM STATUS --}}
                                                            <td class="px-4 py-3 align-top border border-gray-200 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/20">
                                                                <div class="space-y-4">
                                                                    {{-- BLOK DL --}}
                                                                    @if($penugasan->butuh_dl)
                                                                        <div class="grid grid-cols-[30px_1fr] items-start gap-2 min-h-[26px]">
                                                                            <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase mt-1 tracking-widest border-r border-gray-200 dark:border-gray-700 pr-1">DL</span>
                                                                            <div class="flex flex-wrap items-center gap-1.5">
                                                                                @if ($penugasan->status_dl === 'Menunggu')
                                                                                    <span class="inline-flex px-2 py-0.5 text-[9px] font-bold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full border border-yellow-200 dark:border-yellow-800">MENUNGGU</span>
                                                                                @elseif ($penugasan->status_dl === 'ACC')
                                                                                    @if ($penugasan->sudahMasukKalenderDL())
                                                                                        <span class="inline-flex px-2 py-0.5 text-[9px] font-bold bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 rounded-full border border-teal-200 dark:border-teal-800 uppercase tracking-tighter">SUDAH DI KALENDER</span>
                                                                                    @else
                                                                                        <span class="inline-flex px-2 py-0.5 text-[9px] font-bold bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-full border border-orange-200 dark:border-orange-800">DITERIMA (BLM KALENDER)</span>
                                                                                    @endif
                                                                                @elseif ($penugasan->status_dl === 'Ditolak')
                                                                                    <span class="inline-flex px-2 py-0.5 text-[9px] font-bold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full border border-red-200 dark:border-red-800">DITOLAK</span>
                                                                                @else
                                                                                    <span class="inline-flex px-2 py-0.5 text-[9px] font-bold bg-gray-100 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 rounded-full border border-gray-200 dark:border-gray-600">PERLU VERIFIKASI</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    {{-- BLOK TRANSLOK --}}
                                                                    @if($penugasan->butuh_translok)
                                                                        <div class="grid grid-cols-[30px_1fr] items-start gap-2 pt-3 border-t border-gray-200 dark:border-gray-700/50 min-h-[38px]">
                                                                            <span class="text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase mt-1 tracking-widest border-r border-gray-200 dark:border-gray-700 pr-1">TRL</span>
                                                                            <div class="flex flex-wrap items-center gap-1.5">
                                                                                @if ($penugasan->status_translok === 'Menunggu')
                                                                                    <span class="inline-flex px-2 py-0.5 text-[9px] font-bold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full border border-yellow-200 dark:border-yellow-800">MENUNGGU</span>
                                                                                @elseif ($penugasan->status_translok === 'ACC')
                                                                                    @if ($penugasan->sudahMasukKalenderDL())
                                                                                        <span class="inline-flex px-2 py-0.5 text-[9px] font-bold bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 rounded-full border border-teal-200 dark:border-teal-800 uppercase tracking-tighter">SUDAH DI KALENDER</span>
                                                                                    @else
                                                                                        <span class="inline-flex px-2 py-0.5 text-[9px] font-bold bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-full border border-orange-200 dark:border-orange-800">DITERIMA (BLM KALENDER)</span>
                                                                                    @endif
                                                                                @elseif ($penugasan->status_translok === 'Ditolak')
                                                                                    <span class="inline-flex px-2 py-0.5 text-[9px] font-bold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full border border-red-200 dark:border-red-800">DITOLAK</span>
                                                                                @else
                                                                                    <span class="inline-flex px-2 py-0.5 text-[9px] font-bold bg-gray-100 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 rounded-full border border-gray-200 dark:border-gray-600">PERLU VERIFIKASI</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </td>

                                                            {{-- KOLOM AKSI --}}
                                                            <td class="px-4 py-3 align-top border border-gray-200 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/20">
                                                                <div class="space-y-4">
                                                                    {{-- AKSI DL --}}
                                                                    @if($penugasan->butuh_dl)
                                                                        <div class="flex flex-wrap items-center gap-1.5 min-h-[26px]">
                                                                            @if ($penugasan->status_dl === 'Ditolak')
                                                                                @if (Auth::user()->active_role === 'Ketua Tim' && Auth::user()->id_pegawai === $penugasan->subKegiatan->kegiatan->id_penanggung_jawab)
                                                                                    <button type="button" @click="$dispatch('open-smart-modal', { modalId: 'modal-verifikasi-dl', key: @js($penugasan->id_penugasan), data: { nama_pegawai: @js($penugasan->anggota->nama_pegawai), jenis_kegiatan: @js($penugasan->jenisKegiatan->jenis_kegiatan), tanggal_mulai: @js($penugasan->tanggal_mulai->format('d M Y')), tanggal_selesai: @js($penugasan->tanggal_selesai->format('d M Y')), } })" class="px-2.5 py-1 text-[10px] font-bold bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors shadow-sm w-full flex justify-center">Ajukan Kembali</button>
                                                                                @endif
                                                                                @if (Auth::user()->active_role === 'Pimpinan')
                                                                                    <span class="px-2 py-1 text-[9px] font-medium text-gray-400 dark:text-gray-500 italic">Menunggu Pengajuan</span>
                                                                                @endif
                                                                            @endif

                                                                            @can('acceptDL', $penugasan)
                                                                                @if ($penugasan->status_dl === 'ACC')
                                                                                    @if ($penugasan->sudahMasukKalenderDL())
                                                                                        @if (Auth::user()->active_role === 'Pimpinan')
                                                                                            <form id="del-dl-{{ $penugasan->id_penugasan }}" action="{{ route('kalenderDL.delete', $penugasan->id_penugasan) }}" method="POST" class="w-full">@csrf @method('DELETE')
                                                                                                <button type="button" onclick="SwalHelper.confirmDelete('del-dl-{{ $penugasan->id_penugasan }}', 'Kalender DL milik {{ $penugasan->anggota->nama_pegawai }}')" class="w-full flex items-center justify-center gap-1 px-2.5 py-1 text-[10px] font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded transition-colors shadow-sm" title="Hapus Kalender DL">
                                                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                                                                    HAPUS
                                                                                                </button>
                                                                                            </form>
                                                                                        @endif
                                                                                    @elseif (Auth::user()->active_role === 'Pimpinan')
                                                                                        <form action="{{ route('kalenderDL.store') }}" method="POST" class="w-full">@csrf
                                                                                            <input type="hidden" name="id_pegawai" value="{{ $penugasan->id_anggota }}"><input type="hidden" name="id_penugasan" value="{{ $penugasan->id_penugasan }}"><input type="hidden" name="tanggal_mulai" value="{{ $penugasan->tanggal_mulai }}"><input type="hidden" name="tanggal_selesai" value="{{ $penugasan->tanggal_selesai }}">
                                                                                            <button type="submit" class="w-full px-2.5 py-1 text-[10px] font-bold bg-teal-600 text-white rounded hover:bg-teal-700 transition-colors shadow-sm">+ Msk Kalender</button>
                                                                                        </form>
                                                                                    @endif
                                                                                @elseif (Auth::user()->active_role === 'Pimpinan' && in_array($penugasan->status_dl, ['Menunggu', null]))
                                                                                    <button type="button" @click="$dispatch('open-smart-modal', { modalId: 'modal-verifikasi-dl', key: @js($penugasan->id_penugasan), data: { nama_pegawai: @js($penugasan->anggota->nama_pegawai), jenis_kegiatan: @js($penugasan->jenisKegiatan->jenis_kegiatan), tanggal_mulai: @js($penugasan->tanggal_mulai->format('d M Y')), tanggal_selesai: @js($penugasan->tanggal_selesai->format('d M Y')), } })" class="w-full px-2.5 py-1 text-[10px] font-bold bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors shadow-sm">Verifikasi</button>
                                                                                @endif
                                                                            @endcan
                                                                        </div>
                                                                    @endif

                                                                    {{-- AKSI TRANSLOK --}}
                                                                    @if($penugasan->butuh_translok)
                                                                        <div class="flex flex-wrap items-center gap-1.5 pt-3 border-t border-gray-200 dark:border-gray-700/50 min-h-[38px]">
                                                                            @if ($penugasan->status_translok === 'Ditolak')
                                                                                @if (Auth::user()->active_role === 'Ketua Tim' && Auth::user()->id_pegawai === $penugasan->subKegiatan->kegiatan->id_penanggung_jawab)
                                                                                    <button type="button" @click="$dispatch('open-smart-modal', { modalId: 'modal-verifikasi-translok', key: @js($penugasan->id_penugasan), data: { nama_pegawai: @js($penugasan->anggota->nama_pegawai), jenis_kegiatan: @js($penugasan->jenisKegiatan->jenis_kegiatan), tanggal_mulai: @js($penugasan->tanggal_mulai->format('d M Y')), tanggal_selesai: @js($penugasan->tanggal_selesai->format('d M Y')), } })" class="px-2.5 py-1 text-[10px] font-bold bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors shadow-sm w-full flex justify-center">Ajukan Kembali</button>
                                                                                @endif
                                                                                @if (Auth::user()->active_role === 'Pimpinan')
                                                                                    <span class="px-2 py-1 text-[9px] font-medium text-gray-400 dark:text-gray-500 italic">Menunggu Pengajuan</span>
                                                                                @endif
                                                                            @endif

                                                                            @can('acceptTranslok', $penugasan)
                                                                                @if ($penugasan->status_translok === 'ACC')
                                                                                    @if ($penugasan->sudahMasukKalenderDL())
                                                                                        @if (Auth::user()->active_role === 'Pimpinan')
                                                                                            <form id="del-trl-{{ $penugasan->id_penugasan }}" action="{{ route('kalenderDL.delete', $penugasan->id_penugasan) }}" method="POST" class="w-full">@csrf @method('DELETE')
                                                                                                <button type="button" onclick="SwalHelper.confirmDelete('del-trl-{{ $penugasan->id_penugasan }}', 'Kalender Translok milik {{ $penugasan->anggota->nama_pegawai }}')" class="w-full flex items-center justify-center gap-1 px-2.5 py-1 text-[10px] font-bold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded transition-colors shadow-sm" title="Hapus Kalender Translok">
                                                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                                                                    HAPUS
                                                                                                </button>
                                                                                            </form>
                                                                                        @endif
                                                                                    @elseif (Auth::user()->active_role === 'Pimpinan')
                                                                                        <form action="{{ route('kalenderDL.store') }}" method="POST" class="w-full">@csrf
                                                                                            <input type="hidden" name="id_pegawai" value="{{ $penugasan->id_anggota }}"><input type="hidden" name="id_penugasan" value="{{ $penugasan->id_penugasan }}"><input type="hidden" name="tanggal_mulai" value="{{ $penugasan->tanggal_mulai }}"><input type="hidden" name="tanggal_selesai" value="{{ $penugasan->tanggal_selesai }}">
                                                                                            <button type="submit" class="w-full px-2.5 py-1 text-[10px] font-bold bg-teal-600 text-white rounded hover:bg-teal-700 transition-colors shadow-sm">+ Msk Kalender</button>
                                                                                        </form>
                                                                                    @endif
                                                                                @elseif (Auth::user()->active_role === 'Pimpinan' && in_array($penugasan->status_translok, ['Menunggu', null]))
                                                                                    <button type="button" @click="$dispatch('open-smart-modal', { modalId: 'modal-verifikasi-translok', key: @js($penugasan->id_penugasan), data: { nama_pegawai: @js($penugasan->anggota->nama_pegawai), jenis_kegiatan: @js($penugasan->jenisKegiatan->jenis_kegiatan), tanggal_mulai: @js($penugasan->tanggal_mulai->format('d M Y')), tanggal_selesai: @js($penugasan->tanggal_selesai->format('d M Y')), } })" class="w-full px-2.5 py-1 text-[10px] font-bold bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors shadow-sm">Verifikasi</button>
                                                                                @endif
                                                                            @endcan
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            @endforeach

                                            @if ($bidang->kegiatans->count() === 0)
                                                <tr>
                                                    <td colspan="8"
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
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Belum ada kegiatan untuk fungsi ini
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if ($bidangs->count() === 0)
                    <div
                        class="text-center py-8 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
                        <div
                            class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 mb-3">
                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
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