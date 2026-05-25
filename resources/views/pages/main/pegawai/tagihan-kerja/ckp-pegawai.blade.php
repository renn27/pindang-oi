@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    <!-- Bagian Filter Bulan & Tahun -->
    <div
        class="flex flex-col sm:flex-row justify-between items-center rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 mb-6 dark:border-gray-800 dark:bg-gray-900">
        <!-- Form Filter - Kiri -->
        <form method="GET" action="{{ route('ckp.pegawai.index') }}"
            class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full sm:w-auto">
            <div class="flex items-center h-10">
                <label class="text-sm font-medium text-gray-700 whitespace-nowrap dark:text-gray-300">
                    Filter Data
                </label>
            </div>

            <!-- Dropdown Bulan -->
            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent w-full sm:w-auto">
                <select name="bulan"
                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full sm:w-36 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-4 pr-10 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500"
                    :class="isOptionSelected && 'text-gray-800'" @change="isOptionSelected = true">
                    <option value="all" {{ $bulan === 'all' ? 'selected' : '' }} class="text-gray-700 dark:text-gray-300">Semua Bulan</option>
                    @foreach ($bulanList as $key => $nama)
                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}
                            class="text-gray-700 dark:text-gray-300">
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
                <span
                    class="pointer-events-none absolute top-1/2 right-3.5 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                    <svg class="stroke-current" width="16" height="16" viewBox="0 0 20 20" fill="none">
                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </div>

            <!-- Dropdown Tahun -->
            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent w-full sm:w-auto">
                <select name="tahun"
                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full sm:w-36 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-4 pr-10 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500"
                    :class="isOptionSelected && 'text-gray-800'" @change="isOptionSelected = true">
                    @foreach ($tahunList as $thn)
                        <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}
                            class="text-gray-700 dark:text-gray-300">
                            {{ $thn }}
                        </option>
                    @endforeach
                </select>
                <span
                    class="pointer-events-none absolute top-1/2 right-3.5 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                    <svg class="stroke-current" width="16" height="16" viewBox="0 0 20 20" fill="none">
                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </div>

            <button type="submit"
                class="flex justify-center items-center rounded-lg bg-brand-500 px-5 py-2 text-sm font-medium text-white hover:bg-brand-600 w-full sm:w-auto h-10 whitespace-nowrap dark:bg-brand-600 dark:hover:bg-brand-700">
                Tampilkan
            </button>

            <a href="{{ route('ckp.pegawai.index') }}"
                class="flex justify-center items-center rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 w-full sm:w-auto h-10 whitespace-nowrap dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                Reset
            </a>
        </form>

        <!-- Tombol Export - Kanan -->
        @if ($bulan !== 'all')
            <div x-data="{ exportDisabled: false, countdown: 10, timer: null }" class="w-full sm:w-auto">
                <a href="{{ route('ckp.pegawai.export', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                    id="btn-export-excel"
                    x-on:click="if(exportDisabled) { $event.preventDefault(); return; } 
                               exportDisabled = true; 
                               countdown = 10;
                               timer = setInterval(() => { 
                                   if(countdown <= 1) { 
                                       clearInterval(timer); 
                                       exportDisabled = false; 
                                   } else {
                                       countdown--; 
                                   }
                               }, 1000)"
                    :class="exportDisabled ? 'opacity-60 cursor-not-allowed bg-gray-100 border-gray-300 text-gray-400 dark:bg-gray-800 dark:border-gray-700' : 'border-green-500 bg-white text-green-600 hover:bg-green-50 dark:border-green-600 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700'"
                    class="flex justify-center items-center gap-2 rounded-lg border px-5 py-2 text-sm font-medium w-full sm:w-auto h-10 whitespace-nowrap mt-4 sm:mt-0 transition-all duration-300"
                    :title="exportDisabled ? 'Tunggu ' + countdown + ' detik untuk export kembali' : 'Export data ke Excel'">
                    
                    <!-- Icon: Download vs Spinner -->
                    <template x-if="!exportDisabled">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </template>
                    <template x-if="exportDisabled">
                        <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>

                    <!-- Text: Label vs Countdown -->
                    <span x-text="exportDisabled ? 'Sabar, Tunggu (' + countdown + 's)' : 'Export Excel'"></span>
                </a>
            </div>
        @endif
    </div>

    <!-- Statistik Ringkasan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total CKP</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalCkp }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Target</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ number_format($totalTarget, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Periode</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $bulan === 'all' ? 'Semua Bulan' : $bulanList[$bulan] }}
                        {{ $tahun }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel CKP -->
    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="grid grid-cols-1">
            <div class="col-span-1 w-full overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th rowspan="2" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-10 align-middle border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">No.</th>
                            @if ($bulan === 'all')
                            <th rowspan="2" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20 align-middle border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">Bulan</th>
                            @endif
                            <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider align-middle border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">Uraian Kegiatan</th>
                            <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20 align-middle border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">Satuan</th>
                            <th colspan="3" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">Kuantitas</th>
                            <th rowspan="2" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24 align-middle border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">Tingkat Kualitas</th>
                            <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28 align-middle border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">Kode Butir Kegiatan</th>
                            <th rowspan="2" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24 align-middle border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">Angka Kredit</th>
                            <th rowspan="2" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36 align-middle border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">Keterangan</th>
                            <th rowspan="2" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-14 align-middle border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">Aksi</th>
                        </tr>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">Target</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">Realisasi</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">%</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        @php
                            $isNotRealPimpinan = Auth::user()->active_role === 'Pimpinan' 
                                && (Auth::user()->nama_pegawai !== 'Sukendro Suryo Wiguno, SST, M.Ec.Dev' || !str_contains(Auth::user()->jabatan, 'Kepala BPS Ogan Ilir'));
                        @endphp
                        
                        @if ($isNotRealPimpinan)
                            <tr>
                                <td colspan="{{ $bulan === 'all' ? '12' : '11' }}" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="rounded-full bg-red-100 p-3 mb-3 dark:bg-red-900/30">
                                            <svg class="w-8 h-8 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <p class="text-base font-medium text-gray-800 dark:text-gray-200">Akses CKP Pimpinan Dibatasi</p>
                                        <p class="text-sm text-red-600 dark:text-red-400 mt-1 max-w-lg mx-auto">
                                            Hanya Pak Sukendro (Kepala BPS Ogan Ilir) yang bisa mengakses CKP Pimpinan, meskipun Anda memiliki hak akses role Pimpinan.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @elseif ($ckpList->isEmpty())
                            <tr>
                                <td colspan="{{ $bulan === 'all' ? '12' : '11' }}" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                                        </svg>
                                        <p class="text-base font-medium text-gray-500 dark:text-gray-400">Belum ada data CKP</p>
                                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Belum ada CKP yang tercatat untuk periode {{ $bulan === 'all' ? 'Semua Bulan' : $bulanList[$bulan] }} {{ $tahun }}</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            {{-- SEPARATOR: UTAMA --}}
                            <tr class="bg-gray-100 dark:bg-gray-800">
                                <td colspan="{{ $bulan === 'all' ? '12' : '11' }}" class="px-4 py-2 text-xs font-bold text-gray-600 uppercase tracking-wider dark:text-gray-400">UTAMA</td>
                            </tr>

                            @php $noUtama = 1; @endphp
                            @forelse ($ckpList->where('jenis_ckp', 'Utama') as $ckp)
                                @php
                                    $isPimpinanCkp = !$ckp->penugasan && !$ckp->id_sub_kegiatan && $ckp->id_agenda;
                                    $isKetuaTimCkp = !$ckp->penugasan && $ckp->id_sub_kegiatan && !$ckp->id_agenda;
                                    $isAnggotaCkp  = $ckp->penugasan;
                                    
                                    // Data penugasan untuk modal
                                    $penugasanData = $ckp->penugasan ? [
                                        'jenis_kegiatan' => $ckp->penugasan->jenisKegiatan?->jenis_kegiatan ?? 'Isi Sendiri',
                                        'target' => $ckp->penugasan->target,
                                        'satuan_target' => $ckp->penugasan->satuan_target,
                                        'tanggal_mulai' => optional($ckp->penugasan->tanggal_mulai)->translatedFormat('d M Y'),
                                        'tanggal_selesai' => optional($ckp->penugasan->tanggal_selesai)->translatedFormat('d M Y'),
                                        'bulan_pengiriman' => $ckp->penugasan->latestPengiriman?->bulan_pengiriman ? \Carbon\Carbon::createFromFormat('Y-m', $ckp->penugasan->latestPengiriman->bulan_pengiriman)->translatedFormat('F Y') : '-',
                                        'tanggal_pengiriman' => optional($ckp->penugasan->latestPengiriman?->tanggal_pengiriman)->translatedFormat('d F Y'),
                                    ] : null;
                                    
                                    // Data sub kegiatan untuk modal
                                    $subKegiatanData = $ckp->subKegiatan ? [
                                        'nama_sub_kegiatan' => $ckp->subKegiatan->nama_sub_kegiatan,
                                        'kegiatan' => ['nama_rk_kegiatan' => $ckp->subKegiatan->kegiatan->nama_rk_kegiatan ?? '-']
                                    ] : null;

                                    // Data Agenda untuk modal
                                    $agendaData = $ckp->agendaPimpinan ? [
                                        'nama_agenda' => $ckp->agendaPimpinan->nama_agenda,
                                    ] : null;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 {{ $isPimpinanCkp ? 'bg-purple-50/30 dark:bg-purple-900/10' : ($isKetuaTimCkp ? 'bg-green-50/30 dark:bg-green-900/10' : '') }}">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center dark:text-gray-300">{{ $noUtama++ }}</td>
                                    @if ($bulan === 'all')
                                    <td class="px-4 py-3 text-sm text-center font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        @php
                                            $bulanCkpValue = $ckp->bulan_ckp; // format "YYYY-MM"
                                            $bulanIndex = $bulanCkpValue ? str_pad((int) substr($bulanCkpValue, 5, 2), 2, '0', STR_PAD_LEFT) : null;
                                            $bulanLabel = $bulanIndex ? ($bulanList[$bulanIndex] ?? '-') : '-';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $isPimpinanCkp ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : ($isKetuaTimCkp ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300') }}">
                                            {{ $bulanLabel }}
                                        </span>
                                    </td>
                                    @endif
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        <div class="flex items-start gap-2">
                                            <span>{{ $ckp->uraian }}</span>
                                            @if($isPimpinanCkp)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 border border-purple-200 dark:border-purple-800 whitespace-nowrap">
                                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Pimpinan
                                                </span>
                                            @elseif($isKetuaTimCkp)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800 whitespace-nowrap">
                                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Ketua Tim
                                                </span>
                                            @elseif($isAnggotaCkp)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800 whitespace-nowrap">
                                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                    Anggota
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $ckp->satuan ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-center font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ number_format($ckp->target_kuantitas, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-center font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ $ckp->realisasi !== null ? number_format($ckp->realisasi, 0, ',', '.') : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                        @if ($ckp->persentase_realisasi !== null)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $ckp->persentase_realisasi >= 100 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($ckp->persentase_realisasi >= 75 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                                                {{ number_format($ckp->persentase_realisasi, 1, ',', '.') }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                        @if ($ckp->tingkat_kualitas !== null)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $ckp->tingkat_kualitas >= 4 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($ckp->tingkat_kualitas >= 3 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                                                {{ $ckp->tingkat_kualitas }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $ckp->kode_butir_kegiatan ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-center font-semibold text-green-600 dark:text-green-400 whitespace-nowrap">{{ $ckp->angka_kredit ? number_format($ckp->angka_kredit, 2, ',', '.') : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        <div class="w-36 truncate" title="{{ $ckp->keterangan }}">{{ $ckp->keterangan ? Str::limit($ckp->keterangan, 50) : '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-block" x-data="{
                                            showDropdown: false,
                                            dropdownPosition: { x: 0, y: 0 },
                                            openDropdown(event) {
                                                const button = event.currentTarget;
                                                const rect = button.getBoundingClientRect();
                                                const dropdownWidth = 192;
                                                
                                                this.dropdownPosition = {
                                                    x: rect.left - dropdownWidth + 10,
                                                    y: rect.top - 10
                                                };
                                                this.showDropdown = true;
                                            },
                                            closeDropdown() {
                                                this.showDropdown = false;
                                            }}" x-on:mouseleave="closeDropdown()">

                                            <button x-on:mouseenter="openDropdown($event)"
                                                class="inline-flex items-center gap-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <svg class="fill-current" width="16" height="16" viewBox="0 0 18 18" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" />
                                                </svg>
                                                Aksi
                                            </button>

                                            <div x-show="showDropdown" x-transition
                                                class="fixed z-[9999] bg-white dark:bg-gray-800 rounded shadow-xl border border-gray-200 dark:border-gray-700 min-w-[192px]"
                                                :style="`left: ${dropdownPosition.x}px; top: ${dropdownPosition.y}px;`"
                                                x-on:mouseenter="showDropdown = true" x-on:mouseleave="closeDropdown()">

                                                <button class="w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-2 border-b border-gray-100 dark:border-gray-700"
                                                    @click="$dispatch('open-smart-modal', { modalId: 'modal-detail-ckp', data: { id_ckp: '{{ $ckp->id_ckp }}', uraian: {{ json_encode($ckp->uraian) }}, jenis_ckp: '{{ $ckp->jenis_ckp }}', target_kuantitas: '{{ $ckp->target_kuantitas }}', satuan: '{{ $ckp->satuan }}', realisasi: '{{ $ckp->realisasi }}', persentase_realisasi: '{{ $ckp->persentase_realisasi }}', tingkat_kualitas: '{{ $ckp->tingkat_kualitas }}', kode_butir_kegiatan: '{{ $ckp->kode_butir_kegiatan }}', angka_kredit: '{{ $ckp->angka_kredit }}', keterangan: {{ json_encode($ckp->keterangan) }}, created_at: '{{ $ckp->created_at->translatedFormat('d F Y H:i') }}', is_ketua_tim: {{ $isKetuaTimCkp ? 'true' : 'false' }}, is_pimpinan: {{ $isPimpinanCkp ? 'true' : 'false' }}, penugasan: {{ json_encode($penugasanData) }}, sub_kegiatan: {{ json_encode($subKegiatanData) }}, agenda: {{ json_encode($agendaData) }} } })">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Detail
                                                </button>
                                                <button class="w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-2 border-b border-gray-100 dark:border-gray-700"
                                                    @click="$dispatch('open-smart-modal', { modalId: 'modal-edit-ckp', data: { id_ckp: '{{ $ckp->id_ckp }}', uraian: {{ json_encode($ckp->uraian) }}, jenis_ckp: '{{ $ckp->jenis_ckp }}', keterangan: {{ json_encode($ckp->keterangan) }} } })">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </button>
                                                <form id="delete-ckp-{{ $ckp->id_ckp }}" action="{{ route('ckp.pegawai.delete', $ckp->id_ckp) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                        onclick="SwalHelper.confirmDelete('delete-ckp-{{ $ckp->id_ckp }}', {{ json_encode('CKP ' . ($ckp->bulan_ckp ? $bulanList[str_pad((int)substr($ckp->bulan_ckp, 5, 2), 2, '0', STR_PAD_LEFT)] . ' ' . substr($ckp->bulan_ckp, 0, 4) : 'ini')) }})"
                                                        class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ $bulan === 'all' ? '12' : '11' }}" class="px-4 py-3 text-sm text-center text-gray-400 italic dark:text-gray-500">Tidak ada data utama</td></tr>
                            @endforelse

                            {{-- SEPARATOR: TAMBAHAN --}}
                            <tr class="bg-gray-100 dark:bg-gray-800">
                                <td colspan="{{ $bulan === 'all' ? '12' : '11' }}" class="px-4 py-2 text-xs font-bold text-gray-600 uppercase tracking-wider dark:text-gray-400">TAMBAHAN</td>
                            </tr>

                            @php $noTambahan = 1; @endphp
                            @forelse ($ckpList->where('jenis_ckp', 'Tambahan') as $ckp)
                                @php
                                    $isPimpinanCkp = !$ckp->penugasan && !$ckp->id_sub_kegiatan && $ckp->id_agenda;
                                    $isKetuaTimCkp = !$ckp->penugasan && $ckp->id_sub_kegiatan && !$ckp->id_agenda;
                                    $isAnggotaCkp  = $ckp->penugasan;
                                    
                                    // Data penugasan untuk modal
                                    $penugasanData = $ckp->penugasan ? [
                                        'jenis_kegiatan' => $ckp->penugasan->jenisKegiatan?->jenis_kegiatan ?? 'Isi Sendiri',
                                        'target' => $ckp->penugasan->target,
                                        'satuan_target' => $ckp->penugasan->satuan_target,
                                        'tanggal_mulai' => optional($ckp->penugasan->tanggal_mulai)->translatedFormat('d M Y'),
                                        'tanggal_selesai' => optional($ckp->penugasan->tanggal_selesai)->translatedFormat('d M Y'),
                                        'bulan_pengiriman' => $ckp->penugasan->latestPengiriman?->bulan_pengiriman ? \Carbon\Carbon::createFromFormat('Y-m', $ckp->penugasan->latestPengiriman->bulan_pengiriman)->translatedFormat('F Y') : '-',
                                        'tanggal_pengiriman' => optional($ckp->penugasan->latestPengiriman?->tanggal_pengiriman)->translatedFormat('d F Y'),
                                    ] : null;
                                    
                                    // Data sub kegiatan untuk modal
                                    $subKegiatanData = $ckp->subKegiatan ? [
                                        'nama_sub_kegiatan' => $ckp->subKegiatan->nama_sub_kegiatan,
                                        'kegiatan' => ['nama_rk_kegiatan' => $ckp->subKegiatan->kegiatan->nama_rk_kegiatan ?? '-']
                                    ] : null;

                                    // Data Agenda untuk modal
                                    $agendaData = $ckp->agendaPimpinan ? [
                                        'nama_agenda' => $ckp->agendaPimpinan->nama_agenda,
                                    ] : null;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 {{ $isPimpinanCkp ? 'bg-purple-50/30 dark:bg-purple-900/10' : ($isKetuaTimCkp ? 'bg-green-50/30 dark:bg-green-900/10' : '') }}">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center dark:text-gray-300">{{ $noTambahan++ }}</td>
                                    @if ($bulan === 'all')
                                    <td class="px-4 py-3 text-sm text-center font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        @php
                                            $tglKirim = $ckp->penugasan ? $ckp->penugasan->latestPengiriman?->tanggal_pengiriman : $ckp->created_at;
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $isPimpinanCkp ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : ($isKetuaTimCkp ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300') }}">
                                            {{ $tglKirim ? $bulanList[$tglKirim->format('m')] ?? '-' : '-' }}
                                        </span>
                                    </td>
                                    @endif
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        <div class="flex items-start gap-2">
                                            <span>{{ $ckp->uraian }}</span>
                                            @if($isPimpinanCkp)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 border border-purple-200 dark:border-purple-800 whitespace-nowrap">
                                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Pimpinan
                                                </span>
                                            @elseif($isKetuaTimCkp)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800 whitespace-nowrap">
                                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Ketua Tim
                                                </span>
                                            @elseif($isAnggotaCkp)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800 whitespace-nowrap">
                                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                    Anggota
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $ckp->satuan ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-center font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ number_format($ckp->target_kuantitas, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-center font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ $ckp->realisasi !== null ? number_format($ckp->realisasi, 0, ',', '.') : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                        @if ($ckp->persentase_realisasi !== null)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $ckp->persentase_realisasi >= 100 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($ckp->persentase_realisasi >= 75 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                                                {{ number_format($ckp->persentase_realisasi, 1, ',', '.') }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                        @if ($ckp->tingkat_kualitas !== null)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $ckp->tingkat_kualitas >= 4 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($ckp->tingkat_kualitas >= 3 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                                                {{ $ckp->tingkat_kualitas }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $ckp->kode_butir_kegiatan ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-center font-semibold text-green-600 dark:text-green-400 whitespace-nowrap">{{ $ckp->angka_kredit ? number_format($ckp->angka_kredit, 2, ',', '.') : '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        <div class="w-36 truncate" title="{{ $ckp->keterangan }}">{{ $ckp->keterangan ? Str::limit($ckp->keterangan, 50) : '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="relative inline-block" x-data="{
                                            showDropdown: false,
                                            dropdownPosition: { x: 0, y: 0 },
                                            openDropdown(event) {
                                                const button = event.currentTarget;
                                                const rect = button.getBoundingClientRect();
                                                const dropdownWidth = 192;
                                                
                                                this.dropdownPosition = {
                                                    x: rect.left - dropdownWidth + 10,
                                                    y: rect.top - 10
                                                };
                                                this.showDropdown = true;
                                            },
                                            closeDropdown() {
                                                this.showDropdown = false;
                                            }}" x-on:mouseleave="closeDropdown()">

                                            <button x-on:mouseenter="openDropdown($event)"
                                                class="inline-flex items-center gap-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <svg class="fill-current" width="16" height="16" viewBox="0 0 18 18" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" />
                                                </svg>
                                                Aksi
                                            </button>

                                            <div x-show="showDropdown" x-transition
                                                class="fixed z-[9999] bg-white dark:bg-gray-800 rounded shadow-xl border border-gray-200 dark:border-gray-700 min-w-[192px]"
                                                :style="`left: ${dropdownPosition.x}px; top: ${dropdownPosition.y}px;`"
                                                x-on:mouseenter="showDropdown = true" x-on:mouseleave="closeDropdown()">

                                                <button class="w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-2 border-b border-gray-100 dark:border-gray-700"
                                                    @click="$dispatch('open-smart-modal', { modalId: 'modal-detail-ckp', data: { id_ckp: '{{ $ckp->id_ckp }}', uraian: {{ json_encode($ckp->uraian) }}, jenis_ckp: '{{ $ckp->jenis_ckp }}', target_kuantitas: '{{ $ckp->target_kuantitas }}', satuan: '{{ $ckp->satuan }}', realisasi: '{{ $ckp->realisasi }}', persentase_realisasi: '{{ $ckp->persentase_realisasi }}', tingkat_kualitas: '{{ $ckp->tingkat_kualitas }}', kode_butir_kegiatan: '{{ $ckp->kode_butir_kegiatan }}', angka_kredit: '{{ $ckp->angka_kredit }}', keterangan: {{ json_encode($ckp->keterangan) }}, created_at: '{{ $ckp->created_at->translatedFormat('d F Y H:i') }}', is_ketua_tim: {{ $isKetuaTimCkp ? 'true' : 'false' }}, is_pimpinan: {{ $isPimpinanCkp ? 'true' : 'false' }}, penugasan: {{ json_encode($penugasanData) }}, sub_kegiatan: {{ json_encode($subKegiatanData) }}, agenda: {{ json_encode($agendaData) }} } })">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Detail
                                                </button>
                                                <button class="w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-2 border-b border-gray-100 dark:border-gray-700"
                                                    @click="$dispatch('open-smart-modal', { modalId: 'modal-edit-ckp', data: { id_ckp: '{{ $ckp->id_ckp }}', uraian: {{ json_encode($ckp->uraian) }}, jenis_ckp: '{{ $ckp->jenis_ckp }}', keterangan: {{ json_encode($ckp->keterangan) }} } })">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </button>
                                                <form id="delete-ckp-tambahan-{{ $ckp->id_ckp }}" action="{{ route('ckp.pegawai.delete', $ckp->id_ckp) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                        onclick="SwalHelper.confirmDelete('delete-ckp-tambahan-{{ $ckp->id_ckp }}', {{ json_encode('CKP ' . ($ckp->bulan_ckp ? $bulanList[str_pad((int)substr($ckp->bulan_ckp, 5, 2), 2, '0', STR_PAD_LEFT)] . ' ' . substr($ckp->bulan_ckp, 0, 4) : 'ini')) }})"
                                                        class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ $bulan === 'all' ? '12' : '11' }}" class="px-4 py-3 text-sm text-center text-gray-400 italic dark:text-gray-500">Tidak ada data tambahan</td></tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Detail CKP --}}
    <div x-data="{ detailData: { uraian: '', jenis_ckp: '', target_kuantitas: '', satuan: '', realisasi: '', persentase_realisasi: '', tingkat_kualitas: '', kode_butir_kegiatan: '', angka_kredit: '', keterangan: '', created_at: '', is_ketua_tim: false, is_pimpinan: false, penugasan: null, sub_kegiatan: null, agenda: null } }"
        @open-smart-modal.window="if ($event.detail.modalId !== 'modal-detail-ckp') return; detailData = { uraian: $event.detail.data?.uraian ?? '', jenis_ckp: $event.detail.data?.jenis_ckp ?? '', target_kuantitas: $event.detail.data?.target_kuantitas ?? '', satuan: $event.detail.data?.satuan ?? '', realisasi: $event.detail.data?.realisasi ?? '', persentase_realisasi: $event.detail.data?.persentase_realisasi ?? '', tingkat_kualitas: $event.detail.data?.tingkat_kualitas ?? '', kode_butir_kegiatan: $event.detail.data?.kode_butir_kegiatan ?? '', angka_kredit: $event.detail.data?.angka_kredit ?? '', keterangan: $event.detail.data?.keterangan ?? '', created_at: $event.detail.data?.created_at ?? '', is_ketua_tim: $event.detail.data?.is_ketua_tim ?? false, is_pimpinan: $event.detail.data?.is_pimpinan ?? false, penugasan: $event.detail.data?.penugasan ?? null, sub_kegiatan: $event.detail.data?.sub_kegiatan ?? null, agenda: $event.detail.data?.agenda ?? null };">
        <x-ui.smart-modal id="modal-detail-ckp" class="max-w-2xl">
            <div class="relative flex flex-col bg-white dark:bg-gray-900 dark:border dark:border-gray-800 rounded-3xl overflow-hidden" style="max-height: 85vh;">
                <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <h4 class="text-2xl font-semibold text-gray-800 dark:text-white">Detail CKP</h4>
                        <template x-if="detailData.is_pimpinan">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Pimpinan
                            </span>
                        </template>
                        <template x-if="detailData.is_ketua_tim">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Ketua Tim
                            </span>
                        </template>
                        <template x-if="!detailData.is_ketua_tim && !detailData.is_pimpinan">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                Anggota Tim
                            </span>
                        </template>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informasi lengkap Catatan Kinerja Pegawai</p>
                </div>
                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4 custom-scrollbar dark:bg-gray-900">
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-800 p-4">
                        <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Uraian Kegiatan</p>
                        <p class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed" x-text="detailData.uraian || '-'"></p>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 p-3">
                            <p class="text-xs font-medium text-blue-500 dark:text-blue-400 mb-1">Jenis CKP</p>
                            <template x-if="detailData.jenis_ckp === 'Utama'"><p class="text-sm font-semibold text-blue-700 dark:text-blue-300">Utama</p></template>
                            <template x-if="detailData.jenis_ckp === 'Tambahan'"><p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tambahan</p></template>
                            <template x-if="!detailData.jenis_ckp"><p class="text-sm text-gray-500">-</p></template>
                        </div>
                        <div class="rounded-xl bg-gray-50 dark:bg-gray-800 p-3">
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 mb-1">Satuan</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200" x-text="detailData.satuan || '-'"></p>
                        </div>
                        <div class="rounded-xl bg-green-50 dark:bg-green-900/20 p-3">
                            <p class="text-xs font-medium text-green-500 dark:text-green-400 mb-1">Angka Kredit</p>
                            <p class="text-sm font-semibold text-green-700 dark:text-green-300" x-text="detailData.angka_kredit || '-'"></p>
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kuantitas</p>
                        </div>
                        <div class="grid grid-cols-3 divide-x divide-gray-200 dark:divide-gray-700">
                            <div class="p-3 text-center"><p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Target</p><p class="text-sm font-semibold text-gray-800 dark:text-gray-200"><span x-text="detailData.target_kuantitas || '0'"></span><span class="text-xs font-normal text-gray-400" x-text="' ' + (detailData.satuan || '')"></span></p></div>
                            <div class="p-3 text-center"><p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Realisasi</p><p class="text-sm font-semibold text-gray-800 dark:text-gray-200" x-text="detailData.realisasi || '-'"></p></div>
                            <div class="p-3 text-center"><p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Persentase</p><p class="text-sm font-semibold text-gray-800 dark:text-gray-200" x-text="detailData.persentase_realisasi ? detailData.persentase_realisasi + '%' : '-'"></p></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-xl bg-purple-50 dark:bg-purple-900/20 p-3"><p class="text-xs font-medium text-purple-500 dark:text-purple-400 mb-1">Tingkat Kualitas</p><p class="text-sm font-semibold text-purple-700 dark:text-purple-300" x-text="detailData.tingkat_kualitas || '-'"></p></div>
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3"><p class="text-xs font-medium text-gray-400 dark:text-gray-500 mb-1">Kode Butir Kegiatan</p><p class="text-sm font-mono text-gray-800 dark:text-gray-200" x-text="detailData.kode_butir_kegiatan || '-'"></p></div>
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-3"><p class="text-xs font-medium text-gray-400 dark:text-gray-500 mb-1">Tanggal Dibuat</p><p class="text-sm text-gray-800 dark:text-gray-200" x-text="detailData.created_at || '-'"></p></div>
                    </div>
                    <div x-show="detailData.keterangan && detailData.keterangan !== ''" class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                        <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Keterangan</p>
                        <p class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed" x-text="detailData.keterangan"></p>
                    </div>
                    <div x-show="detailData.is_ketua_tim && detailData.sub_kegiatan !== null" class="rounded-xl border border-green-200 dark:border-green-800 overflow-hidden">
                        <div class="px-4 py-2.5 bg-green-50 dark:bg-green-900/20 border-b border-green-200 dark:border-green-800">
                            <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Informasi Sub Kegiatan (CKP Ketua Tim)
                            </p>
                        </div>
                        <div class="p-4 space-y-3">
                            <div><p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Sub Kegiatan</p><p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="detailData.sub_kegiatan?.nama_sub_kegiatan || '-'"></p></div>
                            <div><p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Kegiatan</p><p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="detailData.sub_kegiatan?.kegiatan?.nama_rk_kegiatan || '-'"></p></div>
                        </div>
                    </div>
                    
                    <div x-show="detailData.is_pimpinan && detailData.agenda !== null" class="rounded-xl border border-purple-200 dark:border-purple-800 overflow-hidden">
                        <div class="px-4 py-2.5 bg-purple-50 dark:bg-purple-900/20 border-b border-purple-200 dark:border-purple-800">
                            <p class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Informasi Agenda (CKP Pimpinan)
                            </p>
                        </div>
                        <div class="p-4 space-y-3">
                            <div><p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Agenda Pimpinan</p><p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="detailData.agenda?.nama_agenda || '-'"></p></div>
                        </div>
                    </div>

                    <div x-show="!detailData.is_ketua_tim && !detailData.is_pimpinan && detailData.penugasan !== null" class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700"><p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Informasi Penugasan</p></div>
                        <div class="p-4 grid grid-cols-2 gap-4">
                            <div class="col-span-2"><p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Jenis Kegiatan</p><p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="detailData.penugasan?.jenis_kegiatan || '-'"></p></div>
                            <div><p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Target Penugasan</p><p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="(detailData.penugasan?.target || '') + ' ' + (detailData.penugasan?.satuan_target || '')"></p></div>
                            <div><p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Periode</p><p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="(detailData.penugasan?.tanggal_mulai || '') + ' s.d ' + (detailData.penugasan?.tanggal_selesai || '')"></p></div>
                            <div><p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Bulan Pengiriman</p><p class="text-sm font-semibold text-gray-800 dark:text-gray-200" x-text="detailData.penugasan?.bulan_pengiriman || '-'"></p></div>
                            <div><p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Tanggal Pengiriman</p><p class="text-sm font-semibold text-green-600 dark:text-green-400" x-text="detailData.penugasan?.tanggal_pengiriman || '-'"></p></div>
                        </div>
                    </div>
                </div>
                <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-700">
                    <div class="flex justify-end">
                        <button type="button" @click="open = false" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Tutup</button>
                    </div>
                </div>
            </div>
        </x-ui.smart-modal>
    </div>

    {{-- Modal Edit CKP --}}
    <div x-data="{ editData: { id_ckp: '', uraian: '', jenis_ckp: 'Utama', keterangan: '' } }"
        @open-smart-modal.window="if ($event.detail.modalId !== 'modal-edit-ckp') return; editData = { id_ckp: $event.detail.data?.id_ckp ?? '', uraian: $event.detail.data?.uraian ?? '', jenis_ckp: $event.detail.data?.jenis_ckp ?? 'Utama', keterangan: $event.detail.data?.keterangan ?? '' };">
        <x-ui.smart-modal id="modal-edit-ckp" class="max-w-2xl">
            <div class="relative flex h-auto w-full max-w-2xl flex-col overflow-hidden rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">
                <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
                    <h4 class="text-2xl font-semibold text-gray-800 dark:text-white">Edit CKP</h4>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Edit data Catatan Kinerja Pegawai</p>
                </div>
                <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900">
                    <form method="POST" :action="'{{ url('ckp-pegawai') }}/' + editData.id_ckp" class="grid grid-cols-1 gap-y-5">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Uraian Kegiatan <span class="text-red-500">*</span></label>
                            <textarea name="uraian" x-model="editData.uraian" rows="4" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Masukkan uraian kegiatan..."></textarea>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis CKP</label>
                            <select name="jenis_ckp" x-model="editData.jenis_ckp" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <option value="Utama">Utama</option>
                                <option value="Tambahan">Tambahan</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
                            <textarea name="keterangan" x-model="editData.keterangan" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" placeholder="Isi keterangan jika diperlukan..."></textarea>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button @click="open = false" type="button" class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Batal</button>
                            <button type="submit" class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto dark:bg-brand-600 dark:hover:bg-brand-700">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </x-ui.smart-modal>
    </div>
@endsection
