{{-- resources/views/components/profile/vis-rank-pegawai.blade.php --}}
@props(['rankPegawai', 'perPage' => 5])

<div class="bg-white rounded-2xl shadow p-6 dark:bg-gray-900 dark:border dark:border-gray-800">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                Penilaian Kinerja Pegawai
            </h2>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-16">
                        <div class="flex items-center gap-2">
                            <span>Rank</span>
                        </div>
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Nama Pegawai
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center">
                        RR Kirim
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center">
                        Rating ⭐
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center">
                        Rating %
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center">
                        Rata-rata
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($rankPegawai as $index => $pegawai)
                    @php
                        $rankingNumber = ($rankPegawai->currentPage() - 1) * $rankPegawai->perPage() + $index + 1;

                        // Determine row styling based on rank
                        if ($rankingNumber == 1) {
                            $rowBg =
                                'bg-gradient-to-r from-yellow-50/50 to-amber-50/50 dark:from-yellow-900/10 dark:to-amber-900/10';
                            $rankBadge =
                                'bg-gradient-to-br from-yellow-100 to-yellow-200 text-yellow-800 dark:from-yellow-900/40 dark:to-yellow-800/40 dark:text-yellow-300';
                        } elseif ($rankingNumber == 2) {
                            $rowBg =
                                'bg-gradient-to-r from-gray-50/50 to-gray-100/50 dark:from-gray-900/10 dark:to-gray-800/10';
                            $rankBadge =
                                'bg-gradient-to-br from-gray-100 to-gray-200 text-gray-800 dark:from-gray-900/40 dark:to-gray-800/40 dark:text-gray-300';
                        } elseif ($rankingNumber == 3) {
                            $rowBg =
                                'bg-gradient-to-r from-orange-50/50 to-amber-50/50 dark:from-orange-900/10 dark:to-amber-900/10';
                            $rankBadge =
                                'bg-gradient-to-br from-orange-100 to-orange-200 text-orange-800 dark:from-orange-900/40 dark:to-orange-800/40 dark:text-orange-300';
                        } else {
                            $rowBg = 'bg-white dark:bg-gray-900';
                            $rankBadge =
                                'bg-gradient-to-br from-gray-50 to-gray-100 text-gray-700 dark:from-gray-800/40 dark:to-gray-700/40 dark:text-gray-400';
                        }

                        $rating = round($pegawai->rating_kirim ?? 0, 1);
                        $fullStar = floor($rating);
                        $halfStar = $rating - $fullStar >= 0.5;
                        $emptyStar = 5 - $fullStar - ($halfStar ? 1 : 0);
                    @endphp

                    <tr
                        class="{{ $rowBg }} hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200">
                        {{-- Rank Number --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full {{ $rankBadge }} text-sm font-bold">
                                    {{ $rankingNumber }}
                                </div>
                            </div>
                        </td>

                        {{-- Nama Pegawai --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-800 dark:text-white">
                                {{ $pegawai->nama_pegawai ?? 'N/A' }}
                            </div>
                        </td>

                        {{-- RR Kirim --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($pegawai->rr_kirim ?? 0) >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : (($pegawai->rr_kirim ?? 0) >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300') }}">
                                {{ number_format($pegawai->rr_kirim ?? 0, 0) }}%
                            </span>
                        </td>

                        {{-- Rating Stars --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col items-center space-y-2">
                                <div class="flex justify-center items-center gap-1">
                                    {{-- Full star --}}
                                    @for ($i = 0; $i < $fullStar; $i++)
                                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966
                                            a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81
                                            l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966
                                            c.3.921-.755 1.688-1.54 1.118l-3.38-2.455
                                            a1 1 0 00-1.176 0l-3.38 2.455
                                            c-.784.57-1.838-.197-1.539-1.118l1.287-3.966
                                            a1 1 0 00-.364-1.118L2.05 9.393
                                            c-.783-.57-.38-1.81.588-1.81h4.173
                                            a1 1 0 00.95-.69l1.286-3.966z" />
                                        </svg>
                                    @endfor

                                    {{-- Half star --}}
                                    @if ($halfStar)
                                        <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 20 20">
                                            <defs>
                                                <linearGradient id="halfStar{{ $index }}">
                                                    <stop offset="50%" stop-color="currentColor" />
                                                    <stop offset="50%" stop-color="transparent" />
                                                </linearGradient>
                                            </defs>
                                            <path fill="url(#halfStar{{ $index }})" stroke="currentColor"
                                                stroke-width="1" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966
                                                a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81
                                                l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966
                                                c.3.921-.755 1.688-1.54 1.118l-3.38-2.455
                                                a1 1 0 00-1.176 0l-3.38 2.455
                                                c-.784.57-1.838-.197-1.539-1.118l1.287-3.966
                                                a1 1 0 00-.364-1.118L2.05 9.393
                                                c-.783-.57-.38-1.81.588-1.81h4.173
                                                a1 1 0 00.95-.69l1.286-3.966z" />
                                        </svg>
                                    @endif

                                    {{-- Empty star --}}
                                    @for ($i = 0; $i < $emptyStar; $i++)
                                        <svg class="w-4 h-4 text-gray-300 fill-current dark:text-gray-600"
                                            viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966
                                            a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81
                                            l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966
                                            c.3.921-.755 1.688-1.54 1.118l-3.38-2.455
                                            a1 1 0 00-1.176 0l-3.38 2.455
                                            c-.784.57-1.838-.197-1.539-1.118l1.287-3.966
                                            a1 1 0 00-.364-1.118L2.05 9.393
                                            c-.783-.57-.38-1.81.588-1.81h4.173
                                            a1 1 0 00.95-.69l1.286-3.966z" />
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ number_format($pegawai->rating_kirim ?? 0, 1) }}/5.0
                                </span>
                            </div>
                        </td>

                        {{-- Rating Percentage --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="font-medium text-gray-800 dark:text-white">
                                {{ number_format($pegawai->rating_persen ?? 0, 0) }}%
                            </span>
                        </td>

                        {{-- Rata-rata --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ ($pegawai->rata_rata ?? 0) >= 80 ? 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-300' : (($pegawai->rata_rata ?? 0) >= 60 ? 'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 dark:from-yellow-900/30 dark:to-amber-900/30 dark:text-yellow-300' : 'bg-gradient-to-r from-red-100 to-pink-100 text-red-800 dark:from-red-900/30 dark:to-pink-900/30 dark:text-red-300') }}">
                                {{ number_format($pegawai->rata_rata ?? 0, 0) }}%
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 2.197v-1a6 6 0 00-12 0v1" />
                                </svg>
                                <p class="text-lg font-medium">Tidak ada data pegawai</p>
                                <p class="text-sm mt-1">Belum ada penilaian kinerja untuk ditampilkan</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($rankPegawai->hasPages())
        <div class="mt-6 border-t border-gray-200 dark:border-gray-800 pt-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Menampilkan
                    <span class="font-medium">{{ $rankPegawai->firstItem() }}</span>
                    sampai
                    <span class="font-medium">{{ $rankPegawai->lastItem() }}</span>
                    dari
                    <span class="font-medium">{{ $rankPegawai->total() }}</span>
                    pegawai
                </div>
                <div class="flex items-center space-x-2">
                    {{-- Previous Page Link --}}
                    @if ($rankPegawai->onFirstPage())
                        <span
                            class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-400 dark:text-gray-600 cursor-not-allowed">
                            &laquo;
                        </span>
                    @else
                        <a href="{{ $rankPegawai->previousPageUrl() }}"
                            class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            &laquo;
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($rankPegawai->getUrlRange(1, $rankPegawai->lastPage()) as $page => $url)
                        @if ($page == $rankPegawai->currentPage())
                            <span
                                class="px-3 py-2 rounded-lg bg-brand-100 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 font-semibold">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($rankPegawai->hasMorePages())
                        <a href="{{ $rankPegawai->nextPageUrl() }}"
                            class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            &raquo;
                        </a>
                    @else
                        <span
                            class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-400 dark:text-gray-600 cursor-not-allowed">
                            &raquo;
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
