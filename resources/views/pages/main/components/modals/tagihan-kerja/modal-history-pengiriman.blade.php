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
        class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden rounded-3xl bg-white">

        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-4">
            <h4 class="text-xl font-semibold text-gray-800" x-text="formData.nama_anggota">
            </h4>
            <p class="mt-1 text-sm text-gray-500">
                ID Penugasan:
                <span class="font-medium" x-text="formData.id_penugasan"></span>
            </p>
        </div>

        <!-- BODY -->
        <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar space-y-4">

            <!-- Jika belum ada pengiriman -->
            <template x-if="formData.historiData.length === 0">
                <div
                    class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm
                                        text-gray-500">
                    Belum ada histori pengiriman
                </div>
            </template>

            <!-- Card Histori Pengiriman -->
            <template x-for="(item, index) in formData.historiData" :key="index">
                <div
                    class="rounded-2xl border border-gray-200 p-5 shadow-sm">

                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                Pengiriman ke-<span x-text="index + 1"></span>
                            </p>
                            <p class="mt-1 text-xs text-gray-500" x-text="item.tanggal_pengiriman">
                            </p>
                        </div>

                        <!-- Status -->
                        <span class="rounded-full px-3 py-1 text-xs font-medium"
                            :class="item.status === 'Diterima' ?
                                'bg-green-100 text-green-700' :
                                'bg-yellow-100 text-yellow-700'"
                            x-text="item.status">
                        </span>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Jumlah Dikirim</p>
                            <p class="font-medium text-gray-800" x-text="item.jumlah_dikirim">
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">Media Pengiriman</p>
                            <p class="font-medium text-gray-800" x-text="item.media_pengiriman">
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-sm">Bukti Dukung</p>
                            <a :href="item.bukti_dukung" target="_blank"
                                class="text-sm font-medium text-blue-600 hover:underline">
                                Lihat Bukti Dukung
                            </a>
                        </div>

                        <div>
                            <p class="text-gray-500">Catatan Ketua</p>
                            <p
                                class="font-medium"
                                :class="item.catatan && item.catatan.trim() !== ''
                                    ? 'text-gray-800'
                                    : 'text-gray-400 italic'"
                                x-text="item.catatan && item.catatan.trim() !== ''
                                    ? item.catatan
                                    : 'Belum ada catatan'">
                            </p>
                        </div>

                    </div>
                </div>
            </template>
        </div>

        <!-- FOOTER -->
        <div class="shrink-0 border-t border-gray-200 px-6 py-3">
            <div class="flex justify-end">
                <button type="button" @click="open = false"
                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</x-ui.smart-modal>
