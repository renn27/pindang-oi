{{-- resources/views/components/dashboard/vis-rank-pegawai.blade.php --}}
@props(['rankPegawaiAll', 'perPage' => 5])

<div
    x-data="{
        rawData: {{ Js::from($rankPegawaiAll) }},
        currentPage: 1,
        perPage: {{ $perPage }},
        isLoading: false,

        get totalPages() {
            return Math.ceil(this.rawData.length / this.perPage);
        },

        get paginatedData() {
            let start = (this.currentPage - 1) * this.perPage;
            return this.rawData.slice(start, start + this.perPage);
        },

        get firstItem() {
            return this.rawData.length === 0 ? 0 : (this.currentPage - 1) * this.perPage + 1;
        },

        get lastItem() {
            return Math.min(this.currentPage * this.perPage, this.rawData.length);
        },

        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.isLoading = true;
            // Simulate brief loading for UX feedback
            setTimeout(() => {
                this.currentPage = page;
                this.isLoading = false;
            }, 200);
        },

        getRankBadge(rankNumber) {
            if (rankNumber === 1) return 'bg-gradient-to-br from-yellow-100 to-yellow-200 text-yellow-800 dark:from-yellow-900/40 dark:to-yellow-800/40 dark:text-yellow-300';
            if (rankNumber === 2) return 'bg-gradient-to-br from-gray-100 to-gray-200 text-gray-800 dark:from-gray-900/40 dark:to-gray-800/40 dark:text-gray-300';
            if (rankNumber === 3) return 'bg-gradient-to-br from-orange-100 to-orange-200 text-orange-800 dark:from-orange-900/40 dark:to-orange-800/40 dark:text-orange-300';
            return 'bg-gradient-to-br from-gray-50 to-gray-100 text-gray-700 dark:from-gray-800/40 dark:to-gray-700/40 dark:text-gray-400';
        },

        getRrBadge(rr) {
            if (rr >= 80) return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
            if (rr >= 60) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300';
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
        },

        getRataBadge(rata) {
            if (rata >= 80) return 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-300';
            if (rata >= 60) return 'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 dark:from-yellow-900/30 dark:to-amber-900/30 dark:text-yellow-300';
            return 'bg-gradient-to-r from-red-100 to-pink-100 text-red-800 dark:from-red-900/30 dark:to-pink-900/30 dark:text-red-300';
        },

        getRatingStars(rating) {
            let full = Math.floor(rating);
            let half = (rating - full) >= 0.5 ? 1 : 0;
            let empty = 5 - full - half;
            return { full, half, empty };
        }
    }"
    class="bg-white rounded-2xl shadow p-6 dark:bg-gray-900 dark:border dark:border-gray-800"
>
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                Penilaian Kinerja Pegawai
            </h2>
            <span class="text-xs text-gray-400 dark:text-gray-500"
                x-text="rawData.length + ' pegawai'"
            ></span>
        </div>
    </div>

    {{-- Skeleton Loading --}}
    <div x-show="isLoading" x-cloak>
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        @foreach(['Rank', 'Nama Pegawai', 'RR Kirim', 'Rating ⭐', 'Rating %', 'Skor Cepat', 'Rata-rata', 'Aksi'] as $h)
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    @for ($i = 0; $i < 5; $i++)
                        <tr class="animate-pulse">
                            <td class="px-6 py-4"><div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700"></div></td>
                            <td class="px-6 py-4"><div class="h-4 w-32 rounded bg-gray-200 dark:bg-gray-700"></div></td>
                            <td class="px-6 py-4 text-center"><div class="h-5 w-14 rounded-full bg-gray-200 dark:bg-gray-700 mx-auto"></div></td>
                            <td class="px-6 py-4"><div class="flex gap-1 justify-center">@for($s=0;$s<5;$s++)<div class="h-4 w-4 rounded bg-gray-200 dark:bg-gray-700"></div>@endfor</div></td>
                            <td class="px-6 py-4 text-center"><div class="h-4 w-10 rounded bg-gray-200 dark:bg-gray-700 mx-auto"></div></td>
                            <td class="px-6 py-4 text-center"><div class="h-4 w-12 rounded bg-gray-200 dark:bg-gray-700 mx-auto"></div></td>
                            <td class="px-6 py-4 text-center"><div class="h-6 w-16 rounded-full bg-gray-200 dark:bg-gray-700 mx-auto"></div></td>
                            <td class="px-6 py-4 text-center"><div class="h-7 w-16 rounded-lg bg-gray-200 dark:bg-gray-700 mx-auto"></div></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    {{-- Table --}}
    <div x-show="!isLoading" class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-16">Rank</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nama Pegawai</th>
                    <th scope="col" class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center">RR Kirim</th>
                    <th scope="col" class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center">Rating ⭐</th>
                    <th scope="col" class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center">Rating %</th>
                    <th scope="col" class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center">Skor Cepat</th>
                    <th scope="col" class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center">Rata-rata</th>
                    <th scope="col" class="px-6 py-3 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                <template x-if="rawData.length === 0">
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 2.197v-1a6 6 0 00-12 0v1" />
                                </svg>
                                <p class="text-base font-medium">Tidak ada data pegawai</p>
                                <p class="text-sm">Belum ada penilaian kinerja untuk bulan ini</p>
                            </div>
                        </td>
                    </tr>
                </template>

                <template x-for="(pegawai, index) in paginatedData" :key="pegawai.id_pegawai">
                    <tr
                        :class="{
                            'bg-gradient-to-r from-yellow-50/50 to-amber-50/50 dark:from-yellow-900/10 dark:to-amber-900/10': (currentPage - 1) * perPage + index + 1 === 1,
                            'bg-gradient-to-r from-gray-50/50 to-gray-100/50 dark:from-gray-900/10 dark:to-gray-800/10': (currentPage - 1) * perPage + index + 1 === 2,
                            'bg-gradient-to-r from-orange-50/50 to-amber-50/50 dark:from-orange-900/10 dark:to-amber-900/10': (currentPage - 1) * perPage + index + 1 === 3
                        }"
                        class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-200"
                    >
                        {{-- Rank --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold"
                                    :class="getRankBadge((currentPage - 1) * perPage + index + 1)"
                                    x-text="(currentPage - 1) * perPage + index + 1"
                                ></div>
                            </div>
                        </td>

                        {{-- Nama --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-800 dark:text-white" x-text="pegawai.nama_pegawai || 'N/A'"></div>
                        </td>

                        {{-- RR Kirim --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                :class="getRrBadge(pegawai.rr_kirim ?? 0)"
                                x-text="Math.round(pegawai.rr_kirim ?? 0) + '%'"
                            ></span>
                        </td>

                        {{-- Rating Stars --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col items-center space-y-1">
                                <div class="flex justify-center items-center gap-0.5">
                                    <template x-for="n in getRatingStars(pegawai.rating_kirim ?? 0).full">
                                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.539-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.393c-.783-.57-.38-1.81.588-1.81h4.173a1 1 0 00.95-.69l1.286-3.966z"/>
                                        </svg>
                                    </template>
                                    <template x-if="getRatingStars(pegawai.rating_kirim ?? 0).half === 1">
                                        <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 20 20">
                                            <defs>
                                                <linearGradient :id="'hs-' + pegawai.id_pegawai">
                                                    <stop offset="50%" stop-color="currentColor"/>
                                                    <stop offset="50%" stop-color="transparent"/>
                                                </linearGradient>
                                            </defs>
                                            <path :fill="'url(#hs-' + pegawai.id_pegawai + ')'" stroke="currentColor" stroke-width="1" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.539-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.393c-.783-.57-.38-1.81.588-1.81h4.173a1 1 0 00.95-.69l1.286-3.966z"/>
                                        </svg>
                                    </template>
                                    <template x-for="n in getRatingStars(pegawai.rating_kirim ?? 0).empty">
                                        <svg class="w-4 h-4 text-gray-300 fill-current dark:text-gray-600" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.173c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.176 0l-3.38 2.455c-.784.57-1.838-.197-1.539-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.393c-.783-.57-.38-1.81.588-1.81h4.173a1 1 0 00.95-.69l1.286-3.966z"/>
                                        </svg>
                                    </template>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400" x-text="Number(pegawai.rating_kirim ?? 0).toFixed(1) + '/5.0'"></span>
                            </div>
                        </td>

                        {{-- Rating % --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="font-medium text-gray-800 dark:text-white" x-text="Math.round(pegawai.rating_persen ?? 0) + '%'"></span>
                        </td>

                        {{-- Skor Cepat --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="font-medium text-gray-800 dark:text-white" x-text="Number(pegawai.avg_skor_cepat ?? 0).toFixed(2) + '%'"></span>
                        </td>

                        {{-- Rata-rata --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                :class="getRataBadge(pegawai.rata_rata ?? 0)"
                                x-text="Number(pegawai.rata_rata ?? 0).toFixed(2) + '%'"
                            ></span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button
                                @click="$dispatch('open-calc-modal', {
                                    nama: pegawai.nama_pegawai,
                                    rr_kirim: Number(pegawai.rr_kirim ?? 0),
                                    rating_persen: Number(pegawai.rating_persen ?? 0),
                                    skor_cepat: Number(pegawai.avg_skor_cepat ?? 0),
                                    rata_rata: Number(pegawai.rata_rata ?? 0),
                                    details: pegawai.details ?? []
                                })"
                                class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-colors"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Rumus
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- Client-side Pagination --}}
    <div
        x-show="totalPages > 1 && !isLoading"
        x-cloak
        class="mt-4 border-t border-gray-200 dark:border-gray-800 pt-4"
    >
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700 dark:text-gray-300">
                Menampilkan
                <span class="font-medium" x-text="firstItem"></span> s/d
                <span class="font-medium" x-text="lastItem"></span>
                dari <span class="font-medium" x-text="rawData.length"></span> pegawai
            </div>
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                {{-- Prev --}}
                <button
                    @click="goToPage(currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-40 disabled:cursor-not-allowed dark:ring-gray-600 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                >
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                </button>

                {{-- Page numbers --}}
                <template x-for="page in totalPages" :key="page">
                    <button
                        @click="goToPage(page)"
                        :class="currentPage === page
                            ? 'bg-brand-600 dark:bg-brand-500 text-white z-10'
                            : 'text-gray-900 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600 cursor-pointer transition-colors"
                        x-text="page"
                    ></button>
                </template>

                {{-- Next --}}
                <button
                    @click="goToPage(currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-40 disabled:cursor-not-allowed dark:ring-gray-600 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                >
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                </button>
            </nav>
        </div>
    </div>

    {{-- Modal Perhitungan Rumus Component --}}
    <x-dashboard.modal-perhitungan-rumus />
</div>
