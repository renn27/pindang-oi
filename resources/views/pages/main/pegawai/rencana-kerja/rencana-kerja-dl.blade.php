@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{$title}}" />

    <div id="app" data-pegawais='@json($pegawais)'></div>

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

            <div class="rounded-3xl bg-white">

                <!-- HEADER -->
                <div class="border-b px-6 py-4">
                    <h4 class="text-lg font-semibold text-gray-800">
                        Verifikasi Dinas Luar
                    </h4>
                    <p class="text-sm text-gray-500">
                        Review singkat sebelum mengambil keputusan
                    </p>
                </div>

                <!-- BODY -->
                <div class="px-6 py-5 space-y-3 text-sm text-gray-700">
                    <div>
                        <span class="font-medium">Nama Pegawai:</span><br>
                        <span x-text="formData.nama_pegawai"></span>
                    </div>

                    <div>
                        <span class="font-medium">Jenis Kegiatan:</span><br>
                        <span x-text="formData.jenis_kegiatan"></span>
                    </div>

                    <div>
                        <span class="font-medium">Waktu Pelaksanaan:</span><br>
                        <span x-text="formData.tanggal_mulai"></span>
                        –
                        <span x-text="formData.tanggal_selesai"></span>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="border-t px-6 py-4 flex justify-end gap-2">
                    <button
                        type="button"
                        @click="open = false"
                        class="rounded-lg border px-4 py-2 text-sm">
                        Batal
                    </button>

                    @if (Auth::user()->active_role === 'Pimpinan')
                        <button
                            type="submit"
                            name="status_dl"
                            value="Ditolak"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">
                            Tolak
                        </button>

                        <button
                            type="submit"
                            name="status_dl"
                            value="ACC"
                            class="rounded-lg bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700">
                            Setujui
                        </button>
                    @endif

                    @if (Auth::user()->active_role === 'Ketua Tim')
                        <button
                            type="submit"
                            name="status_dl"
                            value="Menunggu"
                            class="rounded-lg bg-orange-600 px-4 py-2 text-sm text-white hover:bg-orange-700">
                            Ajukan Kembali
                        </button>
                    @endif
                </div>

            </div>
        </form>
    </x-ui.smart-modal>

    <div class="space-y-6">
        <!-- Tampilan Card Fungsi dengan Accordion -->
        <x-common.component-card title="Daftar Rencana Kerja Butuh DL">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="rounded-xl border bg-white p-4">
                    <p class="text-sm text-gray-500">Total Rencana Kerja</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-800">
                        {{ $allPenugasans->count() }}
                    </p>
                </div>

                <div class="rounded-xl border bg-white p-4">
                    <p class="text-sm text-gray-500">Menunggu Verifikasi</p>
                    <p class="mt-1 text-2xl font-semibold text-yellow-600">
                        {{ $allPenugasans->where('status_dl', 'Menunggu')->count() }}
                    </p>
                </div>

                <div class="rounded-xl border bg-white p-4">
                    <p class="text-sm text-gray-500">Diterima</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">
                        {{ $allPenugasans->where('status_dl', 'ACC')->count() }}
                    </p>
                </div>

                <div class="rounded-xl border bg-white p-4">
                    <p class="text-sm text-gray-500">Ditolak</p>
                    <p class="mt-1 text-2xl font-semibold text-red-600">
                        {{ $allPenugasans->where('status_dl', 'Ditolak')->count() }}
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                @foreach ($bidangs as $index => $bidang)
                    <div x-data="{ open: false }" class="rounded-lg border border-gray-200 bg-white">
                        <!-- Header Fungsi -->
                        <button @click="open = !open"
                            class="flex w-full items-center justify-between p-4 text-left hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                        {{ $bidang->nama_bidang }}
                                        {{-- Untuk Pimpinan --}}
                                        @if(Auth::user()->active_role === 'Pimpinan' && $bidang->menungguCount > 0)
                                            <span class="animate-pulse inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                                                {{ $bidang->menungguCount }} menunggu verifikasi
                                            </span>
                                        @endif

                                        {{-- Untuk Ketua Tim --}}
                                        @if(Auth::user()->active_role === 'Ketua Tim' && $bidang->ditolakCount > 0)
                                            <span class="animate-pulse inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                                {{ $bidang->ditolakCount }} ditolak
                                            </span>
                                        @endif
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1">
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
                                            <col class="w-[20%]"> <!-- Sub Kegiatan -->
                                            <col class="w-[15%]"> <!-- Nama Pegawai -->
                                            <col class="w-[10%]"> <!-- Jenis Kegiatan -->
                                            <col class="w-[10%]"> <!-- Target dan Satuan -->
                                            <col class="w-[15%]"> <!-- Tanggal -->
                                            <col class="w-[15%]"> <!-- Status DL -->
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
                                                                {{ $penugasan->target ?? '-' }} <span class="text-orange-800">({{ $penugasan->satuan_target ?? '-' }})</span>
                                                            </td>

                                                            <!-- Kolom Satuan -->
                                                            <td
                                                                class="px-4 py-3 text-sm text-gray-800 align-top border border-gray-200">
                                                                {{ $penugasan->tanggal_mulai?->translatedFormat('d M Y') ?? '-' }} - {{ $penugasan->tanggal_selesai?->translatedFormat('d M Y') ?? '-' }}
                                                            </td>

                                                            <td class="px-4 py-3 text-sm align-top border border-gray-200">
                                                                <div class="flex items-center gap-2">
                                                                    {{-- BADGE STATUS --}}
                                                                    @if ($penugasan->status_dl === 'Menunggu')
                                                                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-1
                                                                            text-xs font-medium text-yellow-800">
                                                                            Menunggu
                                                                        </span>
                                                                    @elseif ($penugasan->status_dl === 'ACC')
                                                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1
                                                                            text-xs font-medium text-green-800">
                                                                            Diterima
                                                                        </span>
                                                                    @elseif ($penugasan->status_dl === 'Ditolak')
                                                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1
                                                                            text-xs font-medium text-red-800">
                                                                            Ditolak
                                                                        </span>
                                                                        @if (Auth::user()->active_role === 'Ketua Tim')
                                                                            <button
                                                                                type="button"
                                                                                title="Verifikasi DL"
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
                                                                                class="text-blue-600 hover:text-blue-800 underline text-xs">
                                                                                Ajukan Kembali
                                                                            </button>
                                                                        @endif
                                                                    @endif

                                                                    @can('acceptDL', $penugasan)
                                                                        {{-- ================= ACC ================= --}}
                                                                        @if ($penugasan->status_dl === 'ACC')

                                                                            {{-- SUDAH MASUK KALENDER --}}
                                                                            @if ($penugasan->sudahMasukKalenderDL())
                                                                                <span
                                                                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-md">
                                                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                        <path stroke-width="2" d="M9 12l2 2 4-4"/>
                                                                                        <path stroke-width="2" d="M12 22a10 10 0 100-20 10 10 0 000 20z"/>
                                                                                    </svg>
                                                                                    Sudah masuk kalender
                                                                                </span>

                                                                            {{-- BELUM MASUK KALENDER --}}
                                                                            @else
                                                                                <form action="{{ route('kalenderDL.store') }}" method="POST" class="inline">
                                                                                    @csrf
                                                                                    <input type="hidden" name="id_pegawai" value="{{ $penugasan->id_anggota }}">
                                                                                    <input type="hidden" name="id_penugasan" value="{{ $penugasan->id_penugasan }}">
                                                                                    <input type="hidden" name="tanggal_mulai" value="{{ $penugasan->tanggal_mulai }}">
                                                                                    <input type="hidden" name="tanggal_selesai" value="{{ $penugasan->tanggal_selesai }}">

                                                                                    <button type="submit"
                                                                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 rounded-md">
                                                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                            <path stroke-width="2" d="M12 4v16m8-8H4"/>
                                                                                        </svg>
                                                                                        Masukkan Kalender DL
                                                                                    </button>
                                                                                </form>
                                                                            @endif


                                                                        {{-- ================= DITOLAK ================= --}}
                                                                        @elseif ($penugasan->status_dl === 'Ditolak')
                                                                            <span
                                                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-md">
                                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                    <path stroke-width="2" d="M12 9v4m0 4h.01"/>
                                                                                    <path stroke-width="2"
                                                                                        d="M10.29 3.86l-7.4 12.84A1 1 0 003.76 18h16.48a1 1 0 00.87-1.3l-7.4-12.84a1 1 0 00-1.72 0z"/>
                                                                                </svg>
                                                                                Ditolak
                                                                            </span>


                                                                        {{-- ================= MENUNGGU / VERIFIKASI ================= --}}
                                                                        @else
                                                                            <button
                                                                                type="button"
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
                                                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-orange-700 bg-orange-100 hover:bg-orange-200 rounded-md">
                                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                    <path stroke-width="2" d="M12 8v4l3 3"/>
                                                                                    <path stroke-width="2" d="M12 22a10 10 0 100-20 10 10 0 000 20z"/>
                                                                                </svg>
                                                                                Verifikasi
                                                                            </button>
                                                                        @endif

                                                                    @endcan

                                                                </div>
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
