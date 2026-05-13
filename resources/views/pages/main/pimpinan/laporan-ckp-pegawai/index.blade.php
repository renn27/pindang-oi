@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    <!-- Bagian Filter Tahun -->
    <div class="flex flex-col sm:flex-row justify-between items-center rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 mb-6 dark:border-gray-800 dark:bg-gray-900">
        <form method="GET" action="{{ route('pimpinan.laporan-ckp.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full sm:w-auto">
            <div class="flex items-center h-10">
                <label class="text-sm font-medium text-gray-700 whitespace-nowrap dark:text-gray-300">
                    Filter Tahun Laporan
                </label>
            </div>

            <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent w-full sm:w-auto">
                <select name="tahun"
                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full sm:w-36 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-4 pr-10 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500"
                    :class="isOptionSelected && 'text-gray-800'" @change="isOptionSelected = true; $event.target.form.submit();">
                    @foreach ($tahunList as $thn)
                        <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}
                            class="text-gray-700 dark:text-gray-300">
                            {{ $thn }}
                        </option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute top-1/2 right-3.5 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                    <svg class="stroke-current" width="16" height="16" viewBox="0 0 20 20" fill="none">
                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </div>
        </form>

        <div class="mt-4 sm:mt-0 flex items-center gap-3 text-sm">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-green-100 border border-green-200 dark:bg-green-900/70 dark:border-green-800/70 inline-block"></span>
                <span class="text-gray-600 dark:text-gray-400">Sudah Download</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-gray-100 border border-gray-200 dark:bg-gray-800 dark:border-gray-700 inline-block"></span>
                <span class="text-gray-600 dark:text-gray-400">Belum Download</span>
            </div>
        </div>
    </div>

    <!-- Heatmap Table -->
    <div class="border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden bg-white dark:bg-gray-900 shadow-sm">
        <div class="overflow-x-auto overflow-y-auto max-h-[calc(100vh-280px)] min-h-[400px] custom-scrollbar relative">
            <table class="w-full whitespace-nowrap border-collapse">
                <thead class="sticky top-0 z-30">
                    <tr class="bg-gray-50 dark:bg-gray-800">
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-r border-gray-200 dark:border-gray-700 dark:text-gray-400 sticky left-0 top-0 z-40 bg-gray-50 dark:bg-gray-800 min-w-[280px]">
                            Pegawai
                        </th>
                        @foreach($bulanList as $key => $nama)
                            <th class="px-3 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 dark:text-gray-400 min-w-[90px] bg-gray-50 dark:bg-gray-800">
                                {{ $nama }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pegawais as $index => $pegawai)
                        <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <!-- Sticky Column -->
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 border-r border-gray-200 dark:border-gray-700 sticky left-0 z-10 bg-white dark:bg-gray-900 group-hover:bg-gray-50 dark:group-hover:bg-gray-800/50">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-medium text-gray-400 dark:text-gray-500 w-5 text-right">{{ $index + 1 }}</span>
                                    <span class="font-medium truncate max-w-[220px]" title="{{ $pegawai->nama_pegawai }}">
                                        {{ $pegawai->nama_pegawai }}
                                    </span>
                                </div>
                            </td>
                            
                            <!-- Month Columns -->
                            @foreach($bulanList as $key => $nama)
                                @php
                                    $count = $downloadData[$pegawai->id_pegawai][$key] ?? 0;
                                @endphp
                                
                                <td class="px-2 py-2 text-center border-l border-gray-100 dark:border-gray-800/50">
                                    @if($count > 0)
                                        <div class="flex items-center justify-center">
                                            <span class="inline-flex items-center justify-center min-w-[64px] px-2.5 py-1.5 rounded-md text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 border border-green-200 dark:border-green-800/50 shadow-sm transition-transform hover:scale-105 cursor-default" title="Didownload {{ $count }} kali pada bulan {{ $nama }} {{ $tahun }}">
                                                {{ $count }} X
                                            </span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center">
                                            <span class="inline-flex items-center justify-center min-w-[64px] px-2.5 py-1.5 rounded-md text-xs font-medium bg-gray-100 text-gray-400 dark:bg-gray-800/60 dark:text-gray-500 border border-transparent cursor-default" title="Belum download pada bulan {{ $nama }} {{ $tahun }}">
                                                0
                                            </span>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p>Belum ada data pegawai yang tersedia.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
