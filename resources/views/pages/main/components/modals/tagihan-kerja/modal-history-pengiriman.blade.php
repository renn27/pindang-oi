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
                <div x-data="{ openDetail: false }" class="rounded-xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-shadow duration-200">

                    <!-- Header Card dengan Status -->
                    <div class="flex items-center justify-between px-5 pt-4 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">Pengiriman ke-<span x-text="Number(index) + 1"></span></span>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-medium"
                            :class="{
                                'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400': item.status === 'Diterima',
                                'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400': item.status === 'Revisi',
                                'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400': item.status === 'Menunggu Diperiksa'
                            }"
                            x-text="item.status">
                        </span>
                    </div>

                    <!-- Grid Data Pengiriman (mirip style pertama) -->
                    <div class="px-5 py-3">
                        <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Tanggal Dikirim</p>
                                <p class="font-medium text-gray-700 dark:text-gray-300" x-text="item.tanggal_pengiriman"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Jumlah Dikirim</p>
                                <p class="font-medium text-gray-700 dark:text-gray-300" x-text="item.jumlah_dikirim"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Media Pengiriman</p>
                                <p class="font-medium text-gray-700 dark:text-gray-300" x-text="item.media_pengiriman"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Bukti Dukung</p>
                                <a :href="item.bukti_dukung" target="_blank"
                                    class="inline-flex items-center gap-1.5 text-sm text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat Bukti Dukung
                                </a>
                            </div>
                        </div>
                    </div>


                    <!-- Tombol Batalkan (khusus Menunggu Diperiksa) -->
                    <template x-if="item.status === 'Menunggu Diperiksa' && formData.id_anggota === '{{ auth()->user()->id_pegawai }}'">
                        <div class="px-5 pb-2">
                            <form :id="'delete-pengiriman-' + item.id_pengiriman" :action="`/pengirimans/${item.id_pengiriman}`" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="SwalHelper.confirmDelete('delete-pengiriman-' + item.id_pengiriman, 'Pengiriman ' + item.tanggal_pengiriman)"
                                    class="inline-flex items-center gap-1.5 text-sm text-rose-500 hover:text-rose-600 dark:text-rose-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Batalkan Pengiriman
                                </button>
                            </form>
                        </div>
                    </template>

                    <!-- Tombol Lihat Penerimaan -->
                    <div class="flex flex-row justify-between items-center px-5 pb-3 pt-1 border-t border-gray-50 dark:border-gray-800 mt-2">
                        <button @click="openDetail = !openDetail"
                            class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span x-text="openDetail ? 'Sembunyikan Penerimaan' : 'Lihat Penerimaan'"></span>
                        </button>
                        <div>
                            <p class="text-xs text-gray-700 dark:text-gray-700">Telah Diperiksa oleh : </p>
                            <p class="text-xs font-medium"
                                :class="item.penerimaan?.id_penerima === 'Belum Diperiksa'
                                    ? 'text-gray-400 italic'
                                    : 'text-green-700 dark:text-green-300'"
                                x-text="item.penerimaan?.id_penerima ?? 'Belum Diperiksa'">
                            </p>
                        </div>
                    </div>

                    <!-- Detail Penerimaan (Expandable) -->
                    <div x-show="openDetail" x-cloak class="border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 px-5 py-4">
                        <template x-if="!item.penerimaan">
                            <div class="text-center py-3">
                                <p class="text-sm text-gray-400 dark:text-gray-500 italic">
                                    Belum ada penerimaan dari Ketua Tim
                                </p>
                            </div>
                        </template>

                        <template x-if="item.penerimaan" class="">
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">Tanggal Penerimaan</p>
                                        <p class="font-medium text-gray-700 dark:text-gray-300" x-text="item.penerimaan.tanggal_penerimaan"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">Jumlah Diterima</p>
                                        <p class="font-medium text-gray-700 dark:text-gray-300" x-text="item.penerimaan.jumlah_diterima"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Status</p>
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400': item.penerimaan.status === 'Diterima',
                                                'bg-rose-100 text-red-600 dark:bg-rose-900/20 dark:text-red-400': item.penerimaan.status === 'Revisi'
                                            }"
                                            x-text="item.penerimaan.status">
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Catatan Ketua</p>
                                        <span class="inline-block p-1 rounded-full text-xs font-medium"
                                            :class="item.penerimaan.catatan && item.penerimaan.catatan.trim() !== ''
                                                ? 'text-gray-600 dark:text-gray-300'
                                                : 'text-gray-400 italic'">
                                            <span x-text="item.penerimaan.catatan && item.penerimaan.catatan.trim() !== '' ? item.penerimaan.catatan : 'Belum ada catatan'"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- FOOTER -->
        <div class="shrink-0 border-t border-gray-100 dark:border-gray-800 px-6 py-3 bg-white dark:bg-gray-900">
            <div class="flex justify-end">
                <button type="button" @click="open = false"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</x-ui.smart-modal>

