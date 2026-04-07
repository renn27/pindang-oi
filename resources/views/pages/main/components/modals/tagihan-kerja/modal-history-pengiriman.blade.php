<!-- Modal Histori Pengiriman Anggota -->
<x-ui.smart-modal id="modal-histori-pengiriman" class="max-w-2xl"
    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-histori-pengiriman') return;

        formData = $event.detail.data ?? {
            nama_anggota: '',
            id_penugasan: '',
            historiData: []
        };">
    <div
        class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">

        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h4 class="text-xl font-semibold text-gray-800 dark:text-white" x-text="formData.nama_anggota">
            </h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                ID Penugasan:
                <span class="font-medium dark:text-gray-300" x-text="formData.id_penugasan"></span>
            </p>
        </div>

        <!-- BODY -->
        <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar space-y-4 dark:bg-gray-900">

            <!-- Jika belum ada pengiriman -->
            <template x-if="formData.historiData.length === 0">
                <div
                    class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm
                                        text-gray-500 dark:border-gray-700 dark:text-gray-400">
                    Belum ada histori pengiriman
                </div>
            </template>

            <!-- Card Histori Pengiriman -->
            <template x-for="(item, index) in formData.historiData" :key="index">
                <div
                    class="rounded-2xl border border-gray-200 p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white">
                                Pengiriman ke-<span x-text="Number(index) + 1"></span>
                            </p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-text="item.tanggal_pengiriman">
                            </p>
                        </div>

                        <!-- Status -->
                        <span
                            class="rounded-full px-3 py-1 text-xs font-medium"
                            :class="
                                item.status === 'Diterima'
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                    : item.status === 'Revisi'
                                        ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-300'
                                        : 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-300'
                            "
                            x-text="item.status"
                        ></span>

                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Jumlah Dikirim</p>
                            <p class="font-medium text-gray-800 dark:text-white" x-text="item.jumlah_dikirim">
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Media Pengiriman</p>
                            <p class="font-medium text-gray-800 dark:text-white" x-text="item.media_pengiriman">
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-sm dark:text-gray-400">Bukti Dukung</p>
                            <a :href="item.bukti_dukung" target="_blank"
                                class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400 dark:hover:text-blue-300">
                                Lihat Bukti Dukung
                            </a>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Catatan Ketua</p>
                            <p
                                class="font-medium"
                                :class="item.catatan && item.catatan.trim() !== ''
                                    ? 'text-gray-800 dark:text-gray-300'
                                    : 'text-gray-400 italic dark:text-gray-500'"
                                x-text="item.catatan && item.catatan.trim() !== ''
                                    ? item.catatan
                                    : 'Belum ada catatan'">
                            </p>
                        </div>

                    </div>

                    <template x-if="item.status === 'Menunggu Diperiksa' && formData.id_anggota === '{{ auth()->user()->id_pegawai }}'">
                        <div class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-4 flex justify-end">
                            <form :id="'delete-pengiriman-' + item.id_pengiriman" :action="`/pengirimans/${item.id_pengiriman}`" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="SwalHelper.confirmDelete('delete-pengiriman-' + item.id_pengiriman, 'Pengiriman ' + item.tanggal_pengiriman)" class="text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Batalkan Pengiriman
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- FOOTER -->
        <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-700">
            <div class="flex justify-end">
                <button type="button" @click="open = false"
                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</x-ui.smart-modal>
