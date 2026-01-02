@extends('layouts.app')

@section('content')
<!-- Breadcrumb -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        {{ $pageTitle }}
    </h1>
    @if($detailBidang)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $detailBidang }}
        </p>
    @endif
</div>

<!-- Tabel Data -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50">
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                        No.
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Kegiatan
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                        Target
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">
                        Realisasi
                    </th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-40">
                        Persentase
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                
                <!-- Kegiatan 1 -->
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                        1
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                        Kegiatan Utama {{ $bidang->nama_bidang }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                        100
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                        75
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                        75%
                    </td>
                </tr>
                
                <!-- Kegiatan 2 -->
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                        2
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                        Kegiatan Pendukung {{ $bidang->nama_bidang }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                        50
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                        45
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                        90%
                    </td>
                </tr>
                
                <!-- Kegiatan 3 -->
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 text-center">
                        3
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                        Administrasi {{ $bidang->nama_bidang }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                        30
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400 text-center">
                        28
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400 text-center">
                        93%
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>

<!-- Info Bidang -->
<div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
    <h3 class="font-medium text-blue-800 dark:text-blue-300">Informasi Bidang:</h3>
    <ul class="mt-2 text-sm text-blue-700 dark:text-blue-400">
        <li>• URL: <code>/tagihan-kerja/{{ $bidang->slug }}</code></li>
        <li>• Route Name: <code>{{ $bidang->route_name }}</code></li>
        <li>• ID Bidang: {{ $bidang->id_bidang }}</li>
        <li>• Data diambil dari tabel <code>bidang</code> database</li>
    </ul>
</div>
@endsection