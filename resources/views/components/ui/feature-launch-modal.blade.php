@props([
    'id' => 'featureLaunchModal',
])

<div x-data="{ open: false }"
    x-show="open"
    @open-feature-modal.window="open = true"
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

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
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
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl dark:bg-gray-900 border border-brand-100 dark:border-brand-900/30">
                
                <!-- Decorative Top Border -->
                <div class="h-2 w-full bg-gradient-to-r from-brand-400 via-brand-500 to-brand-600"></div>

                <!-- Close Button -->
                <button @click="open = false" class="absolute right-4 top-6 text-gray-400 hover:text-gray-500 focus:outline-none dark:hover:text-gray-300 transition-colors">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Modal Content -->
                <div class="px-6 py-8 sm:px-10">
                    
                    <!-- Header -->
                    <div class="flex items-center gap-4 mb-8">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 shadow-sm dark:bg-brand-900/30 dark:text-brand-400 shrink-0">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-brand-600 tracking-wide uppercase dark:text-brand-400 mb-1">Spotlight Update</h2>
                            <h3 class="text-2xl font-bold leading-6 text-gray-900 dark:text-white" id="modal-title">
                                Fitur CKP & Pengiriman Bulanan
                            </h3>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <p class="text-base text-gray-600 dark:text-gray-300 leading-relaxed">
                            Halo! Kami telah melakukan pembaruan besar pada sistem untuk mempermudah Anda dalam melaporkan kinerja secara bulanan. Fitur yang sebelumnya ditangguhkan kini <strong class="text-gray-800 dark:text-gray-100 font-semibold">sudah bisa diakses kembali</strong> dengan mekanisme yang jauh lebih baik. Berikut adalah ringkasan perubahannya:
                        </p>
                    </div>

                    <!-- Features Grid -->
                    <div class="flex flex-col gap-6 mb-8">
                        
                        <!-- Feature 1 -->
                        <div class="relative p-5 rounded-2xl bg-gray-50 border border-gray-100 hover:border-brand-200 transition-colors dark:bg-gray-800/50 dark:border-gray-700/50 dark:hover:border-brand-700/50 group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400 group-hover:scale-110 transition-transform">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white">Pengiriman Bulanan</h4>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed space-y-3">
                                <p>Kini pengiriman tugas bisa dilakukan per bulan dengan dua tipe: <strong class="text-gray-800 dark:text-gray-200">Cicilan</strong> jika pekerjaan berlanjut, atau <strong class="text-gray-800 dark:text-gray-200">Pelunasan</strong>.</p>
                                
                                <ul class="list-disc pl-5 space-y-2">
                                    <li><strong class="text-brand-600 dark:text-brand-400">Lintas Bulan : </strong> (Misal penugasan Januari-Maret). Jika Anda mengirim di Januari dengan tipe Cicilan, maka bulan Januari tidak bisa dipilih lagi pada pengiriman berikutnya. Anda harus memilih bulan lain untuk pengiriman selanjutnya atau pelunasannya. Jadi, pastikan dulu saat mengirim apakah ini sudah pasti Cicilan atau Pelunasan.</li>
                                    <li><strong class="text-brand-600 dark:text-brand-400">Akumulatif : </strong> Jumlah realisasi bersifat akumulatif. Contoh: Misal penugasan Januari-Maret dengan target 10. Jika di bulan Januari Anda mencicil 3 target, maka saat pelunasan di bulan Maret Anda harus menuliskan target keseluruhan yang tercapai, yaitu 10 (bukan sisa 7).</li>
                                    <li><strong class="text-brand-600 dark:text-brand-400">Pemilihan Bulan : </strong> Anda tidak diizinkan memilih bulan pengiriman yang belum tiba (di masa depan), namun tetap diizinkan memilih bulan yang sudah lewat (backdate).</li>
                                    <li><strong class="text-brand-600 dark:text-brand-400">Catatan Pengiriman : </strong> Kini tersedia kolom catatan saat pengiriman untuk mempermudah komunikasi antara anggota dan ketua tim.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div class="relative p-5 rounded-2xl bg-gray-50 border border-gray-100 hover:border-brand-200 transition-colors dark:bg-gray-800/50 dark:border-gray-700/50 dark:hover:border-brand-700/50 group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-400 group-hover:scale-110 transition-transform">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white">CKP Anggota Tim</h4>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed space-y-2">
                                <p>Pembuatan CKP menjadi sangat praktis! Anda hanya perlu memilih bulan CKP yang sesuai dengan riwayat pengiriman bulanan yang sudah otomatis dideteksi oleh sistem.</p>
                                <p class="italic text-gray-500">* Uraian pada CKP bisa diedit secara manual jika tidak ingin menggunakan uraian default template. Semua CKP yang dibuat akan masuk ke halaman <strong class="text-gray-700 dark:text-gray-300">CKP Saya</strong> yang bisa diekspor menjadi format tabel CKP per bulan.</p>
                            </div>
                        </div>

                        <!-- Feature 3 -->
                        <div class="relative p-5 rounded-2xl bg-gray-50 border border-gray-100 hover:border-brand-200 transition-colors dark:bg-gray-800/50 dark:border-gray-700/50 dark:hover:border-brand-700/50 group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400 group-hover:scale-110 transition-transform">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white">CKP Ketua Tim</h4>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed space-y-2">
                                <p>Perhitungan progres CKP untuk Ketua Tim kini otomatis disesuaikan berdasarkan akumulasi dari Total Target Penugasan dan Penugasan yang Targetnya Selesai.</p>
                                <p class="italic text-gray-500">* Uraian pada CKP bisa diedit secara manual jika tidak ingin menggunakan uraian default template. Semua CKP yang dibuat akan masuk ke halaman <strong class="text-gray-700 dark:text-gray-300">CKP Saya</strong> yang bisa diekspor menjadi format tabel CKP per bulan.</p>
                            </div>
                        </div>

                        <!-- Feature 4 -->
                        <div class="relative p-5 rounded-2xl bg-gray-50 border border-gray-100 hover:border-brand-200 transition-colors dark:bg-gray-800/50 dark:border-gray-700/50 dark:hover:border-brand-700/50 group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/40 dark:text-purple-400 group-hover:scale-110 transition-transform">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white">CKP Pimpinan</h4>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed space-y-2">
                                <p>Untuk Role Pimpinan, perhitungan CKP sudah disesuaikan dan akan otomatis menghitung berdasarkan Target dan Realisasi yang tercatat di Agenda Pimpinan.</p>
                                <p class="italic text-gray-500">* Uraian pada CKP bisa diedit secara manual jika tidak ingin menggunakan uraian default template. Semua CKP yang dibuat akan masuk ke halaman <strong class="text-gray-700 dark:text-gray-300">CKP Saya</strong> yang bisa diekspor menjadi format tabel CKP per bulan.</p>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Call to Action -->
                    <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <button @click="open = false" type="button" class="inline-flex justify-center items-center rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-sm bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-all hover:shadow-md dark:focus:ring-offset-gray-900">
                            Siap Eksplorasi Fitur
                            <svg class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
