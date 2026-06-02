<x-ui.smart-modal id="modal-transfer-kegiatan" class="max-w-2xl"
    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-transfer-kegiatan') return;

        let baseData = $event.detail.data || {};
        formData = {
            id_kegiatan: '',
            nama_rk_kegiatan: '',
            id_penanggung_jawab: '',
            nama_penanggung_jawab: '',
            to_ketua_id: '',
            transferred_at: '',
            ...baseData
        };">
    <form
        id="transferKegiatanForm"
        :action="`/kegiatan/${formData.id_kegiatan}/transfer`"
        method="POST" class="grid grid-cols-1 gap-y-5">

        @csrf
        <div class="relative flex h-[85vh] w-full max-w-[800px] flex-col overflow-hidden rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">

            <!-- HEADER (FIXED) -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white">Transfer Kepemilikan Kegiatan</h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Alihkan tanggung jawab pengelolaan kegiatan ini ke Ketua Tim yang baru.
                </p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900">
                
                {{-- ====== WARNING BANNER ====== --}}
                <div class="mb-5 rounded-xl border border-amber-300 dark:border-amber-500/40 bg-amber-50 dark:bg-amber-500/10 px-4 py-3">
                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-400 mb-1 flex items-center gap-1.5">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        PENTING: Tindakan ini tidak dapat dibatalkan!
                    </p>
                    <ul class="list-disc pl-5 space-y-1 text-xs text-amber-700 dark:text-amber-300/90">
                        <li>Transfer kepemilikan kegiatan hanya dapat dilakukan **satu kali**.</li>
                        <li>Seluruh aksi operasional (penugasan baru, penerimaan, CKP) secara default akan berjalan di bawah Ketua Tim yang baru.</li>
                        <li>Akses Ketua Tim lama akan berubah menjadi **Read-only (Header saja)** dan tidak bisa masuk ke halaman detail.</li>
                    </ul>
                </div>
                {{-- ====== END WARNING BANNER ====== --}}

                {{-- ====== VALIDATION BANNER ====== --}}
                <div id="validationBannerTransfer"
                    class="hidden mb-5 rounded-xl border border-red-300 dark:border-red-500/40 bg-red-50 dark:bg-red-500/10 px-4 py-3">
                    <p class="text-sm font-medium text-red-700 dark:text-red-400 mb-1">
                        ⚠ Ada beberapa field yang belum diisi atau tidak valid:
                    </p>
                    <ul id="validationListTransfer" class="list-disc pl-5 space-y-1"></ul>
                </div>
                {{-- ====== END VALIDATION BANNER ====== --}}

                <!-- Nama Kegiatan (readonly tampilan) -->
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Kegiatan
                    </label>
                    <input type="text" :value="formData.nama_rk_kegiatan" disabled
                        class="w-full h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                </div>

                <!-- Ketua Tim Saat Ini (readonly tampilan) -->
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Ketua Tim Saat Ini (Original)
                    </label>
                    <input type="text" :value="formData.nama_penanggung_jawab" disabled
                        class="w-full h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                </div>

                <!-- Pilih Ketua Tim Baru (Custom Dropdown dengan Search & Keyboard Navigation) -->
                <div class="mb-4" x-data="{
                    open: false,
                    highlightedIndex: -1,
                    search: '',

                    pegawais: [
                        @foreach ($pegawais as $p)
                            {
                                id: '{{ $p->id_pegawai }}',
                                text: '{{ addslashes($p->nama_pegawai) }}'
                            },
                        @endforeach
                    ],

                    get filteredPegawais() {
                        let filtered = this.pegawais.filter(p => p.id !== formData.id_penanggung_jawab);
                        if (!this.search) return filtered;
                        return filtered.filter(p =>
                            p.text.toLowerCase().includes(this.search.toLowerCase())
                        );
                    },

                    get selectText() {
                        if (!formData.to_ketua_id) return '-- Pilih Ketua Tim Penerima --';
                        let opt = this.pegawais.find(p => p.id === formData.to_ketua_id);
                        return opt ? opt.text : '-- Pilih Ketua Tim Penerima --';
                    },

                    selectPegawai(p) {
                        formData.to_ketua_id = p.id;
                        this.open = false;
                        this.search = '';
                        this.highlightedIndex = -1;
                    },

                    highlightNext() {
                        if (this.highlightedIndex < this.filteredPegawais.length - 1) {
                            this.highlightedIndex++;
                            this.$nextTick(() => {
                                const container = this.$refs.listContainer;
                                const activeItem = container.children[this.highlightedIndex];
                                if (activeItem) {
                                    activeItem.scrollIntoView({ block: 'nearest' });
                                }
                            });
                        }
                    },

                    highlightPrev() {
                        if (this.highlightedIndex > 0) {
                            this.highlightedIndex--;
                            this.$nextTick(() => {
                                const container = this.$refs.listContainer;
                                const activeItem = container.children[this.highlightedIndex];
                                if (activeItem) {
                                    activeItem.scrollIntoView({ block: 'nearest' });
                                }
                            });
                        }
                    },

                    selectHighlighted() {
                        if (this.highlightedIndex >= 0) {
                            this.selectPegawai(this.filteredPegawais[this.highlightedIndex]);
                        }
                    }
                }">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Pilih Ketua Tim Baru <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" name="to_ketua_id" id="to_ketua_id" x-model="formData.to_ketua_id">

                    <div class="relative"
                        @keydown.arrow-down.prevent="if(!open) open = true; else highlightNext()"
                        @keydown.arrow-up.prevent="highlightPrev()"
                        @keydown.enter.prevent="if(open) selectHighlighted(); else open = true"
                        @keydown.escape="open = false">

                        <button type="button"
                            id="to_ketua_select_btn"
                            @click="
                                open = !open;
                                if(open){
                                    search = '';
                                    highlightedIndex = -1;
                                }
                            "
                            @click.outside="open = false"
                            class="flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800">

                            <span x-text="selectText" class="truncate"
                                :class="!formData.to_ketua_id ? 'text-gray-400' : 'text-gray-800 dark:text-gray-200'">
                            </span>

                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                                <path stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition
                            class="absolute z-50 mt-1 w-full rounded-lg border bg-white shadow-lg dark:bg-gray-800 border-gray-200 dark:border-gray-700">

                            <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                <input type="text" x-model="search" placeholder="Cari pegawai..."
                                    class="w-full px-3 py-2 text-sm rounded-md border focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                    @input="highlightedIndex = -1">
                            </div>

                            <div class="max-h-48 overflow-y-auto" x-ref="listContainer">
                                <template x-for="(p, index) in filteredPegawais" :key="p.id">
                                    <button type="button" @click="selectPegawai(p)"
                                        class="w-full px-4 py-3 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0 text-gray-800 dark:text-gray-200"
                                        :class="[highlightedIndex === index ? 'bg-gray-50 dark:bg-gray-700' : '']">
                                        <span x-text="p.text"></span>
                                    </button>
                                </template>

                                <div x-show="filteredPegawais.length === 0"
                                    class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center italic">
                                    Pegawai tidak ditemukan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tanggal Transfer (DatePicker) -->
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tanggal Efektif Transfer <span class="text-red-500">*</span>
                    </label>
                    <x-form.date-picker id="transferred_at" x-model="formData.transferred_at" name="transferred_at" placeholder="Pilih Tanggal Transfer"/>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-700">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button @click="open = false" type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        Batal
                    </button>

                    <button id="submitTransferButton" type="button"
                        class="flex w-full justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 sm:w-auto">
                        Proses Transfer
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-ui.smart-modal>

<script>
    function clearTransferValidation() {
        document.querySelectorAll('#transferKegiatanForm .input-invalid, #to_ketua_select_btn').forEach(el => {
            el.classList.remove('border-red-500', 'bg-red-50');
        });
        const banner = document.getElementById('validationBannerTransfer');
        if (banner) banner.classList.add('hidden');
    }

    function markTransferInvalid(el) {
        if (!el) return;
        el.classList.add('border-red-500', 'bg-red-50');
        if (el.id === 'to_ketua_id') {
            const btn = document.getElementById('to_ketua_select_btn');
            if (btn) btn.classList.add('border-red-500', 'bg-red-50');
        }
    }

    function validateTransferForm() {
        clearTransferValidation();
        const errors = [];

        const toKetua = document.getElementById('to_ketua_id');
        if (!toKetua?.value) {
            errors.push('Ketua Tim penerima wajib dipilih');
            markTransferInvalid(toKetua);
        }

        const dateEl = document.getElementById('transferred_at');
        if (!dateEl?.value) {
            errors.push('Tanggal efektif transfer wajib diisi');
            markTransferInvalid(dateEl);
        }

        return errors;
    }

    function showTransferValidationBanner(errors) {
        const banner = document.getElementById('validationBannerTransfer');
        const list = document.getElementById('validationListTransfer');
        if (!banner || !list) return;

        list.innerHTML = '';
        errors.forEach(msg => {
            const li = document.createElement('li');
            li.textContent = msg;
            li.className = 'text-xs text-red-600 dark:text-red-400';
            list.appendChild(li);
        });

        banner.classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('submitTransferButton')?.addEventListener('click', function(e) {
            e.preventDefault();
            const errors = validateTransferForm();
            if (errors.length > 0) {
                showTransferValidationBanner(errors);
                return;
            }

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Tanggung jawab kegiatan akan ditransfer sepenuhnya. Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Transfer!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('transferKegiatanForm').submit();
                }
            });
        });
    });
</script>
