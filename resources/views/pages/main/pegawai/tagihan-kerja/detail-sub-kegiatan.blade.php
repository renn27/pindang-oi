@extends('layouts.dashboard')

@section('content')
    @php
        // Gabungkan penugasan untuk menghitung progres keseluruhan
        $allPenugasans = $penugasanButuhDLAtauTranslok->concat($penugasanTidakButuhDLAtauTranslok);
        
        $totalTargetPenugasan = $allPenugasans->sum('target');
        
        $penugasanTargetSelesai = $allPenugasans->sum(function($p) {
            $adaPelunasan = $p->pengirimans->contains(fn($k) =>
                $k->tipe_pengiriman === 'Pelunasan' && $k->penerimaan?->status === 'Diterima'
            );

            return $p->pengirimans->sum(fn($k) =>
                $k->penerimaan?->status === 'Diterima' &&
                $k->tipe_pengiriman === ($adaPelunasan ? 'Pelunasan' : 'Cicilan')
                    ? $k->jumlah_dikirim ?? 0
                    : 0
            );
        });

        $progressPercent = $totalTargetPenugasan ? round(($penugasanTargetSelesai / $totalTargetPenugasan) * 100) : 0;
        
        // Hitung persentase untuk masing-masing kategori
        $targetDL = $penugasanButuhDLAtauTranslok->sum('target');
        $selesaiDL = $penugasanButuhDLAtauTranslok->sum(function($p) {
            $adaPelunasan = $p->pengirimans->contains(fn($k) =>
                $k->tipe_pengiriman === 'Pelunasan' && $k->penerimaan?->status === 'Diterima'
            );
            return $p->pengirimans->sum(fn($k) =>
                $k->penerimaan?->status === 'Diterima' &&
                $k->tipe_pengiriman === ($adaPelunasan ? 'Pelunasan' : 'Cicilan') ? $k->jumlah_dikirim ?? 0 : 0
            );
        });
        $percentDL = $targetDL ? round(($selesaiDL / $targetDL) * 100) : 0;

        $targetNonDL = $penugasanTidakButuhDLAtauTranslok->sum('target');
        $selesaiNonDL = $penugasanTidakButuhDLAtauTranslok->sum(function($p) {
            $adaPelunasan = $p->pengirimans->contains(fn($k) =>
                $k->tipe_pengiriman === 'Pelunasan' && $k->penerimaan?->status === 'Diterima'
            );
            return $p->pengirimans->sum(fn($k) =>
                $k->penerimaan?->status === 'Diterima' &&
                $k->tipe_pengiriman === ($adaPelunasan ? 'Pelunasan' : 'Cicilan') ? $k->jumlah_dikirim ?? 0 : 0
            );
        });
        $percentNonDL = $targetNonDL ? round(($selesaiNonDL / $targetNonDL) * 100) : 0;
    @endphp

    <x-common.page-breadcrumb 
        pageTitle="Detail Sub Kegiatan" 
        backTitle="Daftar Kegiatan"
        backUrl="{{ route('kegiatan.index', $subKegiatan->kegiatan->bidang->slug) }}" 
    />

    <!-- Box Ringkasan Progres Terbobot (Weighted Average) -->
    <div class="mb-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 p-5 text-white shadow-md dark:from-blue-700 dark:to-indigo-700">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-white tracking-wide uppercase">
                    Progres Sub Kegiatan Berdasarkan Target
                </h3>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-xs text-blue-100 font-medium">
                    <span>• Perlu DL/Translok : <strong>({{ $selesaiDL }}/{{ $targetDL }})</strong></span>
                    <span>• Tidak Perlu DL/Translok : <strong>({{ $selesaiNonDL }}/{{ $targetNonDL }})</strong></span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-2xl font-black text-white">
                    {{ $progressPercent }}%
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm">
                    {{ $penugasanTargetSelesai }} / {{ $totalTargetPenugasan }} Tugas
                </span>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 mb-6 dark:border-gray-800 dark:bg-gray-900">
        <!-- Header Kegiatan -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 dark:text-white">
                {{ $subKegiatan->nama_sub_kegiatan }}
            </h1>

            <!-- Informasi Kegiatan dalam Tabel Format -->
            <div class="border border-gray-200 rounded-lg overflow-hidden dark:border-gray-700">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 w-32 dark:bg-gray-800 dark:text-gray-400">
                                Bidang
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">
                                {{ $subKegiatan->kegiatan->bidang->nama_bidang }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 w-32 dark:bg-gray-800 dark:text-gray-400">
                                Sumber Kegiatan
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">
                                {{ $subKegiatan->kegiatan->nama_rk_kegiatan }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 w-32 dark:bg-gray-800 dark:text-gray-400">
                                Nama Ketua
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">
                                @if($subKegiatan->kegiatan->transfer)
                                    {{ $subKegiatan->kegiatan->transfer->fromKetua->nama_pegawai }} (Ketua tim lama) dialihkan ke {{ $subKegiatan->kegiatan->transfer->toKetua->nama_pegawai }} (Ketua tim baru)
                                @else
                                    {{ $subKegiatan->kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                Tanggal Mulai
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">
                                {{ $subKegiatan->tanggal_mulai->format('d M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                Tanggal Berakhir
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">
                                {{ $subKegiatan->tanggal_selesai->format('d M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                Target
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">
                                {{ $subKegiatan->target }} {{ $subKegiatan->satuan_target }} 
                            </td>
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                Status
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">
                                {{ $subKegiatan->status }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section Progres -->
        <div class="mb-8 max-w-full overflow-hidden">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 dark:text-white">Progres</h2>
            @can('create', [App\Models\Penugasan::class, $subKegiatan])
                <div class="flex justify-start mb-6">
                    <button
                        class="flex items-center gap-2 rounded-full border border-gray-300
                            bg-white px-4 py-3 text-sm font-medium text-gray-700
                            shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
                            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                        @click="$dispatch('open-smart-modal', {
                                modalId: 'modal-penugasan-anggota',
                                data: {
                                    id_sub_kegiatan: @js($subKegiatan->id_sub_kegiatan),
                                    nama_sub_kegiatan: @js($subKegiatan->nama_sub_kegiatan),
                                    min_date: @js($subKegiatan->tanggal_mulai->format('Y-m-d')),
                                    max_date: @js($subKegiatan->tanggal_selesai->format('Y-m-d'))
                                }
                            })">
                        <!-- icon -->
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                                fill="" />
                        </svg>
                        Tambah Anggota
                    </button>
                </div>
            @endcan
            <!-- Tabel Penugasan Anggota-->
            @include('pages.main.components.tables.tagihan-kerja.table-penugasan-anggota')
            {{-- @include('pages.main.components.tables.tagihan-kerja.table-penugasan-anggota') --}}
        </div>
    </div>

    {{-- DAFTAR MODAL --}}
    {{-- Modal CKP (Universal, dipakai untuk CKP Anggota) --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-ckp-universal')

    {{-- Modal Penugasan Anggota --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-penugasan-anggota')
    {{-- Modal Penerimaan Anggota --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-penerimaan-anggota')

    {{-- Modal Pengiriman Anggota --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-pengiriman-anggota')

    {{-- Modal Histori Pengiriman --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-history-pengiriman')

    {{-- Modal Update Jenis Kegiatan Khusus --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-update-jenis-kegiatan')

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash) {
                const id = window.location.hash.substring(1);
                const targetRow = document.getElementById(id);
                
                if (targetRow) {
                    // Scroll smoothly to center
                    targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Highlight the row temporarily
                    targetRow.classList.add('!bg-yellow-100', 'dark:!bg-yellow-900/50', 'transition-colors', 'duration-1000');
                    
                    // Remove highlight after 3 seconds
                    setTimeout(() => {
                        targetRow.classList.remove('!bg-yellow-100', 'dark:!bg-yellow-900/50');
                    }, 3000);
                }
            }
        });
    </script>
    @endpush

@endsection