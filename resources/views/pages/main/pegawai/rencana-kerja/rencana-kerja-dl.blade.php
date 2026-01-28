@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{$title}}" />

    <div id="app" data-pegawais='@json($pegawais)'></div>

    <div class="space-y-6">
        <!-- Tampilan Card Fungsi dengan Accordion -->
        <x-common.component-card title="Daftar Rencana Kerja Butuh DL">
            <div class="space-y-4">
                @foreach ($bidangs as $index => $bidang)
                    <div x-data="{ open: false }" class="rounded-lg border border-gray-200 bg-white">
                        <!-- Header Fungsi -->
                        <button @click="open = !open"
                            class="flex w-full items-center justify-between p-4 text-left hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800">{{ $bidang->nama_bidang }}</h3>
                                    <p class="text-xs text-gray-500">
                                        Total {{ $bidang->kegiatans->count() }} kegiatan
                                    </p>
                                </div>
                            </div>
                            <svg :class="{ 'rotate-180': open }" class="h-5 w-5 text-gray-500 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Accordion Content -->
                        <div x-show="open" x-collapse class="border-t border-gray-100">
                            @if ($bidang->kegiatans->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full border border-gray-200 table-fixed">
                                        <colgroup>
                                            <col class="w-[15%]"> <!-- Kegiatan -->
                                            <col class="w-[15%]"> <!-- Sub Kegiatan -->
                                            <col class="w-[15%]"> <!-- Nama Pegawai -->
                                            <col class="w-[10%]"> <!-- Jenis Kegiatan -->
                                            <col class="w-[15%]"> <!-- Target dan Satuan -->
                                            <col class="w-[20%]"> <!-- Tanggal -->
                                            <col class="w-[10%]"> <!-- Status DL -->
                                        </colgroup>
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">
                                                    Kegiatan
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">
                                                    Sub Kegiatan
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">
                                                    Nama Anggota
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">
                                                    Jenis Kegiatan
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">
                                                    Target
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">
                                                    Waktu Pelaksanaan
                                                </th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border border-gray-200">
                                                    Status DL
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
                                                            class="{{ !($loop->parent->last && $loop->last) ? 'border-b border-gray-200' : '' }}">
                                                            <!-- Kolom Kegiatan (rowspan hanya untuk baris pertama tiap kegiatan) -->
                                                            @if ($subIndex === 0 && $penugasanIndex === 0)
                                                                <td class="px-4 py-3 align-top border border-gray-200"
                                                                    rowspan="{{ $kegiatanRowCount }}">
                                                                    <div class="flex flex-col">
                                                                        <span class="text-sm font-medium text-gray-800">
                                                                            {{ $kegiatan->nama_rk_kegiatan }}
                                                                        </span>
                                                                        <span class="text-xs text-gray-500 mt-1">
                                                                            Ketua:
                                                                            {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                            @endif

                                                            <!-- Kolom Sub Kegiatan (rowspan hanya untuk baris pertama tiap sub kegiatan) -->
                                                            @if ($penugasanIndex === 0)
                                                                <td class="px-4 py-3 text-sm text-gray-800 align-top border border-gray-200"
                                                                    rowspan="{{ $subRowCount }}">
                                                                    {{ $subKegiatan->nama_sub_kegiatan }}
                                                                </td>
                                                            @endif

                                                            <!-- Kolom Nama Pegawai -->
                                                            <td
                                                                class="px-4 py-3 text-sm text-gray-800 align-top border border-gray-200">
                                                                {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                                                            </td>

                                                            <!-- Kolom Jenis Kegiatan dengan Badge -->
                                                            <td
                                                                class="px-4 py-3 text-sm text-gray-800 align-top border border-gray-200">
                                                                {{ $penugasan->jenisKegiatan->jenis_kegiatan ?? '-' }}
                                                            </td>

                                                            <!-- Kolom Target -->
                                                            <td
                                                                class="px-4 py-3 text-sm text-gray-800 align-top border border-gray-200">
                                                                {{ $penugasan->target ?? '-' }} <spann class="text-orange-800">{{ $penugasan->satuan_target ?? '-' }}</spann>
                                                            </td>

                                                            <!-- Kolom Satuan -->
                                                            <td
                                                                class="px-4 py-3 text-sm text-gray-800 align-top border border-gray-200">
                                                                {{ $penugasan->tanggal_mulai?->translatedFormat('d M Y') ?? '-' }} - {{ $penugasan->tanggal_selesai?->translatedFormat('d M Y') ?? '-' }}
                                                            </td>
                                                            <td class="px-4 py-3 text-sm align-top border border-gray-200">
                                                                @if ($penugasan->status_dl === 'Menunggu')
                                                                    <span
                                                                        class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-800">
                                                                        Menunggu
                                                                    </span>
                                                                @elseif ($penugasan->status_dl === 'ACC')
                                                                    <span
                                                                        class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">
                                                                        ACC
                                                                    </span>
                                                                @elseif ($penugasan->status_dl === 'Ditolak')
                                                                    <span
                                                                        class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-800">
                                                                        Ditolak
                                                                    </span>
                                                                @else
                                                                    <span class="text-xs text-gray-400 italic">-</span>
                                                                @endif
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            @endforeach

                                            @if ($bidang->kegiatans->count() === 0)
                                                <tr>
                                                    <td colspan="6"
                                                        class="px-4 py-6 text-center text-sm text-gray-500 border border-gray-200">
                                                        Belum ada kegiatan untuk bidang ini
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-4 text-center border border-gray-200 border-t-0">
                                    <p class="text-sm text-gray-500 italic">Belum ada kegiatan untuk fungsi ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if ($bidangs->count() === 0)
                    <div class="text-center py-8 border border-gray-200 rounded-lg">
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 mb-3">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Belum ada fungsi/bidang yang dibuat</p>
                    </div>
                @endif
            </div>
        </x-common.component-card>
    </div>
@endsection
