{{-- Modal Konfirmasi Master Kegiatan --}}
<x-ui.modal
    x-data="{ open: false }"
    @open-confirmation-modal.window="open = true"
    @close-confirmation-modal.window="open = false"
    id="modal-konfirmasi-master-kegiatan"
    :isOpen="false"
    class="max-w-[800px]">
    <div class="relative flex h-[80vh] w-full max-w-[800px] flex-col overflow-hidden
               rounded-3xl bg-white">

        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xl font-semibold text-gray-800">
                        Konfirmasi Data
                    </h4>
                    <p class="mt-1 text-sm text-gray-500">
                        Review data sebelum disimpan
                    </p>
                </div>
                <button @click="open = false"
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- BODY -->
        <div class="flex-1 overflow-y-auto px-6 py-5">
            <div id="confirmationContent" class="space-y-6">
                <!-- Content akan diisi oleh JavaScript -->
            </div>
        </div>

        <!-- FOOTER -->
        <div class="shrink-0 border-t border-gray-200 px-6 py-3">
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button @click="open = false" type="button"
                    class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto">
                    Batal
                </button>

                <button onclick="confirmSave()" type="button"
                    class="flex w-full justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700 sm:w-auto">
                    Ya, Simpan Data
                </button>
            </div>
        </div>
    </div>
</x-ui.modal>