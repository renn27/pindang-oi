<!-- Modal CKP Universal (untuk CKP Pegawai dan CKP Ketua Tim) -->
<div x-data="ckpUniversalModal()"
    @open-ckp-modal.window="
        if ($event.detail.modalId !== 'modal-ckp') return;
        
        ckpData = {
            id_penugasan: $event.detail.id_penugasan || null,
            id_sub_kegiatan: $event.detail.id_sub_kegiatan || null,
            nama_anggota: $event.detail.nama_anggota || '',
            nama_sub_kegiatan: $event.detail.nama_sub_kegiatan || '',
            uraian: $event.detail.uraian || '',
            satuan: $event.detail.satuan || '',
            target_kuantitas: $event.detail.target_kuantitas || 0,
            keterangan: $event.detail.keterangan || '',
            is_ketua_tim: $event.detail.is_ketua_tim || false
        };
        
        showCkpModal = true;
    ">

    <div x-show="showCkpModal"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-[100000] overflow-y-auto"
        style="display: none;">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/50 dark:bg-gray-950/80 transition-opacity"
            @click="showCkpModal = false"></div>

        <!-- Modal Content -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl"
                @click.away="showCkpModal = false">

                <!-- HEADER -->
                <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full"
                            :class="ckpData.is_ketua_tim ? 'bg-green-100 dark:bg-green-900/30' : 'bg-blue-100 dark:bg-blue-900/30'">
                            <svg class="w-5 h-5" 
                                :class="ckpData.is_ketua_tim ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 dark:text-white">
                                Konfirmasi Jadikan CKP
                            </h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                <span x-show="!ckpData.is_ketua_tim">Konfirmasi dan lengkapi data CKP dari penugasan ini</span>
                                <span x-show="ckpData.is_ketua_tim">Konfirmasi dan lengkapi data CKP Ketua Tim dari sub kegiatan ini</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- BODY -->
                <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900 max-h-[70vh]">
                    <form :action="ckpData.is_ketua_tim 
                        ? `{{ url('ckp/from-sub-kegiatan') }}/${ckpData.id_sub_kegiatan}`
                        : `{{ url('ckp/from-penugasan') }}/${ckpData.id_penugasan}`"
                        method="POST"
                        id="ckpUniversalForm">
                        @csrf

                        <!-- Informasi Card -->
                        <div class="mb-6 rounded-xl border border-gray-200 bg-gray-50/50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    <span x-show="!ckpData.is_ketua_tim">Informasi Penugasan</span>
                                    <span x-show="ckpData.is_ketua_tim">Informasi Sub Kegiatan</span>
                                </h4>
                            </div>

                            <div class="grid gap-3 text-sm">
                                <template x-if="!ckpData.is_ketua_tim">
                                    <div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Anggota:</span>
                                            <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="ckpData.nama_anggota"></span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="text-gray-500 dark:text-gray-400">Satuan:</span>
                                            <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="ckpData.satuan"></span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="text-gray-500 dark:text-gray-400">Target Kuantitas:</span>
                                            <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="ckpData.target_kuantitas"></span>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="ckpData.is_ketua_tim">
                                    <div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Sub Kegiatan:</span>
                                            <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="ckpData.nama_sub_kegiatan"></span>
                                        </div>
                                        <div class="mt-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-3">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-sm text-green-700 dark:text-green-300">
                                                    Semua penugasan (100%) telah selesai.
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Form CKP -->
                        <div class="space-y-5">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Data CKP
                                </h4>
                                <span class="text-xs text-gray-400">(bisa diedit)</span>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Uraian Kegiatan <span class="text-red-500">*</span>
                                </label>
                                <textarea name="uraian"
                                    x-model="ckpData.uraian"
                                    rows="3"
                                    required
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800
                                            focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors
                                            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"></textarea>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Keterangan
                                </label>
                                <textarea name="keterangan"
                                    x-model="ckpData.keterangan"
                                    rows="2"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800
                                            focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors
                                            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                    placeholder="Isi keterangan jika diperlukan..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- FOOTER -->
                <div class="shrink-0 border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button type="button"
                                @click="showCkpModal = false"
                                class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto
                                        dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            Batal
                        </button>
                        <button type="button"
                                @click="submitCkpForm()"
                                class="flex w-full justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700 sm:w-auto
                                        dark:bg-green-600 dark:hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-show="!ckpData.is_ketua_tim">Simpan ke CKP</span>
                            <span x-show="ckpData.is_ketua_tim">Simpan ke CKP Ketua Tim</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function ckpUniversalModal() {
        return {
            showCkpModal: false,
            ckpData: {
                id_penugasan: null,
                id_sub_kegiatan: null,
                nama_anggota: '',
                nama_sub_kegiatan: '',
                uraian: '',
                satuan: '',
                target_kuantitas: 0,
                keterangan: '',
                is_ketua_tim: false
            },

            submitCkpForm() {
                const form = document.getElementById('ckpUniversalForm');
                if (form) {
                    this.showCkpModal = false;
                    form.submit();
                }
            }
        }
    }
</script>