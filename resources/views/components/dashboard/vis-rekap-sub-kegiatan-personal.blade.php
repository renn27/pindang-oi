@props(['rekapSubKegiatan', 'selectedMonth', 'selectedYear'])

@php
    $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $bulanLabel = $namaBulan[$selectedMonth - 1] . ' ' . $selectedYear;

    $data = $rekapSubKegiatan;
    $details = collect($data->details ?? []);
    $totalSub = $data->total_sub_kegiatan ?? 0;
    $selesai = $data->sub_kegiatan_selesai ?? 0;
    $belumSelesai = $data->sub_kegiatan_belum_selesai ?? 0;
    $avgProgress = $data->average_progress ?? 0;
@endphp

<div class="bg-white rounded-2xl shadow p-6 dark:bg-gray-900 dark:border dark:border-gray-800"
     x-data="{ showDetail: true }">

    {{-- Header --}}
    <div class="mb-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg border border-brand-100 bg-brand-50 text-brand-600 dark:border-brand-900/40 dark:bg-brand-900/20 dark:text-brand-300 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Rekap Sub Kegiatan Ketua Tim</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Data periode: <span class="font-semibold text-brand-600 dark:text-brand-400">{{ $bulanLabel }}</span></p>
            </div>
        </div>

        {{-- Toggle Detail Button --}}
        <button @click="showDetail = !showDetail"
            class="flex-shrink-0 inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-brand-300 dark:hover:border-brand-700">
            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showDetail ? 'rotate-180' : ''"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
            <span x-text="showDetail ? 'Sembunyikan' : 'Tampilkan Detail'"></span>
        </button>
    </div>

    {{-- Summary Row --}}
    <div class="flex items-center gap-3 mb-3 flex-wrap">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 border border-green-200 dark:border-green-800/30">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            {{ $selesai }} Selesai
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $belumSelesai > 0 ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 border border-amber-200 dark:border-amber-800/30' : 'bg-gray-50 text-gray-400 dark:bg-gray-800 dark:text-gray-500 border border-gray-200 dark:border-gray-700' }}">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $belumSelesai }} Belum Selesai
        </span>
        <div class="flex items-center gap-2 ml-auto">
            <div class="w-32 h-1.5 bg-gray-200 rounded-full overflow-hidden dark:bg-gray-700">
                <div class="h-full rounded-full transition-all duration-700 {{ $avgProgress >= 100 ? 'bg-green-500' : 'bg-brand-500' }}"
                     style="width: {{ min($avgProgress, 100) }}%"></div>
            </div>
            <span class="text-xs font-bold {{ $avgProgress >= 100 ? 'text-green-600 dark:text-green-400' : 'text-brand-600 dark:text-brand-400' }}">{{ $avgProgress }}%</span>
        </div>
    </div>

    {{-- Detail Table --}}
    <div x-show="showDetail" x-transition x-cloak>
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600 dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400">
                            <th class="px-3 py-2 font-semibold">Nama Sub Kegiatan</th>
                            <th class="px-3 py-2 text-center font-semibold whitespace-nowrap">Tenggat Waktu</th>
                            <th class="px-3 py-2 text-center font-semibold whitespace-nowrap">Progres</th>
                            <th class="px-3 py-2 text-center font-semibold whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($details as $sub)
                            @php $pct = $sub['progress_percent'] ?? 0; @endphp
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-gray-800/40 transition-colors duration-150 cursor-pointer"
                                onclick="window.location.href='/kegiatan/{{ $sub['id_kegiatan'] }}/sub-kegiatan/{{ $sub['id_sub_kegiatan'] }}'">

                                <td class="px-3 py-2.5 font-semibold text-gray-800 dark:text-gray-200 max-w-xs break-words">
                                    <span class="hover:underline hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        {{ $sub['nama_sub_kegiatan'] }}
                                    </span>
                                </td>

                                <td class="px-3 py-2.5 text-center text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $sub['tanggal_mulai_formatted'] }} - {{ $sub['tanggal_selesai_formatted'] }}
                                </td>

                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 justify-center">
                                        <div class="w-24 h-1.5 bg-gray-200 rounded-full overflow-hidden dark:bg-gray-700 flex-shrink-0">
                                            <div class="h-full rounded-full transition-all duration-500 {{ $pct >= 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                                                 style="width: {{ min($pct, 100) }}%"></div>
                                        </div>
                                        <span class="text-[11px] font-semibold w-9 text-right flex-shrink-0 {{ $pct >= 100 ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400' }}">
                                            {{ $pct }}%
                                        </span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 flex-shrink-0">
                                            ({{ $sub['total_realisasi'] }}/{{ $sub['total_target'] }})
                                        </span>
                                    </div>
                                </td>

                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $pct >= 100
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                            : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                        {{ $pct >= 100 ? 'Selesai' : 'Dalam Proses' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-5 text-center text-gray-400 dark:text-gray-500">
                                    Tidak ada sub kegiatan untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
