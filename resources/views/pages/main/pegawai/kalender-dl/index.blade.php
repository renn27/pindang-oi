@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="Kalender Dinas Luar" />

    @php
        $currentDate = \Carbon\Carbon::create($year, $month, 1);
        $prevDate = $currentDate->copy()->subMonth();
        $nextDate = $currentDate->copy()->addMonth();
        
        $prevUrl = route('kalenderDL.index', ['month' => $prevDate->month, 'year' => $prevDate->year]);
        $nextUrl = route('kalenderDL.index', ['month' => $nextDate->month, 'year' => $nextDate->year]);
    @endphp

    {{-- Filter & Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between rounded-xl border border-gray-200 bg-white p-3 lg:p-4 mb-4 dark:border-gray-800 dark:bg-gray-900 gap-3">
        <div>
            <h2 class="text-lg lg:text-xl font-bold text-gray-800 dark:text-white tracking-wide leading-tight">
                Jadwal Dinas Luar dan Translok
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Pantau aktivitas pegawai di lapangan
            </p>
        </div>

        {{-- Month Navigation --}}
        <div class="flex items-center gap-1 bg-gray-50 dark:bg-gray-800 p-1.5 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm w-full md:w-auto justify-between md:justify-center">
            <a href="{{ $prevUrl }}" class="p-1.5 rounded-md bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition group flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="group-hover:-translate-x-0.5 transition-transform">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
            </a>
            
            <div class="w-36 text-center font-bold text-sm text-gray-800 dark:text-gray-200 tracking-wide select-none flex items-center justify-center gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-500">
                    <rect x="3" y="4" width="18" height="18" rx="2" stroke-linecap="round"/>
                    <path d="M16 2V6" stroke-linecap="round"/>
                    <path d="M8 2V6" stroke-linecap="round"/>
                    <path d="M3 10H21" stroke-linecap="round"/>
                </svg>
                {{ $currentDate->translatedFormat('F Y') }}
            </div>

            <a href="{{ $nextUrl }}" class="p-1.5 rounded-md bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition group flex items-center justify-center">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-0.5 transition-transform">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- Kalender --}}
    <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden bg-white dark:bg-gray-900 shadow-sm">
        <div class="w-full overflow-x-auto overflow-y-auto max-h-[calc(100vh-220px)] min-h-[400px] custom-scrollbar relative">
            <!-- Pakai table-fixed agar tanggal dibagi rata dan tidak overflow horizontal -->
            <table class="w-full border-collapse table-fixed text-[10px] lg:text-[11px] dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0 z-30">
                    <tr>
                        <th class="border-b border-r dark:border-gray-700 px-3 py-2 text-left font-semibold text-gray-600 dark:text-gray-300 w-[140px] md:w-[160px] sticky left-0 top-0 z-40 bg-gray-50 dark:bg-gray-900 uppercase tracking-wider text-[10px] shadow-[1px_0_0_0_#e5e7eb] dark:shadow-[1px_0_0_0_#374151]">
                            Pegawai
                        </th>
                        <th class="border-b border-r dark:border-gray-700 px-1 py-2 text-center font-semibold text-gray-600 dark:text-gray-300 w-[36px] bg-gray-50 dark:bg-gray-900 sticky top-0 z-30 uppercase tracking-wider text-[9px] shadow-[1px_0_0_0_#e5e7eb] dark:shadow-[1px_0_0_0_#374151]">
                            Tot
                        </th>
                        @foreach ($dates as $date)
                            <!-- Lebar auto, dibagi rata oleh table-fixed -->
                            <th class="border-b dark:border-gray-700 px-0 py-2 text-center bg-gray-50 dark:bg-gray-900 sticky top-0 z-30">
                                <div class="font-bold text-gray-800 dark:text-gray-300 text-[10px] leading-none">
                                    {{ $date->format('d') }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-400 uppercase text-[8px] mt-0.5 leading-none">
                                    {{ $date->translatedFormat('D') }}
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($pegawais as $index => $pegawai)
                        <tr class="group hover:bg-blue-50/50 dark:hover:bg-gray-800/50 transition-colors h-[28px] lg:h-[32px]">
                            <td class="px-3 py-1 font-medium text-gray-800 dark:text-gray-300 truncate sticky left-0 z-10 bg-white dark:bg-gray-900 group-hover:bg-blue-50/50 dark:group-hover:bg-gray-800/50 shadow-[1px_0_0_0_#e5e7eb] dark:shadow-[1px_0_0_0_#374151]">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[9px] text-gray-400 w-3 text-right">{{ $index + 1 }}</span>
                                    <span class="truncate max-w-[110px] md:max-w-[130px] text-[10px] lg:text-[11px]" title="{{ $pegawai->nama_pegawai }}">{{ $pegawai->nama_pegawai }}</span>
                                </div>
                            </td>
                            <td class="px-1 py-1 text-center bg-white dark:bg-gray-900 group-hover:bg-blue-50/50 dark:group-hover:bg-gray-800/50 shadow-[1px_0_0_0_#e5e7eb] dark:shadow-[1px_0_0_0_#374151]">
                                @if ($pegawai->total_dl_bulan_ini > 0)
                                    <span class="inline-flex items-center justify-center bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 px-1 py-0.5 rounded text-[9px] font-bold min-w-[20px]">
                                        {{ $pegawai->total_dl_bulan_ini }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center bg-gray-100 dark:bg-gray-800/60 text-gray-400 dark:text-gray-500 px-1 py-0.5 rounded text-[9px] font-medium min-w-[20px]">
                                        0
                                    </span>
                                @endif
                            </td>
                            @foreach ($dates as $date)
                                @php
                                    $kalenderItem = $pegawai->kalenderDls->first(function ($dl) use ($date) {
                                        return $dl->tanggal_dl === $date->toDateString();
                                    });

                                    $isTranslok = false;
                                    $isDL = false;

                                    if ($kalenderItem) {
                                        if ($kalenderItem->penugasan) {
                                            $isTranslok = $kalenderItem->penugasan->butuh_translok;
                                            $isDL = $kalenderItem->penugasan->butuh_dl;
                                        } elseif ($kalenderItem->agendaPimpinan) {
                                            $isDL = $kalenderItem->agendaPimpinan->butuh_dl;
                                        }
                                    }
                                @endphp
                                <td class="border-l border-gray-100 dark:border-gray-800/50 p-0.5 text-center bg-white dark:bg-gray-900 group-hover:bg-blue-50/50 dark:group-hover:bg-gray-800/50">
                                    @if ($isDL)
                                        <div class="mx-auto h-4 w-4 rounded bg-blue-500 shadow-sm flex items-center justify-center" title="Dinas Luar">
                                            <span class="text-[7px] font-bold text-white leading-none">DL</span>
                                        </div>
                                    @elseif ($isTranslok)
                                        <div class="mx-auto h-4 w-4 rounded bg-teal-500 shadow-sm flex items-center justify-center" title="Translok">
                                            <span class="text-[7px] font-bold text-white leading-none">TL</span>
                                        </div>
                                    @else
                                        <div class="mx-auto h-1 w-1 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Legend --}}
    <div class="mt-4 flex items-center justify-start gap-4 text-xs text-gray-600 dark:text-gray-400 px-2">
        <div class="flex items-center gap-2 bg-white dark:bg-gray-900 px-2.5 py-1.5 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="h-4 w-4 rounded bg-blue-500 shadow-sm flex items-center justify-center">
                <span class="text-[7px] font-bold text-white leading-none">DL</span>
            </div>
            <span class="font-medium text-[10px] uppercase tracking-wide">Dinas Luar</span>
        </div>
        <div class="flex items-center gap-2 bg-white dark:bg-gray-900 px-2.5 py-1.5 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="h-4 w-4 rounded bg-teal-500 shadow-sm flex items-center justify-center">
                <span class="text-[7px] font-bold text-white leading-none">TL</span>
            </div>
            <span class="font-medium text-[10px] uppercase tracking-wide">Translok</span>
        </div>
    </div>
@endsection
