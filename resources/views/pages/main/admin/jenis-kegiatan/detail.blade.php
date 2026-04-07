@extends('layouts.dashboard')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-y-4 sm:flex-row sm:items-center px-2">
    <div>
        <h2 class="text-xl font-bold text-gray-800 dark:text-white sm:text-2xl">
            {{ $title }}
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Daftar penugasan untuk kategori jenis kegiatan ini
        </p>
    </div>
    
    <div>
        <a href="{{ route('jenis-kegiatan.index') }}"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white shadow-theme-xs transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16 dark:text-gray-400">No.</th>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Kegiatan & Sub Kegiatan</th>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Anggota</th>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32 dark:text-gray-400">Target</th>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-48 dark:text-gray-400">Tanggal Pelaksanaan</th>
                    <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-40 dark:text-gray-400">Status Penugasannya</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                @forelse ($jenisKegiatan->penugasans as $index => $penugasan)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150 cursor-pointer"
                        onclick="window.location.href='{{ route('sub.kegiatan.show', ['kegiatan' => $penugasan->subKegiatan->id_kegiatan, 'subKegiatan' => $penugasan->id_sub_kegiatan]) }}#penugasan-{{ $penugasan->id_penugasan }}'">
                        <td class="px-4 py-4 text-sm text-gray-600 font-medium text-center dark:text-gray-400">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 p-0 w-80 md:w-full break-words">
                                    {{ $penugasan->subKegiatan->kegiatan->nama_rk_kegiatan ?? '-' }}
                                </span>
                                <span class="text-xs text-brand-600 font-medium dark:text-brand-400 line-clamp-2">
                                    Sub: {{ $penugasan->subKegiatan->nama_sub_kegiatan ?? '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex py-1 px-3 rounded-md bg-gray-100 text-gray-800 text-xs font-bold dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                {{ $penugasan->target }} {{ $penugasan->satuan_target }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>
                                    {{ \Carbon\Carbon::parse($penugasan->tanggal_mulai)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($penugasan->tanggal_selesai)->format('d M Y') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $statusInfo = $penugasan->statusPenugasan();
                            @endphp
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $statusInfo['class'] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusInfo['label'] ?? 'Tidak Diketahui' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-base text-gray-600 dark:text-gray-300 font-medium">Belum ada penugasan</p>
                                <p class="text-sm mt-1">Tidak ditemukan data penugasan untuk jenis kegiatan ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
