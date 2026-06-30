<div class="space-y-4">
    @foreach ($bidangs as $bidang)
        @php
            $totalKegiatan = $bidang->kegiatans->count();
            $totalSub = $bidang->kegiatans->sum(fn($k) => $k->subKegiatans ? $k->subKegiatans->count() : 0);
            $totalPenugasan = $bidang->kegiatans->sum(fn($k) => $k->subKegiatans ? $k->subKegiatans->sum(fn($s) => $s->penugasans ? $s->penugasans->count() : 0) : 0);
        @endphp

        <div x-data="{ open: false }" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">

            <!-- Header Fungsi -->
            <button @click="open = !open"
                class="flex w-full flex-col md:flex-row md:items-center justify-between p-4 md:p-5 text-left bg-gray-50/50 hover:bg-blue-50/50 dark:bg-gray-800 dark:hover:bg-gray-700/80 transition-colors">
                <div class="flex-1 mb-4 md:mb-0">
                    <h3 class="text-base font-bold text-gray-800 dark:text-white tracking-wide">{{ $bidang->nama_bidang }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Fungsi / Bidang Pimpinan</p>
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto md:mr-6">
                    <div class="flex-1 md:flex-none bg-blue-50 dark:bg-blue-900/30 px-4 py-2 rounded-lg border border-blue-100 dark:border-blue-800/50 text-center min-w-[100px]">
                        <span class="block text-[10px] uppercase tracking-wider text-blue-600 dark:text-blue-400 font-semibold mb-0.5">Kegiatan</span>
                        <span class="block text-xl font-bold text-blue-700 dark:text-blue-300">{{ $totalKegiatan }}</span>
                    </div>
                    <div class="flex-1 md:flex-none bg-teal-50 dark:bg-teal-900/30 px-4 py-2 rounded-lg border border-teal-100 dark:border-teal-800/50 text-center min-w-[100px]">
                        <span class="block text-[10px] uppercase tracking-wider text-teal-600 dark:text-teal-400 font-semibold mb-0.5">Sub Kegiatan</span>
                        <span class="block text-xl font-bold text-teal-700 dark:text-teal-300">{{ $totalSub }}</span>
                    </div>
                    <div class="flex-1 md:flex-none bg-purple-50 dark:bg-purple-900/30 px-4 py-2 rounded-lg border border-purple-100 dark:border-purple-800/50 text-center min-w-[100px]">
                        <span class="block text-[10px] uppercase tracking-wider text-purple-600 dark:text-purple-400 font-semibold mb-0.5">Penugasan</span>
                        <span class="block text-xl font-bold text-purple-700 dark:text-purple-300">{{ $totalPenugasan }}</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center justify-center h-10 w-10 rounded-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm text-gray-500 dark:text-gray-400">
                    <svg :class="{ 'rotate-180': open }" class="h-5 w-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </button>

            <!-- Accordion -->
            <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-gray-700">

                @if ($bidang->kegiatans->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 dark:border-gray-700">

                            <!-- HEADER -->
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border dark:border-gray-700">
                                        Kegiatan</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border dark:border-gray-700">
                                        Sub Kegiatan</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border dark:border-gray-700">
                                        Nama Pegawai</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border dark:border-gray-700">
                                        Jenis Kegiatan</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border dark:border-gray-700">
                                        Target</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase border dark:border-gray-700">
                                        Satuan</th>
                                </tr>
                            </thead>

                            <!-- BODY -->
                            <tbody>

                                @foreach ($bidang->kegiatans as $kegiatan)
                                    {{-- Kalau belum ada sub --}}
                                    @if ($kegiatan->subKegiatans->count() === 0)
                                        <tr>
                                            <td class="px-4 py-3 align-top border dark:border-gray-700">
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-gray-800 dark:text-gray-300">
                                                        {{ $kegiatan->nama_rk_kegiatan }}
                                                    </span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        Ketua:
                                                        {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                                    </span>
                                                </div>
                                            </td>

                                            <td
                                                class="px-4 py-3 text-gray-500 dark:text-gray-400 italic border dark:border-gray-700">
                                                Belum ada sub kegiatan
                                            </td>

                                            <td colspan="4"
                                                class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 italic border dark:border-gray-700">
                                                Belum ada penugasan
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($kegiatan->subKegiatans as $subIndex => $subKegiatan)
                                            @php($penugasanCount = max($subKegiatan->penugasans->count(), 1))

                                            {{-- Kalau belum ada penugasan --}}
                                            @if ($subKegiatan->penugasans->count() === 0)
                                                <tr>

                                                    {{-- MERGED KEGIATAN --}}
                                                    @if ($subIndex === 0)
                                                        <td rowspan="{{ $kegiatan->subKegiatans->sum(fn($s) => max($s->penugasans->count(), 1)) }}"
                                                            class="px-4 py-3 align-top border dark:border-gray-700">
                                                            <div class="flex flex-col">
                                                                <span
                                                                    class="font-medium text-gray-800 dark:text-gray-300">
                                                                    {{ $kegiatan->nama_rk_kegiatan }}
                                                                </span>
                                                                <span
                                                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                    Ketua:
                                                                    {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                                                </span>
                                                            </div>
                                                        </td>
                                                    @endif

                                                    {{-- MERGED SUB --}}
                                                    <td rowspan="1"
                                                        class="px-4 py-3 align-top border dark:border-gray-700 text-gray-800 dark:text-gray-300">
                                                        {{ $subKegiatan->nama_sub_kegiatan }}
                                                    </td>

                                                    <td colspan="4"
                                                        class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 italic border dark:border-gray-700">
                                                        Belum ada penugasan
                                                    </td>

                                                </tr>
                                            @else
                                                @foreach ($subKegiatan->penugasans as $penugasanIndex => $penugasan)
                                                    <tr>

                                                        {{-- KEGIATAN --}}
                                                        @if ($subIndex === 0 && $penugasanIndex === 0)
                                                            <td rowspan="{{ $kegiatan->subKegiatans->sum(fn($s) => max($s->penugasans->count(), 1)) }}"
                                                                class="px-4 py-3 align-top border dark:border-gray-700">
                                                                <div class="flex flex-col">
                                                                    <span
                                                                        class="font-medium text-gray-800 dark:text-gray-300">
                                                                        {{ $kegiatan->nama_rk_kegiatan }}
                                                                    </span>
                                                                    <span
                                                                        class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                        Ketua:
                                                                        {{ $kegiatan->penanggungJawab->nama_pegawai ?? '-' }}
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        @endif

                                                        {{-- SUB KEGIATAN --}}
                                                        @if ($penugasanIndex === 0)
                                                            <td rowspan="{{ $penugasanCount }}"
                                                                class="px-4 py-3 align-top border dark:border-gray-700 text-gray-800 dark:text-gray-300">
                                                                {{ $subKegiatan->nama_sub_kegiatan }}
                                                            </td>
                                                        @endif

                                                        <td
                                                            class="px-4 py-3 border dark:border-gray-700 text-gray-800 dark:text-gray-300">
                                                            {{ $penugasan->anggota->nama_pegawai ?? '-' }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 border dark:border-gray-700 text-gray-800 dark:text-gray-300">
                                                            {{ $penugasan->jenisKegiatan?->jenis_kegiatan ?? 'Isi Sendiri' }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 border dark:border-gray-700 text-gray-800 dark:text-gray-300">
                                                            {{ $penugasan->target ?? '-' }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 border dark:border-gray-700 text-gray-800 dark:text-gray-300">
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
                    <div class="p-4 text-center border-t dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                            Belum ada kegiatan untuk fungsi ini
                        </p>
                    </div>
                @endif

            </div>
        </div>
    @endforeach


    {{-- Kalau belum ada bidang --}}
    @if ($bidangs->count() === 0)
        <div
            class="text-center py-8 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Belum ada fungsi/bidang yang dibuat atau tidak ada data yang cocok dengan pencarian.
            </p>
        </div>
    @endif
</div>
