@props([
    'id' => 'calcModal',
])

<div x-data="{ open: false, calcData: {} }"
    x-show="open"
    @open-calc-modal.window="calcData = $event.detail; open = true"
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-[99999]"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
    x-cloak>
    
    <!-- Backdrop -->
    <div x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto custom-scrollbar">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            
            <!-- Modal Panel -->
            <div x-show="open"
                @click.away="open = false"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl dark:bg-gray-900 border border-gray-100 dark:border-gray-800 flex flex-col max-h-[85vh]">
                
                <!-- Header -->
                <div class="flex-none items-center justify-between rounded-t-2xl border-b border-gray-200 px-5 py-4 dark:border-gray-700 flex bg-white dark:bg-gray-900 z-10">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Perhitungan Kinerja Akhir
                    </h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="flex-1 overflow-y-auto p-5 sm:p-6 custom-scrollbar bg-white dark:bg-gray-900">
                    
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                        Berikut rincian perhitungan skor rata-rata untuk pegawai <strong x-text="calcData.nama" class="text-gray-900 dark:text-white font-bold"></strong>.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                            <h4 class="mb-3 text-sm font-bold text-gray-800 dark:text-gray-200 border-b border-gray-200 dark:border-gray-700 pb-2">Formula Perhitungan (Bobot Penilaian)</h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium">1. Rata-rata Response Rate Kirim <span class="text-xs bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded ml-1 dark:bg-blue-900/30 dark:text-blue-300">40%</span></span>
                                    <span class="text-gray-800 dark:text-gray-200 font-mono text-xs bg-white dark:bg-gray-800 px-2 py-1 rounded border border-gray-200 dark:border-gray-700"><span x-text="calcData.rr_kirim"></span>% &times; 0.40 = <strong x-text="(calcData.rr_kirim * 0.40).toFixed(2)"></strong></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium">2. Rata-rata Rating Kirim <span class="text-xs bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded ml-1 dark:bg-yellow-900/30 dark:text-yellow-300">35%</span></span>
                                    <span class="text-gray-800 dark:text-gray-200 font-mono text-xs bg-white dark:bg-gray-800 px-2 py-1 rounded border border-gray-200 dark:border-gray-700"><span x-text="calcData.rating_persen"></span>% &times; 0.35 = <strong x-text="(calcData.rating_persen * 0.35).toFixed(2)"></strong></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium">3. Rata-rata Skor Kecepatan <span class="text-xs bg-green-100 text-green-800 px-1.5 py-0.5 rounded ml-1 dark:bg-green-900/30 dark:text-green-300">25%</span></span>
                                    <span class="text-gray-800 dark:text-gray-200 font-mono text-xs bg-white dark:bg-gray-800 px-2 py-1 rounded border border-gray-200 dark:border-gray-700"><span x-text="calcData.skor_cepat"></span>% &times; 0.25 = <strong x-text="(calcData.skor_cepat * 0.25).toFixed(2)"></strong></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="rounded-xl border border-brand-200 bg-brand-50 p-4 dark:border-brand-800/50 dark:bg-brand-900/20 shadow-sm relative overflow-hidden">
                            <div class="absolute right-0 top-0 h-full w-2 bg-brand-500"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-brand-900 dark:text-brand-100 font-bold">Total Rata-rata Akhir</span>
                                <span class="text-brand-700 dark:text-brand-300 text-2xl font-black tracking-tight" x-text="calcData.rata_rata + '%'"></span>
                            </div>
                            <div class="mt-2 text-xs font-mono text-brand-600/80 dark:text-brand-400/80">
                                Σ ( <span x-text="(calcData.rr_kirim * 0.40).toFixed(2)"></span> + <span x-text="(calcData.rating_persen * 0.35).toFixed(2)"></span> + <span x-text="(calcData.skor_cepat * 0.25).toFixed(2)"></span> ) = <span x-text="calcData.rata_rata"></span>
                            </div>
                        </div>

                        <div class="mt-6 mb-2">
                            <h4 class="text-base font-bold text-gray-800 dark:text-white">Rincian Per Penugasan</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Berikut adalah rincian data dari masing-masing penugasan yang dikerjakan.</p>
                            
                            <div class="space-y-3">
                                <template x-for="(detail, index) in calcData.details" :key="index">
                                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                        <h5 class="mb-3 text-sm font-bold text-gray-800 dark:text-gray-200 border-b border-gray-100 dark:border-gray-700 pb-2 flex gap-2 items-start">
                                            <span class="bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded px-1.5 py-0.5 text-xs mt-0.5" x-text="'#' + (index + 1)"></span>
                                            <span x-text="detail.nama_sub_kegiatan"></span>
                                        </h5>
                                        
                                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                                            <div class="rounded bg-gray-50 p-2 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800 text-center">
                                                <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-1 font-semibold">RR Kirim</div>
                                                <div class="font-mono text-gray-800 dark:text-gray-200 font-bold" x-text="detail.rr_kirim + '%'"></div>
                                            </div>
                                            <div class="rounded bg-gray-50 p-2 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800 text-center">
                                                <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-1 font-semibold">Rating Kirim</div>
                                                <div class="font-mono text-gray-800 dark:text-gray-200 font-bold" x-text="detail.rating_kirim + ' ⭐'"></div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col gap-1.5 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-800 p-3 text-xs">
                                            <div class="font-bold text-gray-700 dark:text-gray-300 mb-1">Kalkulasi Kecepatan & Volume:</div>
                                            
                                            <!-- Volume calculation -->
                                            <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                                                <span>Volume Dikirim / Target:</span>
                                                <span class="font-mono text-gray-800 dark:text-gray-200" x-text="detail.jumlah_dikirim + ' / ' + detail.target"></span>
                                            </div>
                                            <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                                                <span>Skor Volume (V):</span>
                                                <span class="font-mono text-gray-800 dark:text-gray-200" x-text="Number(detail.scoreVol).toFixed(2)"></span>
                                            </div>

                                            <div class="h-px bg-gray-200 dark:bg-gray-700 my-1"></div>

                                            <!-- Time calculation -->
                                            <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                                                <span>Durasi Penugasan (Tgl Mulai &rarr; Selesai):</span>
                                                <span class="font-mono text-gray-800 dark:text-gray-200">
                                                    <span x-text="detail.tanggal_mulai ? detail.tanggal_mulai.substring(0, 10) : '-'"></span> &rarr; <span x-text="detail.tanggal_selesai ? detail.tanggal_selesai.substring(0, 10) : '-'"></span>
                                                    <strong class="ml-1">(<span x-text="detail.diffSelesaiMulai"></span> hari)</strong>
                                                </span>
                                            </div>
                                            <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                                                <span>Waktu Pengerjaan (Tgl Mulai &rarr; Pengiriman):</span>
                                                <span class="font-mono text-gray-800 dark:text-gray-200">
                                                    <span x-text="detail.tanggal_mulai ? detail.tanggal_mulai.substring(0, 10) : '-'"></span> &rarr; <span x-text="detail.tanggal_pengiriman ? detail.tanggal_pengiriman.substring(0, 10) : '-'"></span>
                                                    <strong class="ml-1">(<span x-text="detail.diffKirimMulai"></span> hari)</strong>
                                                </span>
                                            </div>
                                            <div class="flex justify-between items-center text-gray-600 dark:text-gray-400">
                                                <span>Skor Waktu (W) <span class="text-[10px] bg-gray-200 dark:bg-gray-700 px-1 rounded">1 - (Pengerjaan / Durasi)</span> :</span>
                                                <span class="font-mono text-gray-800 dark:text-gray-200" x-text="'1 - (' + detail.diffKirimMulai + ' / ' + detail.diffSelesaiMulai + ') = ' + Number(detail.scoreTime).toFixed(4)"></span>
                                            </div>

                                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                                                <span class="text-gray-700 dark:text-gray-300 font-bold">Skor Cepat (V &times; W &times; 100%):</span>
                                                <span class="font-mono font-black text-green-600 dark:text-green-400 text-sm" x-text="Number(detail.skor_cepat).toFixed(2) + '%'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="!calcData.details || calcData.details.length === 0" class="text-center text-sm text-gray-500 py-4 italic">
                                    Tidak ada data rincian penugasan.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer -->
                <div class="flex-none flex justify-end gap-3 rounded-b-2xl border-t border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <button @click="open = false" type="button" class="inline-flex justify-center items-center rounded-xl px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-all dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-900">
                        Tutup
                    </button>
                </div>
                
            </div>
        </div>
    </div>
</div>
