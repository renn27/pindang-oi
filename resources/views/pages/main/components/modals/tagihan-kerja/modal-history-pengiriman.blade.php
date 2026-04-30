<!-- Modal Histori Pengiriman Anggota -->
<x-ui.smart-modal id="modal-histori-pengiriman" class="max-w-2xl"
    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-histori-pengiriman') return;

        formData = $event.detail.data ?? {
            nama_anggota: '',
            id_penugasan: '',
            id_anggota: '',
            historiData: []
        };">
    <div
        class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden rounded-2xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800 shadow-lg">

        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-100 dark:border-gray-800 px-6 py-5 bg-white dark:bg-gray-900">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white" x-text="formData.nama_anggota"></h4>
            <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">
                ID Penugasan: <span class="font-medium text-gray-600 dark:text-gray-400" x-text="formData.id_penugasan"></span>
            </p>
        </div>

        <!-- BODY -->
        <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar space-y-4 bg-gray-50/50 dark:bg-gray-900"
            x-data="{
                get groupedHistori() {
                    if (!formData.historiData) return {};
                    const groups = {};
                    const bulanNama = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    
                    formData.historiData.forEach(item => {
                        let monthLabel = 'Tanpa Bulan';
                        if (item.bulan_pengiriman) {
                            const parts = item.bulan_pengiriman.split('-');
                            if (parts.length === 2) {
                                monthLabel = bulanNama[parseInt(parts[1], 10) - 1] + ' ' + parts[0];
                            } else {
                                monthLabel = item.bulan_pengiriman;
                            }
                        }
                        
                        if (!groups[monthLabel]) groups[monthLabel] = [];
                        groups[monthLabel].push(item);
                    });
                    
                    for (const month in groups) {
                        groups[month].sort((a, b) => {
                            const aDiterima = a.penerimaan?.status === 'Diterima' ? -1 : 1;
                            const bDiterima = b.penerimaan?.status === 'Diterima' ? -1 : 1;
                            if (aDiterima !== bDiterima) return aDiterima - bDiterima;
                            return 0; 
                        });
                    }
                    return groups;
                }
            }">

            <!-- Empty State -->
            <template x-if="formData.historiData.length === 0">
                <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada histori pengiriman</p>
                </div>
            </template>

            <!-- Group Histori Pengiriman Berdasarkan Bulan -->
            <template x-for="([month, items], index) in Object.entries(groupedHistori)" :key="month">
                <div class="mb-8 last:mb-0" x-data="{ openGroup: index === 0 }">
                    <div class="flex items-center gap-3 mb-4 mt-2 px-1 cursor-pointer select-none group" @click="openGroup = !openGroup">
                        <div class="px-4 py-1.5 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 whitespace-nowrap transition-colors group-hover:border-brand-300 dark:group-hover:border-brand-500/50 flex items-center">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Periode</span>
                            <span class="ml-1 text-sm font-bold text-brand-600 dark:text-brand-400" x-text="month"></span>
                            <svg class="w-3.5 h-3.5 ml-1.5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': openGroup }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="flex-1 h-px bg-gray-300 dark:bg-gray-700 rounded-full transition-colors group-hover:bg-brand-200 dark:group-hover:bg-brand-500/30"></div>
                    </div>
                    <div x-show="openGroup" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="space-y-4">
                        <template x-for="(item, index) in items" :key="item.id_pengiriman">
                            <div x-data="{ openDetail: false }" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">

                                <!-- Timeline Indicator -->
                                <div class="relative">
                                    <div class="flex items-center justify-between px-5 pt-4 pb-3 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800/50 dark:to-gray-800 border-b border-gray-100 dark:border-gray-700/50">
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <p class="text-xs text-gray-400 dark:text-gray-500">Tanggal Pengiriman</p>
                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="item.tanggal_pengiriman"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <!-- Badge Tipe -->
                                            <template x-if="item.tipe_pengiriman">
                                                <span class="rounded-full px-2.5 py-0.5 text-[10px] font-semibold shadow-sm"
                                                    :class="{
                                                        'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300': item.tipe_pengiriman === 'Cicilan',
                                                        'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300': item.tipe_pengiriman === 'Pelunasan'
                                                    }" x-text="item.tipe_pengiriman"></span>
                                            </template>
                                            <!-- Badge Status -->
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold shadow-sm"
                                                :class="{
                                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300': item.penerimaan.status === 'Diterima',
                                                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300': item.penerimaan.status === 'Revisi',
                                                    'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300': item.penerimaan.status === 'Menunggu'
                                                }"
                                                x-text="item.penerimaan.status">
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Pengiriman - Grid 2 Kolom -->
                                <div class="px-5 py-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Kolom Kiri -->
                                        <div class="space-y-3">
                                            <div class="flex items-start gap-2">
                                                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                <div>
                                                    <p class="text-xs text-gray-400 dark:text-gray-500">Jumlah Dikirim</p>
                                                    <p class="text-sm font-semibold text-gray-800 dark:text-white" x-text="item.jumlah_dikirim"></p>
                                                </div>
                                            </div>
                                            <div class="flex items-start gap-2">
                                                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                </svg>
                                                <div>
                                                    <p class="text-xs text-gray-400 dark:text-gray-500">Media Pengiriman</p>
                                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="item.media_pengiriman"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Kolom Kanan -->
                                        <div class="space-y-3">
                                            <div class="flex items-start gap-2">
                                                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <div class="flex-1">
                                                    <p class="text-xs text-gray-400 dark:text-gray-500">Bukti Dukung</p>
                                                    <a :href="item.bukti_dukung" target="_blank"
                                                        class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors">
                                                        Lihat Bukti
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3 border border-gray-100 dark:border-gray-700">
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mb-1 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    Catatan Anggota
                                                </p>
                                                <div class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed italic">
                                                    <span x-text="item.catatan_pengiriman ? item.catatan_pengiriman : '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- RR dan Rating Pengiriman -->
                                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                        <div>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">Response Rate (Kirim)</p>
                                            <p class="text-sm font-semibold text-blue-600 dark:text-blue-400" x-text="(item.rr_kirim || 0) + '%'"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-400 dark:text-gray-500">Ketepatan Waktu</p>
                                            <div class="flex items-center justify-end gap-0.5 mt-0.5">
                                                <template x-for="(star, idx) in item.bintang_kirim_array" :key="'kirim-'+idx">
                                                    <span :class="star ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'" class="text-sm">★</span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tombol Batalkan Pengiriman (hanya untuk Anggota Tim) -->
                                    @if(in_array(auth()->user()->active_role, ['Anggota Tim']))
                                        <template x-if="item.penerimaan.status === 'Menunggu' && formData.id_anggota === '{{ auth()->user()->id_pegawai }}'">
                                            <div class="mt-4 pt-4 border-t border-red-50 dark:border-red-900/20 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                <form :id="'delete-pengiriman-' + item.id_pengiriman" :action="`/pengirimans/${item.id_pengiriman}`" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" @click="SwalHelper.confirmDelete('delete-pengiriman-' + item.id_pengiriman, 'Pengiriman ' + item.tanggal_pengiriman)"
                                                        class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 font-medium transition-colors">
                                                        Batalkan Pengiriman
                                                    </button>
                                                </form>
                                            </div>
                                        </template>
                                    @endif
                                </div>

                                <!-- Footer Card: Informasi Penerimaan & Tombol Expand -->
                                <div class="px-5 py-3 bg-gray-50/50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">Diperiksa oleh :</span>
                                                <span class="text-xs font-medium"
                                                    :class="item.penerimaan?.id_penerima && item.penerimaan?.id_penerima !== 'Belum Diperiksa'
                                                        ? 'text-green-600 dark:text-green-400'
                                                        : 'text-gray-400 italic'"
                                                    x-text="item.penerimaan?.id_penerima && item.penerimaan?.id_penerima !== 'Belum Diperiksa' ? item.penerimaan.id_penerima : 'Belum Diperiksa'">
                                                </span>
                                            </div>
                                        </div>
                                        <button @click="openDetail = !openDetail"
                                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 hover:bg-gray-200 dark:hover:bg-gray-700"
                                            :class="openDetail ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">
                                            <span x-text="openDetail ? 'Sembunyikan' : 'Lihat Penerimaan'"></span>
                                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openDetail }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Detail Penerimaan (Expandable dengan Animasi) -->
                                <div x-show="openDetail" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="border-t border-gray-100 dark:border-gray-700 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800/50 dark:to-gray-800 px-5 py-4">
                                    <template x-if="item.penerimaan">
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-100 dark:border-gray-700">
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Tanggal Terima</p>
                                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300" x-text="item.penerimaan.tanggal_penerimaan"></p>
                                                </div>
                                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-100 dark:border-gray-700">
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Jumlah Diterima</p>
                                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300" x-text="item.penerimaan.jumlah_diterima"></p>
                                                </div>
                                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-100 dark:border-gray-700">
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Status</p>
                                                    <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold"
                                                        :class="{
                                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300': item.penerimaan.status === 'Diterima',
                                                            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300': item.penerimaan.status === 'Revisi'
                                                        }"
                                                        x-text="item.penerimaan.status">
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3 border border-gray-100 dark:border-gray-700">
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-1 flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Catatan Ketua Tim
                                                    </p>
                                                    <div class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed italic">
                                                        <span x-text="item.penerimaan?.catatan ? item.penerimaan.catatan : '-'"></span>
                                                    </div>
                                                </div>

                                                <div class="flex flex-col justify-center gap-2 bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-100 dark:border-gray-700">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">Response Rate</span>
                                                        <span class="text-sm font-semibold text-green-600 dark:text-green-400" x-text="(item.penerimaan.rr_terima || 0) + '%'"></span>
                                                    </div>
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">Ketepatan Waktu</span>
                                                        <div class="flex items-center gap-0.5">
                                                            <template x-for="(star, idx) in item.penerimaan.bintang_terima_array" :key="'terima-'+idx">
                                                                <span :class="star ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'" class="text-sm">★</span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Tombol Batalkan Penerimaan (hanya untuk Ketua Tim) --}}
                                            @if(in_array(auth()->user()->active_role, ['Ketua Tim']))
                                                <template x-if="item.penerimaan.status !== 'Menunggu' && item.penerimaan.id_penerimaan && item.is_last">
                                                    <div class="mt-2 flex items-center gap-2">
                                                        <!-- Jika sudah masuk CKP, tampilkan button disabled dengan tooltip -->
                                                        <template x-if="formData.bulan_sudah_ckp && formData.bulan_sudah_ckp.includes(item.bulan_pengiriman)">
                                                            <div class="relative group flex items-center gap-2">
                                                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                                <button type="button" disabled
                                                                    class="text-sm text-gray-400 dark:text-gray-500 font-medium cursor-not-allowed">
                                                                    Batalkan Penerimaan
                                                                </button>
                                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-40 p-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs rounded-lg shadow-xl ring-1 ring-black/5 dark:ring-white/10 z-10 text-center leading-relaxed whitespace-normal">
                                                                    Sudah masuk CKP Anggota Tim.<br>Gak bisa dibatalkan.
                                                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-white dark:border-t-gray-800"></div>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <!-- Jika belum masuk CKP, tampilkan button aktif -->
                                                        <template x-if="!formData.bulan_sudah_ckp || !formData.bulan_sudah_ckp.includes(item.bulan_pengiriman)">
                                                            <div class="flex items-center gap-2">
                                                                <svg class="w-4 h-4 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                                <form :id="'delete-penerimaan-' + item.penerimaan.id_penerimaan"
                                                                      :action="`/penerimaans/${item.penerimaan.id_penerimaan}`"
                                                                      method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" @click="SwalHelper.confirmDelete('delete-penerimaan-' + item.penerimaan.id_penerimaan, 'Penerimaan ' + item.tanggal_pengiriman)"
                                                                        class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 font-medium transition-colors">
                                                                        Batalkan Penerimaan
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            @endif
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- FOOTER -->
        <div class="shrink-0 border-t border-gray-100 dark:border-gray-800 px-6 py-4 bg-white dark:bg-gray-900">
            <div class="flex justify-end gap-3">
                <button type="button" @click="open = false"
                    class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</x-ui.smart-modal>


