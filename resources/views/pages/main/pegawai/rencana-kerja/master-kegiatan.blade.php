@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="Master Kegiatan" />

    <div id="app" data-pegawais='@json($pegawais)'></div>

    <div class="space-y-6">
        <x-common.component-card title="Master Kegiatan">
            <div class="flex justify-end">
                @can('create', App\Models\Kegiatan::class)
                    <button
                        class="flex items-center gap-2 rounded-full border border-gray-300
                        bg-white px-4 py-3 text-sm font-medium text-gray-700
                        shadow-theme-xs hover:bg-gray-50 hover:text-gray-800"
                        @click="$dispatch('open-smart-modal', {
                            modalId: 'modal-master-kegiatan',
                    })">
                        <!-- icon -->
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                                fill="" />
                        </svg>
                        Tambah Master Kegiatan
                    </button>
                @endcan
            </div>
        </x-common.component-card>

        <!-- Tampilan Card Fungsi dengan Accordion -->
        <x-common.component-card title="Matriks Peran Hasil">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800"></h2>
                <div class="flex items-center gap-2">
                    <a href="{{ route('kegiatan.export-mph-all') }}"
                        class="flex items-center gap-2 rounded-lg border border-green-500
                            bg-green-50 px-4 py-2 text-sm font-medium text-green-700
                            hover:bg-green-100 hover:text-green-800 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 384 512">
                            <path d="M48 448V64c0-8.8 7.2-16 16-16H224v80c0 17.7 14.3 32 32 32h80V448c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16zM64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V154.5c0-17-6.7-33.3-18.7-45.3L274.7 18.7C262.7 6.7 246.5 0 229.5 0H64zm90.9 233.3c-8.1-10.5-23.2-12.3-33.7-4.2s-12.3 23.2-4.2 33.7L161.6 320l-44.5 57.3c-8.1 10.5-6.3 25.5 4.2 33.7s25.5 6.3 33.7-4.2L192 359.1l37.1 47.6c8.1 10.5 23.2 12.3 33.7 4.2s12.3-23.2 4.2-33.7L222.4 320l44.5-57.3c-8.1-10.5-6.3-25.5-4.2-33.7s-23.2-12.3-33.7-4.2L192 280.9l-37.1-47.6z"/>
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>
            {{-- <div class="space-y-4">
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
                                    <table class="min-w-full border border-gray-200">
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
                                                    Nama Pegawai
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
                                                    Satuan
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
                                                                {{ $penugasan->target ?? '-' }}
                                                            </td>

                                                            <!-- Kolom Satuan -->
                                                            <td
                                                                class="px-4 py-3 text-sm text-gray-800 align-top border border-gray-200">
                                                                {{ $penugasan->satuan_target ?? '-' }}
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
            </div> --}}

            <div class="space-y-4">
    @foreach ($bidangs as $bidang)
        <div x-data="{ open: false }" class="rounded-lg border border-gray-200 bg-white">

            <!-- Header Fungsi -->
            <button @click="open = !open"
                class="flex w-full items-center justify-between p-4 text-left hover:bg-gray-50">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">{{ $bidang->nama_bidang }}</h3>
                    <p class="text-xs text-gray-500">
                        Total {{ $bidang->kegiatans->count() }} kegiatan
                    </p>
                </div>

                <svg :class="{ 'rotate-180': open }"
                    class="h-5 w-5 text-gray-500 transition-transform"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Accordion -->
            <div x-show="open" x-collapse class="border-t border-gray-100">

                @if ($bidang->kegiatans->count() > 0)

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">

                            <!-- HEADER -->
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border">Kegiatan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border">Sub Kegiatan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border">Nama Pegawai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border">Jenis Kegiatan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border">Target</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border">Satuan</th>
                                </tr>
                            </thead>

                            <!-- BODY -->
                            <tbody>

                                @foreach ($bidang->kegiatans as $kegiatan)

                                    {{-- Kalau belum ada sub --}}
                                    @if ($kegiatan->subKegiatans->count() === 0)

                                        <tr>
                                            <td class="px-4 py-3 align-top border">
                                                <div class="flex flex-col">
                                                    <span class="font-medium">
                                                        {{ $kegiatan->nama_rk_kegiatan }}
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        Ketua:
                                                        {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="px-4 py-3 text-gray-500 italic border">
                                                Belum ada sub kegiatan
                                            </td>

                                            <td colspan="4"
                                                class="px-4 py-3 text-center text-gray-500 italic border">
                                                Belum ada penugasan
                                            </td>
                                        </tr>

                                    @else

                                        @foreach ($kegiatan->subKegiatans as $subIndex => $subKegiatan)

                                            @php($penugasanCount = max($subKegiatan->penugasans->count(),1))

                                            {{-- Kalau belum ada penugasan --}}
                                            @if ($subKegiatan->penugasans->count() === 0)

                                                <tr>

                                                    {{-- MERGED KEGIATAN --}}
                                                    @if ($subIndex === 0)
                                                        <td rowspan="{{ $kegiatan->subKegiatans->sum(fn($s)=> max($s->penugasans->count(),1)) }}"
                                                            class="px-4 py-3 align-top border">
                                                            <div class="flex flex-col">
                                                                <span class="font-medium">
                                                                    {{ $kegiatan->nama_rk_kegiatan }}
                                                                </span>
                                                                <span class="text-xs text-gray-500">
                                                                    Ketua:
                                                                    {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                    @endif

                                                    {{-- MERGED SUB --}}
                                                    <td rowspan="1"
                                                        class="px-4 py-3 align-top border">
                                                        {{ $subKegiatan->nama_sub_kegiatan }}
                                                    </td>

                                                    <td colspan="4"
                                                        class="px-4 py-3 text-center text-gray-500 italic border">
                                                        Belum ada penugasan
                                                    </td>

                                                </tr>

                                            @else

                                                @foreach ($subKegiatan->penugasans as $penugasanIndex => $penugasan)

                                                    <tr>

                                                        {{-- KEGIATAN --}}
                                                        @if ($subIndex === 0 && $penugasanIndex === 0)
                                                            <td rowspan="{{ $kegiatan->subKegiatans->sum(fn($s)=> max($s->penugasans->count(),1)) }}"
                                                                class="px-4 py-3 align-top border">
                                                                <div class="flex flex-col">
                                                                    <span class="font-medium">
                                                                        {{ $kegiatan->nama_rk_kegiatan }}
                                                                    </span>
                                                                    <span class="text-xs text-gray-500">
                                                                        Ketua:
                                                                        {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        @endif

                                                        {{-- SUB KEGIATAN --}}
                                                        @if ($penugasanIndex === 0)
                                                            <td rowspan="{{ $penugasanCount }}"
                                                                class="px-4 py-3 align-top border">
                                                                {{ $subKegiatan->nama_sub_kegiatan }}
                                                            </td>
                                                        @endif

                                                        <td class="px-4 py-3 border">
                                                            {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                                                        </td>

                                                        <td class="px-4 py-3 border">
                                                            {{ $penugasan->jenisKegiatan->jenis_kegiatan ?? '-' }}
                                                        </td>

                                                        <td class="px-4 py-3 border">
                                                            {{ $penugasan->target ?? '-' }}
                                                        </td>

                                                        <td class="px-4 py-3 border">
                                                            {{ $penugasan->satuan_target ?? '-' }}
                                                        </td>

                                                    </tr>

                                                @endforeach
                                            @endif

                                        @endforeach
                                    @endif

                                @endforeach

                            </tbody>
                        </table>
                    </div>

                @else
                    <div class="p-4 text-center border-t">
                        <p class="text-sm text-gray-500 italic">
                            Belum ada kegiatan untuk fungsi ini
                        </p>
                    </div>
                @endif

            </div>
        </div>
    @endforeach


    {{-- Kalau belum ada bidang --}}
    @if ($bidangs->count() === 0)
        <div class="text-center py-8 border border-gray-200 rounded-lg">
            <p class="text-sm text-gray-500">
                Belum ada fungsi/bidang yang dibuat
            </p>
        </div>
    @endif
</div>

        </x-common.component-card>
    </div>

    <!-- Modal Master Kegiatan -->
    @include('pages.main.components.modals.rencana-kerja.modal-master-kegiatan')

    <!-- Modal Konfirmasi Master Kegiatan -->
    @include('pages.main.components.modals.rencana-kerja.modal-konfirmasi-master-kegiatan')
@endsection

@push('scripts')
    <script>
        const app = document.getElementById('app');

        const pegawais = app?.dataset.pegawais ?
            JSON.parse(app.dataset.pegawais) : [];
        // Variabel global untuk menyimpan data
        let rkAnggotaCounter = 0;
        let detailAnggotaCounter = {};

        // Fungsi untuk menambah section RK Anggota
        function tambahRKAnggota() {
            console.log('✅ Tombol Tambah RK Anggota diklik!');

            rkAnggotaCounter++;
            const sectionIndex = rkAnggotaCounter;
            const sectionId = `rk-anggota-${sectionIndex}`;
            detailAnggotaCounter[sectionId] = 0;

            const sectionHTML = `
                <div id="${sectionId}" class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <h5 class="text-sm font-semibold text-gray-800">
                            Sub Kegiatan ${rkAnggotaCounter}
                        </h5>
                        <button type="button" onclick="hapusRKAnggota('${sectionId}')"
                            class="rounded-lg p-1 text-gray-500 hover:bg-gray-200 hover:text-gray-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <input type="hidden" name="rk_section_keys[]" value="${sectionId}">

                    <div class="space-y-4">
                        <div class="flex flex-col gap-2 md:flex-row md:items-center">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 md:w-1/4">
                                Nama Sub Kegiatan
                            </label>
                            <input name="rk_anggota[]" type="text" placeholder="Masukkan rk anggota"
                                class="md:w-3/4 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                        </div>

                        <div class="flex flex-col gap-2 md:flex-row md:items-center">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 md:w-1/4">
                                Target Kegiatan
                            </label>
                            <input type="number" name="target[]"
                                placeholder="Misalnya : 200"
                                class="md:w-3/4 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                        </div>

                        <!-- Tanggal Mulai dan Tanggal Selesai untuk Detail Anggota -->
                        <div class="flex flex-col gap-2 md:flex-row md:items-center">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 md:w-1/4">
                                Tanggal Mulai
                            </label>
                            <div class="md:w-3/4">
                                <x-form.date-picker
                                    name="tanggal_mulai[]"
                                    placeholder="Tanggal Mulai"
                                    defaultDate="{{ now()->format('Y-m-d') }}" />
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 md:flex-row md:items-center">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 md:w-1/4">
                                Tanggal Selesai
                            </label>
                            <div class="md:w-3/4">
                                <x-form.date-picker
                                    name="tanggal_selesai[]"
                                    placeholder="Tanggal Selesai"
                                    defaultDate="{{ now()->format('Y-m-d') }}" />
                            </div>
                        </div>

                        <div id="detail-${sectionId}" class="space-y-4"></div>

                        <div class="flex flex-col gap-2 md:flex-row md:items-center">
                            <div class="md:w-1/4"></div>
                            <div class="md:w-3/4">
                                <button type="button" onclick="tambahDetailAnggota('${sectionId}')"
                                    class="flex items-center gap-2 rounded-lg border border-dashed border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8 3.5C8.27614 3.5 8.5 3.72386 8.5 4V7.5H12C12.2761 7.5 12.5 7.72386 12.5 8C12.5 8.27614 12.2761 8.5 12 8.5H8.5V12C8.5 12.2761 8.27614 12.5 8 12.5C7.72386 12.5 7.5 12.2761 7.5 12V8.5H4C3.72386 8.5 3.5 8.27614 3.5 8C3.5 7.72386 3.72386 7.5 4 7.5H7.5V4C7.5 3.72386 7.72386 3.5 8 3.5Z" fill=""/>
                                    </svg>
                                    Tambah Anggota
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const container = document.getElementById('rkAnggotaContainer');
            container.insertAdjacentHTML('beforeend', sectionHTML);

            // Set tanggal default untuk input baru
            const sectionElement = document.getElementById(sectionId);
            if (sectionElement) {
                const today = new Date().toISOString().split('T')[0];
                const nextWeek = new Date();
                nextWeek.setDate(nextWeek.getDate() + 7);
                const nextWeekFormatted = nextWeek.toISOString().split('T')[0];

                const tanggalMulaiInput = sectionElement.querySelector('input[name="tanggal_mulai[]"]');
                const tanggalSelesaiInput = sectionElement.querySelector('input[name="tanggal_selesai[]"]');

                if (tanggalMulaiInput) tanggalMulaiInput.value = today;
                if (tanggalSelesaiInput) tanggalSelesaiInput.value = nextWeekFormatted;
            }

            setTimeout(() => {
                const newSection = document.getElementById(sectionId);
                if (newSection) {
                    newSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            }, 100);

            console.log('✅ Section RK Anggota ditambahkan dengan ID:', sectionId);
        }

        function hapusRKAnggota(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.remove();
                console.log('🗑️ Section dihapus:', sectionId);
            }
        }

        function tambahDetailAnggota(sectionId) {
            console.log('➕ Menambah detail anggota untuk section:', sectionId);

            if (!detailAnggotaCounter[sectionId]) {
                detailAnggotaCounter[sectionId] = 0;
            }

            detailAnggotaCounter[sectionId]++;
            const detailId = sectionId + '-detail-' + detailAnggotaCounter[sectionId];

            const detailHTML = `
                <div id="${detailId}" class="rounded-lg border border-dashed border-gray-300 bg-white p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <h6 class="text-xs font-medium text-gray-700">
                            Anggota ${detailAnggotaCounter[sectionId]}
                        </h6>
                        <button type="button" onclick="hapusDetailAnggota('${detailId}')"
                            class="rounded-lg p-1 text-gray-400 hover:bg-gray-200 hover:text-gray-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <!-- Kolom Nama Anggota dengan Alpine Search (PERBAIKAN) -->
                        <div class="relative flex flex-col gap-2 md:flex-row md:items-center"
                            x-data="{
                                open: false,
                                search: '',
                                selectedId: '',
                                highlightedIndex: -1,
                                pegawais: pegawais,

                                filtered() {
                                    if(this.search.length === 0) return [];
                                    return this.pegawais.filter(p =>
                                        p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase())
                                    );
                                },

                                selectPegawai(p) {
                                    this.search = p.nama_pegawai;
                                    this.selectedId = p.id_pegawai;
                                    this.open = false;
                                    this.highlightedIndex = -1;
                                },

                                highlightNext() {
                                    if(this.highlightedIndex < this.filtered().length - 1) this.highlightedIndex++;
                                },
                                highlightPrev() {
                                    if(this.highlightedIndex > 0) this.highlightedIndex--;
                                },
                                selectHighlighted() {
                                    if(this.highlightedIndex >= 0) this.selectPegawai(this.filtered()[this.highlightedIndex]);
                                }
                            }">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 md:w-1/4">
                                Nama Anggota
                            </label>

                            <div class="relative md:w-3/4">
                                <!-- Input search -->
                                <input
                                    type="text"
                                    x-model="search"
                                    @focus="open = !!search"
                                    @input="open = search.length > 0; selectedId = ''"
                                    @keydown.arrow-down.prevent="highlightNext()"
                                    @keydown.arrow-up.prevent="highlightPrev()"
                                    @keydown.enter.prevent="selectHighlighted()"
                                    placeholder="Ketik untuk cari nama anggota"
                                    class="detail-nama-anggota h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-3 py-2 text-xs text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10">

                                <!-- Hidden input untuk menyimpan ID -->
                                <input type="hidden"
                                        name="detail_id_anggota[${sectionId}][]"
                                        x-model="selectedId">

                                <!-- Hidden input untuk menyimpan Nama -->
                                <input type="hidden"
                                        name="detail_nama_anggota[${sectionId}][]"
                                        x-model="search">

                                <!-- Dropdown -->
                                <div
                                    x-show="open && search.length > 0"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    @click.away="open = false"
                                    class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-60 overflow-y-auto">

                                    <template x-if="filtered().length > 0">
                                        <template x-for="(pegawai, index) in filtered()" :key="pegawai.id_pegawai">
                                            <div
                                                @click="selectPegawai(pegawai)"
                                                :class="{
                                                    'bg-brand-50': highlightedIndex===index,
                                                    'hover:bg-gray-50': highlightedIndex!==index
                                                }"
                                                class="cursor-pointer px-3 py-2 text-xs text-gray-700"
                                                x-text="pegawai.nama_pegawai">
                                            </div>
                                        </template>
                                    </template>

                                    <template x-if="search.length > 0 && filtered().length === 0">
                                        <div class="px-3 py-2 text-xs text-gray-500">
                                            Data tidak ditemukan
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div x-data="{ isOther: false, idJenisKegiatan: '' }"
                            class="flex flex-col gap-2 md:flex-row md:items-start">

                            <label class="mb-1.5 block text-xs font-medium text-gray-700 md:w-1/4">
                                Jenis Kegiatan
                            </label>

                            <div class="md:w-3/4 w-full space-y-2">
                                <!-- SELECT -->
                                <select
                                    name="detail_id_jenis_kegiatan[${sectionId}][]"
                                    x-model="idJenisKegiatan"
                                    @change="isOther = (idJenisKegiatan === 'LAINNYA')"
                                    required
                                    class="h-10 w-full rounded-lg border border-gray-300 px-3 py-2 text-xs">
                                    <option value="">-- Pilih Jenis Kegiatan --</option>

                                    @foreach ($jenisKegiatans as $jenis)
                                        <option value="{{ $jenis->id }}"
                                            class="
                                                @if ($jenis->kategori === 'Utama') text-green-700 font-medium
                                                @elseif($jenis->kategori === 'Tambahan')
                                                    text-orange-700 @endif">
                                            {{ $jenis->jenis_kegiatan }}
                                            ({{ $jenis->kategori }})
                                        </option>
                                    @endforeach

                                    <option value="LAINNYA">➕ Lainnya</option>
                                </select>

                                <!-- INPUT JENIS KEGIATAN BARU -->
                                <div x-show="isOther" x-transition>
                                    <input
                                        type="text"
                                        name="detail_jenis_kegiatan_baru[${sectionId}][]"
                                        placeholder="Masukkan jenis kegiatan baru"
                                        class="dark:bg-dark-900 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-3 py-2 text-xs text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Target Input -->
                        <div class="flex flex-col gap-2 md:flex-row md:items-center">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 md:w-1/4">
                                Target
                            </label>
                            <input name="detail_target[${sectionId}][]" type="text" placeholder="Masukkan target"
                                class="md:w-3/4 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-3 py-2 text-xs text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                        </div>

                        <div class="flex flex-col gap-2 md:flex-row md:items-center">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 md:w-1/4">
                                Satuan Target
                            </label>
                            <input name="detail_satuan_target[${sectionId}][]" type="text" placeholder="Masukkan satuan target"
                                class="md:w-3/4 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-3 py-2 text-xs text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />
                        </div>

                        <!-- Tanggal Mulai -->
                        <div class="flex flex-col gap-2 md:flex-row md:items-center">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 md:w-1/4">
                                Tanggal Mulai
                            </label>
                            <div class="md:w-3/4">
                                <x-form.date-picker
                                    name="detail_tanggal_mulai[${sectionId}][]"
                                    placeholder="Tanggal Mulai"
                                    defaultDate="{{ now()->format('Y-m-d') }}" />
                            </div>
                        </div>

                        <!-- Tanggal Selesai -->
                        <div class="flex flex-col gap-2 md:flex-row md:items-center">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 md:w-1/4">
                                Tanggal Berakhir (Deadline)
                            </label>
                            <div class="md:w-3/4">
                                <x-form.date-picker
                                    name="detail_tanggal_selesai[${sectionId}][]"
                                    placeholder="Tanggal Selesai"
                                    defaultDate="{{ now()->format('Y-m-d') }}" />
                            </div>
                        </div>
                    </div>
                </div>
                `;

            const detailContainer = document.getElementById(`detail-${sectionId}`);
            if (detailContainer) {
                detailContainer.insertAdjacentHTML('beforeend', detailHTML);
                if (window.Alpine) {
                    Alpine.initTree(detailContainer); // ⬅️ TETAP WAJIB
                }
                // Set tanggal default untuk detail anggota
                const detailElement = document.getElementById(detailId);
                if (detailElement) {
                    const today = new Date().toISOString().split('T')[0];
                    const nextWeek = new Date();
                    nextWeek.setDate(nextWeek.getDate() + 7);
                    const nextWeekFormatted = nextWeek.toISOString().split('T')[0];

                    const tanggalMulaiInput = detailElement.querySelector('input[name="detail_tanggal_mulai[' + sectionId +
                        '][]"]');
                    const tanggalSelesaiInput = detailElement.querySelector('input[name="detail_tanggal_selesai[' +
                        sectionId + '][]"]');

                    if (tanggalMulaiInput) tanggalMulaiInput.value = today;
                    if (tanggalSelesaiInput) tanggalSelesaiInput.value = nextWeekFormatted;
                }

                console.log('✅ Detail anggota ditambahkan dengan ID:', detailId);
            }
        }

        function hapusDetailAnggota(detailId) {
            const detail = document.getElementById(detailId);
            if (detail) {
                detail.remove();
                console.log('🗑️ Detail dihapus:', detailId);
            }
        }

        function saveAll(event) {
            const rkKetuaInput = document.getElementById('rkKetua');
            const rkKetua = rkKetuaInput ? rkKetuaInput.value : '';

            const ketuaIdInput = document.querySelector('input[name="id_penanggung_jawab"]');
            const idKetua = ketuaIdInput ? ketuaIdInput.value : '';
            if (!rkKetua) {
                alert('Rencana Kinerja Ketua wajib diisi');
                return;
            }

            if (!idKetua) {
                alert('Nama Ketua wajib dipilih');
                return;
            }
            try {
                console.log('✅ saveAll() function dipanggil');

                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                // Kumpulkan data
                const tahunInput = document.getElementById('tahunInput');
                const tahun = tahunInput ? tahunInput.value : '{{ now()->format('Y') }}';

                // Ambil data dari Alpine component modal (karena menggunakan x-model)
                const rkJptInput = document.querySelector('#rk_jpt');
                const rkJpt = rkJptInput ? rkJptInput.value : '';

                const ikiJptInput = document.querySelector('#iki_jpt');
                const ikiJpt = ikiJptInput ? ikiJptInput.value : '';

                // Bidang (bidang)
                const bidangSelect = document.getElementById('bidang');
                let bidangNama = '';
                if (bidangSelect && bidangSelect.selectedIndex >= 0) {
                    const selectedOption = bidangSelect.options[bidangSelect.selectedIndex];
                    bidangNama = selectedOption ? selectedOption.text : '';
                }

                // Nama Ketua dari Alpine component
                const ketuaSearchInput = document.querySelector(
                    'input[x-model="search"][placeholder*="Ketik untuk cari nama"]');
                const namaKetua = ketuaSearchInput ? ketuaSearchInput.value : '';

                // ID Ketua
                const ketuaIdInput = document.querySelector('input[name="id_penanggung_jawab"]');
                const idKetua = ketuaIdInput ? ketuaIdInput.value : '';

                // Rencana Kinerja Ketua
                const rkKetuaInput = document.getElementById('rkKetua');
                const rkKetua = rkKetuaInput ? rkKetuaInput.value : '';

                // Data RK Anggota
                const sections = document.querySelectorAll('[id^="rk-anggota-"]:not([id*="-detail-"])');

                let detailHTML = '';
                console.log('RK sections terbaca:', sections.length);

                sections.forEach((section, sectionIndex) => {
                    let sectionHTML = '';
                    const sectionId = section.id;

                    // Ambil data dari section
                    const rkAnggotaInput = section.querySelector('input[name="rk_anggota[]"]');
                    const satuanTargetInput = section.querySelector('input[name="satuan_target[]"]');
                    const keteranganInput = section.querySelector('input[name="keterangan[]"]');
                    const tanggalMulaiInput = section.querySelector('input[name="tanggal_mulai[]"]');
                    const tanggalSelesaiInput = section.querySelector('input[name="tanggal_akhir[]"]');

                    const rkAnggota = rkAnggotaInput ? rkAnggotaInput.value : '';
                    const satuanTarget = satuanTargetInput ? satuanTargetInput.value : '';
                    const tanggalMulai = tanggalMulaiInput ? tanggalMulaiInput.value : '';
                    const tanggalAkhir = tanggalSelesaiInput ? tanggalSelesaiInput.value : '';
                    const keterangan = keteranganInput ? keteranganInput.value : '';

                    sectionHTML += `
                    <div class="border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="text-sm font-semibold text-gray-700">
                                RK Anggota ${sectionIndex + 1}
                            </h5>
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                Bagian ${sectionIndex + 1}
                            </span>
                        </div>

                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 mb-1">RK Anggota</span>
                                    <span class="block text-sm text-gray-800">${rkAnggota || '-'}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 mb-1">Satuan Target</span>
                                    <span class="block text-sm text-gray-800">${satuanTarget || '-'}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 mb-1">Tanggal Mulai</span>
                                    <span class="block text-sm text-gray-800">${tanggalMulai || '-'}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 mb-1">Tanggal Selesai</span>
                                    <span class="block text-sm text-gray-800">${tanggalAkhir || '-'}</span>
                                </div>
                            </div>

                            <div>
                                <span class="block text-xs font-medium text-gray-500 mb-1">Keterangan</span>
                                    <span class="block text-sm text-gray-800">${keterangan || '-'}</span>
                            </div>
                    `;

                    // Data Detail Anggota
                    const detailContainer = document.getElementById(`detail-${sectionId}`);
                    let detailAnggotas = [];

                    if (detailContainer) {
                        detailAnggotas = detailContainer.querySelectorAll('[id*="-detail-"]');
                    }

                    if (detailAnggotas.length > 0) {
                        sectionHTML += `
                            <div class="mt-3 border-t border-gray-100 pt-3">
                                <p class="text-xs font-medium text-gray-600 mb-2">Anggota:</p>
                                <div class="space-y-2">
                            `;

                        detailAnggotas.forEach((detail, detailIndex) => {
                            // Ambil data dari Alpine component di detail anggota
                            const namaInput = detail.querySelector('input[name*="detail_nama_anggota"]');
                            const namaAnggota = namaInput ? namaInput.value : '';

                            const idInput = detail.querySelector('input[name*="detail_id_anggota"]');
                            const idAnggota = idInput ? idInput.value : '';

                            const targetInput = detail.querySelector('input[name*="detail_target"]');
                            const target = targetInput ? targetInput.value : '';

                            const detailTanggalMulaiInput = detail.querySelector(
                                'input[name*="detail_tanggal_mulai"]');
                            const detailTanggalSelesaiInput = detail.querySelector(
                                'input[name*="detail_tanggal_selesai"]');

                            const detailTanggalMulai = detailTanggalMulaiInput ? detailTanggalMulaiInput
                                .value : '';
                            const detailTanggalSelesai = detailTanggalSelesaiInput ?
                                detailTanggalSelesaiInput.value : '';

                            sectionHTML += `
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-gray-700">Anggota ${detailIndex + 1}</span>
                                    <div>
                                        <span class="text-xs font-medium text-gray-800">${namaAnggota || '-'}</span>
                                        ${idAnggota ? `<span class="ml-2 text-xs text-gray-500">(ID: ${idAnggota})</span>` : ''}
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <span class="block text-gray-500 mb-1">Target</span>
                                        <span class="block text-gray-700">${target || '-'}</span>
                                    </div>
                                    <div>
                                        <span class="block text-gray-500 mb-1">Tanggal</span>
                                        <span class="block text-gray-700">${detailTanggalMulai || '-'} s/d ${detailTanggalSelesai || '-'}</span>
                                    </div>
                                </div>
                            </div>
                            `;
                        });

                        sectionHTML += `
                                </div>
                            </div>
                            `;
                                }

                                sectionHTML += `</div></div>`;
                                detailHTML += sectionHTML;
                            });

                // Buat HTML untuk modal
                const confirmationHTML = `
        <div class="space-y-6">
            <!-- Header -->
            <div class="text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 mb-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">DATA RENCANA KINERJA KETUA</h3>
                <p class="text-sm text-gray-500">Review data sebelum disimpan ke database</p>
            </div>

            <!-- Data Utama -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">DATA KETUA</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <div>
                            <span class="block text-xs font-medium text-gray-500 mb-1">Tahun</span>
                            <span class="block text-sm text-gray-800 font-medium">${tahun || '-'}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 mb-1">RK JPT</span>
                            <span class="block text-sm text-gray-800">${rkJpt || '-'}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 mb-1">IKI JPT</span>
                            <span class="block text-sm text-gray-800">${ikiJpt || '-'}</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <span class="block text-xs font-medium text-gray-500 mb-1">Bidang</span>
                            <span class="block text-sm text-gray-800 font-medium">${bidangNama || '-'}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 mb-1">Ketua</span>
                            <div>
                                <span class="block text-sm text-gray-800 font-medium">${namaKetua || '-'}</span>
                                ${idKetua ? `<span class="block text-xs text-gray-500 mt-1">ID: ${idKetua}</span>` : ''}
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-gray-500 mb-1">Rencana Kinerja Ketua</span>
                            <span class="block text-sm text-gray-800">${rkKetua || '-'}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data RK Anggota -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-gray-700">DATA RK ANGGOTA</h4>
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">
                        ${sections.length} bagian
                    </span>
                </div>

                ${sections.length > 0 ? `
                                        <div class="space-y-4">
                                            ${detailHTML}
                                        </div>
                                    ` : `
                                        <div class="text-center py-4">
                                            <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 mb-2">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-500">Belum ada RK Anggota yang ditambahkan</p>
                                        </div>
                                    `}
            </div>

            <!-- Note -->
            <div class="flex items-center text-sm text-gray-500">
                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Data akan disimpan ke database setelah konfirmasi
            </div>
        </div>
        `;

                // Tampilkan di modal
                const confirmationContent = document.getElementById('confirmationContent');
                if (confirmationContent) {
                    confirmationContent.innerHTML = confirmationHTML;
                }

                // Setelah data dimasukkan ke modal, cari element Alpine terdekat
                const alpineElement = event.target.closest('[x-data]');

                if (alpineElement && alpineElement.__x) {
                    // Gunakan $dispatch dari Alpine
                    alpineElement.__x.$dispatch('open-confirmation-modal');
                    console.log('✅ Event dispatched via Alpine');
                } else {
                    // Fallback: coba dispatch dengan bubbles
                    const customEvent = new CustomEvent('open-confirmation-modal', {
                        bubbles: true,
                        composed: true
                    });
                    event.target.dispatchEvent(customEvent);
                    console.log('✅ Event dispatched via custom event');
                }

                console.log('✅ Modal konfirmasi ditampilkan');

            } catch (error) {
                console.error('❌ Error in saveAll():', error);
                alert('Terjadi kesalahan: ' + error.message);
            }
        }

        // Fungsi untuk menyimpan data saat tombol "Ya, Simpan Data" diklik
        function confirmSave() {
            window.dispatchEvent(new CustomEvent('close-confirmation-modal', {
                detail: {
                    modalId: 'modal-konfirmasi-master-kegiatan'
                }
            }));

            const form = document.getElementById('masterKegiatanForm');
            if (!form) {
                alert('Form tidak ditemukan');
                return;
            }

            form.submit();
        }

        // Event listener untuk tombol Save di modal utama
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('saveAllButton')?.addEventListener('click', saveAll);
        });
    </script>
@endpush
