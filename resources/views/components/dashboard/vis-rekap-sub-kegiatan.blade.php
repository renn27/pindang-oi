@props(['rekapSubKegiatan', 'selectedMonth', 'selectedYear', 'perPage' => 10, 'perPageOptions' => [10, 25, 50, 100]])

<div class="bg-white rounded-2xl shadow p-6 dark:bg-gray-900 dark:border dark:border-gray-800">
    <div x-data="{
            search: '',
            sortCol: 'nama_pegawai',
            sortAsc: true,
            currentPage: 1,
            perPage: {{ (int) $perPage }},
            perPageOptions: {{ Js::from($perPageOptions) }},
            rawData: {{ Js::from($rekapSubKegiatan) }},
            expandedRows: [],

            toggleRow(id) {
                if (this.expandedRows.includes(id)) {
                    this.expandedRows = this.expandedRows.filter(rowId => rowId !== id);
                } else {
                    this.expandedRows.push(id);
                }
            },

            isExpanded(id) {
                return this.expandedRows.includes(id);
            },
            
            get filteredData() {
                let data = this.rawData;
                
                // 1. Search filter
                if (this.search !== '') {
                    this.currentPage = 1;
                    data = data.filter(item => 
                        item.nama_pegawai.toLowerCase().includes(this.search.toLowerCase())
                    );
                }
                
                // 2. Sorting
                data = data.sort((a, b) => {
                    let valA = a[this.sortCol];
                    let valB = b[this.sortCol];
                    
                    if (typeof valA === 'string') {
                        return this.sortAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    }
                    
                    return this.sortAsc ? (valA - valB) : (valB - valA);
                });
                
                return data;
            },

            get paginatedData() {
                let start = (this.currentPage - 1) * this.perPage;
                let end = start + this.perPage;
                return this.filteredData.slice(start, end);
            },

            get totalPages() {
                return Math.ceil(this.filteredData.length / this.perPage);
            },
            
            sortBy(col) {
                if (this.sortCol === col) {
                    this.sortAsc = !this.sortAsc;
                } else {
                    this.sortCol = col;
                    this.sortAsc = (col === 'nama_pegawai');
                }
            },

            updatePerPage(value) {
                this.perPage = Number(value);
                this.currentPage = 1;
            }
        }">
        <!-- Header & Search -->
        <div class="mb-3 mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    Rekap Sub Kegiatan Ketua Tim
                </h3>
                @php
                    $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                @endphp
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Data periode: <span class="font-semibold text-brand-600 dark:text-brand-400">{{ $namaBulan[$selectedMonth - 1] }} {{ $selectedYear }}</span></p>
            </div>
            
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                <label class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                    <span>Tampilkan</span>
                    <select
                        x-model.number="perPage"
                        @change="updatePerPage($event.target.value)"
                        class="rounded-md border border-gray-300 bg-white px-2 py-1.5 text-xs font-semibold text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                    >
                        @foreach($perPageOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                    <span>data</span>
                </label>

                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input x-model="search" type="text" 
                        class="block w-full rounded-md border border-gray-300 bg-white py-1.5 pl-10 pr-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400" 
                        placeholder="Cari nama ketua tim...">
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-hidden rounded border border-gray-300 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th @click="sortBy('nama_pegawai')" 
                                class="cursor-pointer border-b border-gray-300 dark:border-gray-600 px-6 py-3 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors select-none group">
                                <div class="flex items-center justify-between gap-2">
                                    <span>Nama Ketua Tim</span>
                                    <span class="text-gray-300 group-hover:text-gray-500" x-show="sortCol === 'nama_pegawai'" :class="{'rotate-180': sortAsc, 'text-gray-700 dark:text-white': sortCol === 'nama_pegawai'}">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </span>
                                </div>
                            </th>
                            
                            <th @click="sortBy('sub_kegiatan_selesai')" 
                                class="cursor-pointer border-b border-l border-gray-300 dark:border-gray-600 px-4 py-3 text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors select-none whitespace-nowrap group text-green-700 dark:text-green-400">
                                <div class="flex items-center justify-center gap-1">
                                    Selesai (100%)
                                    <span class="text-gray-300 group-hover:text-gray-500" x-show="sortCol === 'sub_kegiatan_selesai'" :class="{'rotate-180': sortAsc, 'text-green-700 dark:text-white': sortCol === 'sub_kegiatan_selesai'}">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </span>
                                </div>
                            </th>

                            <th @click="sortBy('sub_kegiatan_belum_selesai')" 
                                class="cursor-pointer border-b border-l border-gray-300 dark:border-gray-600 px-4 py-3 text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors select-none whitespace-nowrap group text-amber-700 dark:text-amber-400">
                                <div class="flex items-center justify-center gap-1">
                                    Belum Selesai
                                    <span class="text-gray-300 group-hover:text-gray-500" x-show="sortCol === 'sub_kegiatan_belum_selesai'" :class="{'rotate-180': sortAsc, 'text-amber-700 dark:text-white': sortCol === 'sub_kegiatan_belum_selesai'}">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </span>
                                </div>
                            </th>

                            <th @click="sortBy('average_progress')" 
                                class="cursor-pointer border-b border-l border-gray-300 dark:border-gray-600 px-6 py-3 text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors select-none whitespace-nowrap group text-brand-700 dark:text-brand-400">
                                <div class="flex items-center justify-center gap-1">
                                    Rata-rata Progres
                                    <span class="text-gray-300 group-hover:text-gray-500" x-show="sortCol === 'average_progress'" :class="{'rotate-180': sortAsc, 'text-brand-700 dark:text-white': sortCol === 'average_progress'}">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </span>
                                </div>
                            </th>
                            <th class="border-b border-l border-gray-300 dark:border-gray-600 px-4 py-3 text-center w-20">
                                Detail
                            </th>
                        </tr>
                    </thead>
                    <template x-for="(pegawai, index) in paginatedData" :key="pegawai.id_pegawai">
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                            <tr 
                                @click="toggleRow(pegawai.id_pegawai)"
                                class="cursor-pointer hover:bg-indigo-50/50 dark:hover:bg-gray-700/60 transition-colors duration-150"
                                :class="{'bg-gray-50/30 dark:bg-gray-800/10': index % 2 === 1}"
                            >
                                <!-- Nama & Photo -->
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold text-xs uppercase overflow-hidden flex-shrink-0">
                                            <template x-if="pegawai.photo">
                                                <img :src="'/storage/' + pegawai.photo" class="h-full w-full object-cover">
                                            </template>
                                            <template x-if="!pegawai.photo">
                                                <span x-text="pegawai.nama_pegawai.substring(0, 2)"></span>
                                            </template>
                                        </div>
                                        <span x-text="pegawai.nama_pegawai"></span>
                                    </div>
                                </td>
                                
                                <!-- Selesai (100%) -->
                                <td class="border-l border-gray-200 dark:border-gray-700 px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800/30"
                                        x-text="pegawai.sub_kegiatan_selesai + ' Sub Kegiatan'">
                                    </span>
                                </td>
                                
                                <!-- Belum Selesai -->
                                <td class="border-l border-gray-200 dark:border-gray-700 px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                        :class="pegawai.sub_kegiatan_belum_selesai > 0 ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 border border-amber-200 dark:border-amber-800/30' : 'bg-gray-50 text-gray-500 dark:bg-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700'"
                                        x-text="pegawai.sub_kegiatan_belum_selesai + ' Sub Kegiatan'">
                                    </span>
                                </td>
                                
                                <!-- Rata-rata Progres -->
                                <td class="border-l border-gray-200 dark:border-gray-700 px-6 py-4 text-center">
                                    <div class="flex items-center gap-3 justify-center">
                                        <div class="w-24 h-1.5 bg-gray-200 rounded-full overflow-hidden dark:bg-gray-700 flex-shrink-0">
                                            <div class="h-full rounded-full transition-all duration-500"
                                                :style="'width: ' + pegawai.average_progress + '%'"
                                                :class="pegawai.average_progress >= 100 ? 'bg-green-500' : 'bg-brand-500'">
                                            </div>
                                        </div>
                                        <span class="text-xs font-bold w-12 text-right"
                                            :class="pegawai.average_progress >= 100 ? 'text-green-600 dark:text-green-400' : 'text-brand-600 dark:text-brand-400'"
                                            x-text="pegawai.average_progress + '%'">
                                        </span>
                                    </div>
                                </td>

                                <!-- Toggle Arrow -->
                                <td class="border-l border-gray-200 dark:border-gray-700 px-4 py-4 text-center">
                                    <button type="button" class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform duration-200" 
                                            :class="{'rotate-180': isExpanded(pegawai.id_pegawai)}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>

                            <!-- Collapsible Detail Row -->
                            <tr x-show="isExpanded(pegawai.id_pegawai)" x-cloak x-transition class="bg-gray-50/50 dark:bg-gray-900/40">
                                <td colspan="5" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-950 p-4">
                                        <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Detail Sub Kegiatan yang Diketuai</h4>
                                        
                                        <table class="w-full text-left text-xs text-gray-600 dark:text-gray-400">
                                            <thead>
                                                <tr class="border-b border-gray-200 dark:border-gray-800 text-gray-500">
                                                    <th class="py-2 font-semibold">Nama Sub Kegiatan</th>
                                                    <th class="py-2 text-center font-semibold">Tenggat Waktu</th>
                                                    <th class="py-2 text-center font-semibold">Progres Tugas</th>
                                                    <th class="py-2 text-center font-semibold">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-900">
                                                <template x-for="sub in pegawai.details" :key="sub.id_sub_kegiatan">
                                                    <tr 
                                                        @click="window.location.href = '/kegiatan/' + sub.id_kegiatan + '/sub-kegiatan/' + sub.id_sub_kegiatan"
                                                        class="cursor-pointer hover:bg-indigo-50/30 dark:hover:bg-gray-800/40 transition-colors duration-150"
                                                    >
                                                        <!-- Nama Sub Kegiatan -->
                                                        <td class="py-3 font-semibold text-gray-800 dark:text-gray-200 max-w-sm break-words">
                                                            <span class="hover:underline hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                                                               x-text="sub.nama_sub_kegiatan"></span>
                                                        </td>
                                                        
                                                        <!-- Tanggal -->
                                                        <td class="py-3 text-center text-gray-500 dark:text-gray-400 whitespace-nowrap" 
                                                            x-text="sub.tanggal_mulai_formatted + ' - ' + sub.tanggal_selesai_formatted">
                                                        </td>
                                                        
                                                        <!-- Progres Bar -->
                                                        <td class="py-3 whitespace-nowrap">
                                                            <div class="flex items-center gap-2 justify-center">
                                                                <div class="w-28 h-1.5 bg-gray-200 rounded-full overflow-hidden dark:bg-gray-700 flex-shrink-0">
                                                                    <div class="h-full rounded-full transition-all duration-500"
                                                                        :style="'width: ' + sub.progress_percent + '%'"
                                                                        :class="sub.progress_percent >= 100 ? 'bg-green-500' : 'bg-blue-500'">
                                                                    </div>
                                                                </div>
                                                                <span class="text-[11px] font-semibold w-10 text-right flex-shrink-0"
                                                                    :class="sub.progress_percent >= 100 ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400'"
                                                                    x-text="sub.progress_percent + '%'">
                                                                </span>
                                                                <span class="text-[11px] text-gray-400 dark:text-gray-500 flex-shrink-0"
                                                                    x-text="'(' + sub.total_realisasi + '/' + sub.total_target + ')'">
                                                                </span>
                                                            </div>
                                                        </td>
                                                        
                                                        <!-- Status Badge -->
                                                        <td class="py-3 text-center whitespace-nowrap">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold"
                                                                :class="sub.progress_percent >= 100 
                                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' 
                                                                    : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'"
                                                                x-text="sub.progress_percent >= 100 ? 'Selesai' : 'Dalam Proses'">
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </template>
                                                
                                                <template x-if="pegawai.details.length === 0">
                                                    <tr>
                                                        <td colspan="4" class="py-4 text-center text-gray-400">Tidak ada sub kegiatan.</td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </template>

                    <tbody x-show="filteredData.length === 0" x-cloak>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <span x-text="search ? 'Tidak ada ketua tim yang menggunakan nama tersebut.' : 'Belum ada data rekap sub kegiatan bulan ini.'"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6 dark:border-gray-700 dark:bg-gray-800/50" x-show="totalPages > 1" x-cloak>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Menampilkan <span class="font-medium" x-text="((currentPage - 1) * perPage) + 1"></span> s/d 
                            <span class="font-medium" x-text="Math.min(currentPage * perPage, filteredData.length)"></span>
                            dari <span class="font-medium" x-text="filteredData.length"></span> Ketua Tim
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <button @click="if(currentPage > 1) currentPage--" :disabled="currentPage === 1" 
                                class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed dark:ring-gray-600 dark:hover:bg-gray-700 cursor-pointer">
                                <span class="sr-only">Sebelumnya</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <template x-for="page in totalPages" :key="page">
                                <button @click="currentPage = page" 
                                    :class="{'bg-indigo-600 text-white focus-visible:outline-indigo-600 dark:bg-indigo-500': currentPage === page, 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700': currentPage !== page}"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 cursor-pointer"
                                    x-text="page">
                                </button>
                            </template>

                            <button @click="if(currentPage < totalPages) currentPage++" :disabled="currentPage === totalPages"
                                class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed dark:ring-gray-600 dark:hover:bg-gray-700 cursor-pointer">
                                <span class="sr-only">Berikutnya</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
                
                <div class="flex flex-1 justify-between sm:hidden">
                    <button @click="if(currentPage > 1) currentPage--" :disabled="currentPage === 1" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Sebelumnya</button>
                    <div class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400" x-text="currentPage + ' / ' + totalPages"></div>
                    <button @click="if(currentPage < totalPages) currentPage++" :disabled="currentPage === totalPages" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Berikutnya</button>
                </div>
            </div>
        </div>
    </div>
</div>
