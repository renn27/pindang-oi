@props(['rekapAnggota', 'selectedMonth', 'selectedYear', 'perPage' => 10, 'perPageOptions' => [10, 25, 50, 100]])

<div class="bg-white rounded-2xl shadow p-6 dark:bg-gray-900 dark:border dark:border-gray-800">
    <div x-data="{
            search: '',
            sortCol: 'nama_pegawai',
            sortAsc: true,
            currentPage: 1,
            perPage: {{ (int) $perPage }},
            perPageOptions: {{ Js::from($perPageOptions) }},
            rawData: {{ Js::from($rekapAnggota) }},
            selectedPegawai: null,
            todoLoading: false,
            todoHtml: '',
            isModalOpen: false,
            activeTabModal: 'revisi',

            async openTodoModal(pegawai) {
                this.selectedPegawai = pegawai;
                this.todoLoading = true;
                this.todoHtml = '';
                this.activeTabModal = 'revisi';
                
                // Dispatch event untuk membuka smart modal
                this.$dispatch('open-smart-modal', {
                    modalId: 'modal-todo-list-anggota',
                    data: pegawai
                });
                
                try {
                    let response = await fetch(`/pegawai/${pegawai.id_pegawai}/todo-list`);
                    if (response.ok) {
                        this.todoHtml = await response.text();
                    } else {
                        this.todoHtml = '<div class=\'text-center text-red-500 py-6 font-semibold\'>Gagal memuat To Do List anggota.</div>';
                    }
                } catch (error) {
                    console.error(error);
                    this.todoHtml = '<div class=\'text-center text-red-500 py-6 font-semibold\'>Terjadi kesalahan saat memuat data.</div>';
                } finally {
                    this.todoLoading = false;
                }
            },

            async sendReminder() {
                if (!this.selectedPegawai) return;
                
                if (window.SwalHelper && window.SwalHelper.fire) {
                    window.SwalHelper.fire({
                        html: `
                            <div class='flex items-center gap-3 p-4'>
                                <div class='flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30'>
                                    <svg class='animate-spin h-5 w-5 text-indigo-600 dark:text-indigo-400' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'>
                                        <circle class='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle>
                                        <path class='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'></path>
                                    </svg>
                                </div>
                                <div class='flex-1 min-w-0 text-left'>
                                    <p class='font-medium text-gray-900 dark:text-white'>Mengirim...</p>
                                    <p class='mt-1 text-sm text-gray-600 dark:text-gray-300'>Mengirim push notification ke pegawai.</p>
                                </div>
                            </div>
                        `,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        customClass: {
                            popup: '!rounded-3xl !border !border-gray-200 !shadow-2xl !bg-white dark:!border-gray-700 dark:!bg-gray-900 !p-0 !max-w-sm',
                            htmlContainer: '!p-0 !m-0',
                            container: '!p-5'
                        }
                    });
                }

                try {
                    let response = await fetch(`/pegawai/${this.selectedPegawai.id_pegawai}/send-todo-reminder`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            month: {{ $selectedMonth }},
                            year: {{ $selectedYear }}
                        })
                    });

                    let result = await response.json();

                    if (window.SwalHelper && window.SwalHelper.close) {
                        window.SwalHelper.close();
                    }

                    if (response.ok && result.success) {
                        if (window.SwalHelper && window.SwalHelper.success) {
                            window.SwalHelper.success(result.message);
                        }
                    } else {
                        let errMsg = result.message || 'Gagal mengirim pengingat.';
                        if (window.SwalHelper && window.SwalHelper.error) {
                            window.SwalHelper.error(errMsg);
                        }
                    }
                } catch (error) {
                    console.error(error);
                    if (window.SwalHelper && window.SwalHelper.close) {
                        window.SwalHelper.close();
                    }
                    let errMsg = 'Terjadi kesalahan sistem saat mengirim pengingat.';
                    if (window.SwalHelper && window.SwalHelper.error) {
                        window.SwalHelper.error(errMsg);
                    }
                }
            },
            
            get filteredData() {
                let data = this.rawData;
                
                // Reset ke halaman 1 setiap kali search berubah
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
                    // Urutan default: Teks = A ke Z, Angka = Besar ke Kecil
                    this.sortAsc = (col === 'nama_pegawai');
                }
            },

            updatePerPage(value) {
                this.perPage = Number(value);
                this.currentPage = 1;
                this.syncPerPageToUrl('rekap_per_page', this.perPage);
            },

            syncPerPageToUrl(key, value) {
                const url = new URL(window.location.href);
                url.searchParams.set(key, value);
                window.history.replaceState({}, '', `${url.pathname}?${url.searchParams.toString()}${url.hash}`);
            }
        }">
        <!-- Header Tabel & Search Bar -->
        <div class="mb-3 mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    Rekap Penugasan Seluruh Anggota
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
                        placeholder="Cari nama pegawai...">
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
                                class="cursor-pointer border-b border-gray-300 dark:border-gray-600 px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors select-none group">
                                <div class="flex items-center justify-between gap-2">
                                    <span>Nama Anggota</span>
                                    <span class="text-gray-300 group-hover:text-gray-500" x-show="sortCol === 'nama_pegawai'" :class="{'rotate-180': sortAsc, 'text-gray-700 dark:text-white': sortCol === 'nama_pegawai'}">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </span>
                                </div>
                            </th>
                            
                            <th @click="sortBy('total_penugasan')" 
                                class="cursor-pointer border-b border-l border-gray-300 dark:border-gray-600 px-4 py-2 text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors select-none whitespace-nowrap group">
                                <div class="flex items-center justify-center gap-1">
                                    Jml. Penugasan
                                    <span class="text-gray-300 group-hover:text-gray-500" x-show="sortCol === 'total_penugasan'" :class="{'rotate-180': sortAsc, 'text-gray-700 dark:text-white': sortCol === 'total_penugasan'}">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </span>
                                </div>
                            </th>

                            <th @click="sortBy('total_target')" 
                                class="cursor-pointer border-b border-l border-gray-300 dark:border-gray-600 px-4 py-2 text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors select-none whitespace-nowrap group text-purple-700 dark:text-purple-400">
                                <div class="flex items-center justify-center gap-1">
                                    Total Target
                                    <span class="text-purple-300 group-hover:text-purple-500" x-show="sortCol === 'total_target'" :class="{'rotate-180': sortAsc, 'text-purple-700 dark:text-purple-300': sortCol === 'total_target'}">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </span>
                                </div>
                            </th>

                            <th @click="sortBy('total_belum_dikerjakan')" 
                                class="cursor-pointer border-b border-l border-gray-300 dark:border-gray-600 px-4 py-2 text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors select-none whitespace-nowrap group text-gray-500 dark:text-gray-400">
                                <div class="flex items-center justify-center gap-1">
                                    Belum Dikerjakan
                                    <span class="text-gray-300 group-hover:text-gray-500" x-show="sortCol === 'total_belum_dikerjakan'" :class="{'rotate-180': sortAsc, 'text-gray-500 dark:text-gray-300': sortCol === 'total_belum_dikerjakan'}">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </span>
                                </div>
                            </th>

                            <th @click="sortBy('total_dikirim')" 
                                class="cursor-pointer border-b border-l border-gray-300 dark:border-gray-600 px-4 py-2 text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors select-none whitespace-nowrap group text-blue-700 dark:text-blue-400">
                                <div class="flex items-center justify-center gap-1">
                                    Dikirim
                                    <span class="text-blue-300 group-hover:text-blue-500" x-show="sortCol === 'total_dikirim'" :class="{'rotate-180': sortAsc, 'text-blue-700 dark:text-blue-300': sortCol === 'total_dikirim'}">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </span>
                                </div>
                            </th>

                            <th @click="sortBy('total_diperiksa')" 
                                class="cursor-pointer border-b border-l border-gray-300 dark:border-gray-600 px-4 py-2 text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors select-none whitespace-nowrap group text-amber-700 dark:text-amber-400">
                                <div class="flex items-center justify-center gap-1">
                                    Diperiksa
                                    <span class="text-amber-300 group-hover:text-amber-500" x-show="sortCol === 'total_diperiksa'" :class="{'rotate-180': sortAsc, 'text-amber-700 dark:text-amber-300': sortCol === 'total_diperiksa'}">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </span>
                                </div>
                            </th>

                            <th @click="sortBy('total_revisi')" 
                                class="cursor-pointer border-b border-l border-gray-300 dark:border-gray-600 px-4 py-2 text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors select-none whitespace-nowrap group text-red-700 dark:text-red-400">
                                <div class="flex items-center justify-center gap-1">
                                    Revisi
                                    <span class="text-red-300 group-hover:text-red-500" x-show="sortCol === 'total_revisi'" :class="{'rotate-180': sortAsc, 'text-red-700 dark:text-red-300': sortCol === 'total_revisi'}">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </span>
                                </div>
                            </th>

                            <th @click="sortBy('total_diterima')" 
                                class="cursor-pointer border-b border-l border-gray-300 dark:border-gray-600 px-4 py-2 text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors select-none whitespace-nowrap group text-green-700 dark:text-green-400">
                                <div class="flex items-center justify-center gap-1">
                                    Diterima
                                    <span class="text-green-300 group-hover:text-green-500" x-show="sortCol === 'total_diterima'" :class="{'rotate-180': sortAsc, 'text-green-700 dark:text-green-300': sortCol === 'total_diterima'}">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(pegawai, index) in paginatedData" :key="pegawai.id_pegawai">
                            <tr 
                                @if(auth()->user()?->isSuperUser())
                                    @click="openTodoModal(pegawai)"
                                    class="cursor-pointer hover:bg-indigo-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700 last:border-b-0"
                                @else
                                    class="hover:bg-indigo-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700 last:border-b-0"
                                @endif
                                :class="{'bg-gray-50/70 dark:bg-gray-800/40': index % 2 === 1}"
                            >
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white" x-text="pegawai.nama_pegawai"></td>
                                
                                <td class="border-l border-gray-200 dark:border-gray-700 px-4 py-2 text-center font-semibold bg-gray-50/50 dark:bg-transparent" x-text="pegawai.total_penugasan"></td>
                                
                                <td class="border-l border-gray-200 dark:border-gray-700 px-4 py-2 text-center font-semibold text-purple-700 dark:text-purple-400" x-text="pegawai.total_target ?? 0"></td>
                                
                                <td class="border-l border-gray-200 dark:border-gray-700 px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-400" x-text="pegawai.total_belum_dikerjakan ?? 0"></td>
                                
                                <td class="border-l border-gray-200 dark:border-gray-700 px-4 py-2 text-center font-medium text-blue-700 dark:text-blue-400" x-text="pegawai.total_dikirim"></td>
                                
                                <td class="border-l border-gray-200 dark:border-gray-700 px-4 py-2 text-center font-medium text-amber-700 dark:text-amber-400" x-text="pegawai.total_diperiksa"></td>
                                
                                <td class="border-l border-gray-200 dark:border-gray-700 px-4 py-2 text-center font-medium text-red-700 dark:text-red-400" x-text="pegawai.total_revisi"></td>
                                
                                <td class="border-l border-gray-200 dark:border-gray-700 px-4 py-2 text-center font-medium text-green-700 dark:text-green-400" x-text="pegawai.total_diterima"></td>
                            </tr>
                        </template>
                        
                        <tr x-show="filteredData.length === 0" x-cloak>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <span x-text="search ? 'Tidak ada pegawai yang menggunakan nama tersebut.' : 'Belum ada data rekap penugasan bulan ini.'"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Bagian Pagination Controls -->
            <div class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6 dark:border-gray-700 dark:bg-gray-800/50" x-show="totalPages > 1" x-cloak>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Menampilkan <span class="font-medium" x-text="((currentPage - 1) * perPage) + 1"></span> s/d 
                            <span class="font-medium" x-text="Math.min(currentPage * perPage, filteredData.length)"></span>
                            dari <span class="font-medium" x-text="filteredData.length"></span> pegawai
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <!-- Tombol Prev -->
                            <button @click="if(currentPage > 1) currentPage--" :disabled="currentPage === 1" 
                                class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed dark:ring-gray-600 dark:hover:bg-gray-700 cursor-pointer">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <!-- Loop Tombol Angka -->
                            <template x-for="page in totalPages" :key="page">
                                <button @click="currentPage = page" 
                                    :class="{'bg-indigo-600 text-white focus-visible:outline-indigo-600 dark:bg-indigo-500': currentPage === page, 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700': currentPage !== page}"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 cursor-pointer"
                                    x-text="page">
                                </button>
                            </template>

                            <!-- Tombol Next -->
                            <button @click="if(currentPage < totalPages) currentPage++" :disabled="currentPage === totalPages"
                                class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed dark:ring-gray-600 dark:hover:bg-gray-700 cursor-pointer">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
                
                <!-- Mobile Pagination View (Simple) -->
                <div class="flex flex-1 justify-between sm:hidden">
                    <button @click="if(currentPage > 1) currentPage--" :disabled="currentPage === 1" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Previous</button>
                    <div class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400" x-text="currentPage + ' / ' + totalPages"></div>
                    <button @click="if(currentPage < totalPages) currentPage++" :disabled="currentPage === totalPages" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Next</button>
                </div>
            </div>

            <!-- Modal To Do List Anggota -->
            @if(auth()->user()?->isSuperUser())
                <x-ui.smart-modal id="modal-todo-list-anggota" class="max-w-5xl" :isOpen="false">
                    <div class="relative flex max-h-[90vh] w-full flex-col overflow-visible rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">
                        <!-- HEADER -->
                        <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                            <h4 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                                <span class="p-1.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                To Do List: <span class="text-indigo-600 dark:text-indigo-400" x-text="selectedPegawai ? selectedPegawai.nama_pegawai : ''"></span>
                            </h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Daftar tugas mandiri anggota tim yang belum selesai dikerjakan.
                            </p>
                        </div>

                        <!-- BODY -->
                        <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900 max-h-[60vh]">
                            <!-- Loading State -->
                            <div x-show="todoLoading" class="flex flex-col items-center justify-center py-20 gap-4">
                                <svg class="animate-spin h-8 w-8 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Memuat To Do List...</p>
                            </div>

                            <!-- Content State -->
                            <div x-show="!todoLoading" x-html="todoHtml" class="transition-opacity duration-200"></div>
                        </div>

                        <!-- FOOTER -->
                        <div class="shrink-0 border-t border-gray-200 px-6 py-4 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
                            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                                <button @click="open = false" type="button" class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
                                    Batal
                                </button>
                                
                                <button type="button" @click="sendReminder()" class="flex w-full justify-center items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 sm:w-auto transition shadow-sm dark:bg-indigo-500 dark:hover:bg-indigo-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    Kirim Pengingat Push
                                </button>
                            </div>
                        </div>
                    </div>
                </x-ui.smart-modal>
            @endif
        </div>
    </div>
</div>
