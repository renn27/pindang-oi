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
        <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar space-y-4 bg-gray-50/50 dark:bg-gray-900">

            <!-- Empty State -->
            <template x-if="formData.historiData.length === 0">
                <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada histori pengiriman</p>
                </div>
            </template>

            <!-- Card Histori Pengiriman -->
            <template x-for="(item, index) in formData.historiData" :key="index">
                <div x-data="{ openDetail: false }" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">

                    <!-- Timeline Indicator -->
                    <div class="relative">
                        <div class="flex items-center justify-between px-5 pt-4 pb-3 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800/50 dark:to-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 text-xs font-semibold text-gray-600 dark:text-gray-300">
                                    <span x-text="formData.historiData.length - index"></span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Tanggal Pengiriman</p>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="item.tanggal_pengiriman"></p>
                                </div>
                            </div>
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
                                <!-- Tombol Batalkan -->
                                <template x-if="item.penerimaan.status === 'Menunggu' && formData.id_anggota === '{{ auth()->user()->id_pegawai }}'">
                                    <div class="flex items-center gap-2">
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
                            </div>
                        </div>
                    </div>

                    <!-- Separator dengan border halus -->
                    <div class="border-t border-gray-100 dark:border-gray-700 mx-5"></div>

                    <!-- Footer Card: Informasi Penerimaan & Tombol Expand -->
                    <div class="px-5 py-3 bg-gray-50/30 dark:bg-gray-800/30">
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
                                <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>
                            </div>
                            <button @click="openDetail = !openDetail"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-200 dark:hover:bg-gray-700"
                                :class="openDetail ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">
                                <span x-text="openDetail ? 'Sembunyikan Detail' : 'Lihat Detail Penerimaan'"></span>
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
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">📅 Tanggal Penerimaan</p>
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300" x-text="item.penerimaan.tanggal_penerimaan"></p>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">📊 Jumlah Diterima</p>
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300" x-text="item.penerimaan.jumlah_diterima"></p>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">🏷️ Status</p>
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold"
                                            :class="{
                                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300': item.penerimaan.status === 'Diterima',
                                                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300': item.penerimaan.status === 'Revisi'
                                            }"
                                            x-text="item.penerimaan.status">
                                        </span>
                                    </div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Catatan Ketua Tim
                                    </p>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                        <span x-text="item.penerimaan?.catatan ? item.penerimaan.catatan : '-'"></span>
                                    </div>
                                </div>

                                {{-- Tombol Batalkan Penerimaan (hanya untuk Ketua Tim / Admin / Pimpinan) --}}
                                @if(in_array(auth()->user()->active_role, ['Ketua Tim']))
                                    <template x-if="item.penerimaan.status !== 'Menunggu' && item.penerimaan.id_penerimaan && item.is_last">
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
                                @endif
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


