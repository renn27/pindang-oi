@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Detail Kegiatan" />

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6 mb-6">
        <!-- Header Kegiatan -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
                {{ $subKegiatan->nama_sub_kegiatan }}
            </h1>

            <!-- Informasi Kegiatan dalam Tabel Format -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50 w-32">
                                Sub Kegiatan
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                {{ $subKegiatan->nama_sub_kegiatan }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                                Jenis Kegiatan
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                {{ $subKegiatan->jenis_kegiatan }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                                Tanggal Mulai
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                {{ $subKegiatan->tanggal_mulai->format('d M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                                Tanggal Berakhir
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                {{ $subKegiatan->tanggal_selesai->format('d M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                                Target
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                200
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50">
                                Status
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
                                {{ $subKegiatan->status }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section Progres -->
        <div class="mb-8 max-w-full overflow-hidden">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Progres</h2>
            @can('manage', $kegiatan)
                <div class="flex justify-start mb-6">
                    <button
                        class="flex items-center gap-2 rounded-full border border-gray-300
                            bg-white px-4 py-3 text-sm font-medium text-gray-700
                            shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
                            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400
                            dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                        @click="$dispatch('open-smart-modal', {
                                modalId: 'modal-penugasan-anggota',
                                data: {
                                    id_sub_kegiatan: '{{ $subKegiatan->id_sub_kegiatan }}',
                                    nama_sub_kegiatan: '{{ $subKegiatan->nama_sub_kegiatan }}'
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
        </div>
    </div>

    {{-- DAFTAR MODAL --}}
    @can('manage', $kegiatan)
        {{-- Modal Penugasan Anggota --}}
        @include('pages.main.components.modals.tagihan-kerja.modal-penugasan-anggota')
        {{-- Modal Penerimaan Anggota --}}
        @include('pages.main.components.modals.tagihan-kerja.modal-penerimaan-anggota')
    @endcan

    {{-- Modal Pengiriman Anggota --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-pengiriman-anggota')

    {{-- Modal Histori Pengiriman --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-history-pengiriman')

@endsection