<!-- Modal CKP Universal (untuk CKP Pegawai dan CKP Ketua Tim) -->
<div x-data="ckpUniversalModal()"
    @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-ckp-universal') return;
        
        const d = $event.detail.data || {};
        ckpData = {
            id_penugasan: d.id_penugasan || null,
            id_sub_kegiatan: d.id_sub_kegiatan || null,
            id_agenda: d.id_agenda || null,
            nama_pegawai: d.nama_pegawai || '',
            nama_sub_kegiatan: d.nama_sub_kegiatan || '',
            nama_agenda: d.nama_agenda || '',
            uraian: d.uraian || '',
            satuan: d.satuan || '',
            target_kuantitas: d.target_kuantitas || 0,
            keterangan: d.keterangan || '',
            is_ketua_tim: d.is_ketua_tim || false,
            is_pimpinan: d.is_pimpinan || false,
            tanggal_mulai: d.tanggal_mulai || '',
            tanggal_selesai: d.tanggal_selesai || '',
            bulanDiterima: d.bulanDiterima || [],
            bulanSudahCkp: d.bulanSudahCkp || [],
        };
        bulanCkp = '';

        // Auto-set bulan CKP
        $nextTick(() => {
            const isAnggota = !ckpData.is_ketua_tim && !ckpData.is_pimpinan;
            if (isAnggota) {
                // Anggota Tim: hanya bulan yang sudah ada pengiriman Diterima
                const accepted = ckpData.bulanDiterima || [];
                if (accepted.length === 1) {
                    bulanCkp = accepted[0];
                }
            } else if (bulanOptions.length === 1) {
                bulanCkp = bulanOptions[0].value;
            }
        });
        
        showCkpModal = true;"
    @open-ckp-modal.window="
        if ($event.detail.modalId !== 'modal-ckp') return;
        
        ckpData = {
            id_penugasan: $event.detail.id_penugasan || null,
            id_sub_kegiatan: $event.detail.id_sub_kegiatan || null,
            id_agenda: $event.detail.id_agenda || null,
            nama_pegawai: $event.detail.nama_pegawai || '',
            nama_sub_kegiatan: $event.detail.nama_sub_kegiatan || '',
            nama_agenda: $event.detail.nama_agenda || '',
            uraian: $event.detail.uraian || '',
            satuan: $event.detail.satuan || '',
            target_kuantitas: $event.detail.target_kuantitas || 0,
            keterangan: $event.detail.keterangan || '',
            is_ketua_tim: $event.detail.is_ketua_tim || false,
            is_pimpinan: $event.detail.is_pimpinan || false,
            tanggal_mulai: $event.detail.tanggal_mulai || '',
            tanggal_selesai: $event.detail.tanggal_selesai || '',
            bulanDiterima: $event.detail.bulanDiterima || [],
        };
        bulanCkp = '';

        $nextTick(() => {
            const isAnggota = !ckpData.is_ketua_tim && !ckpData.is_pimpinan;
            if (isAnggota) {
                const accepted = ckpData.bulanDiterima || [];
                if (accepted.length === 1) {
                    bulanCkp = accepted[0];
                }
            } else if (bulanOptions.length === 1) {
                bulanCkp = bulanOptions[0].value;
            }
        });
        
        showCkpModal = true;">

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
                            :class="ckpData.is_pimpinan 
                                ? 'bg-purple-100 dark:bg-purple-900/30' 
                                : ckpData.is_ketua_tim 
                                    ? 'bg-green-100 dark:bg-green-900/30' 
                                    : 'bg-blue-100 dark:bg-blue-900/30'">

                            <svg class="w-5 h-5"
                                :class="ckpData.is_pimpinan 
                                    ? 'text-purple-600 dark:text-purple-400' 
                                    : ckpData.is_ketua_tim 
                                        ? 'text-green-600 dark:text-green-400' 
                                        : 'text-blue-600 dark:text-blue-400'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold text-gray-800 dark:text-white">
                                Konfirmasi Jadikan CKP
                            </h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                <span x-show="!ckpData.is_ketua_tim && !ckpData.is_pimpinan">Konfirmasi dan lengkapi data CKP dari Penugasan ini</span>
                                <span x-show="ckpData.is_ketua_tim">Konfirmasi dan lengkapi data CKP Ketua Tim dari Sub Kegiatan ini</span>
                                <span x-show="ckpData.is_pimpinan">Konfirmasi dan lengkapi data CKP Pimpinan dari Agenda Pimpinan ini</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- BODY -->
                <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900 max-h-[70vh]">
                    <form :action="ckpData.is_ketua_tim 
                        ? `{{ url('ckp/from-sub-kegiatan') }}/${ckpData.id_sub_kegiatan}`
                        : ckpData.is_pimpinan
                            ? `{{ url('ckp/from-agenda-pimpinan') }}/${ckpData.id_agenda}`
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
                                    <span x-show="!ckpData.is_ketua_tim && !ckpData.is_pimpinan">Informasi Penugasan</span>
                                    <span x-show="ckpData.is_ketua_tim">Informasi Sub Kegiatan</span>
                                    <span x-show="ckpData.is_pimpinan">Informasi Agenda Pimpinan</span>
                                </h4>
                            </div>

                            <div class="grid gap-3 text-sm">
                                <!-- Info Khusus Sub Kegiatan (Ketua Tim) -->
                                <template x-if="ckpData.is_ketua_tim">
                                    <div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Sub Kegiatan : </span>
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

                                <!-- Info Khusus Agenda Pimpinan -->
                                <template x-if="ckpData.is_pimpinan">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Agenda Pimpinan : </span>
                                        <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="ckpData.nama_agenda"></span>
                                    </div>
                                </template>

                                <!-- Info Universal -->
                                <div>
                                    <div x-show="ckpData.nama_pegawai">
                                        <span class="text-gray-500 dark:text-gray-400">
                                            <span x-show="!ckpData.is_ketua_tim && !ckpData.is_pimpinan">Pegawai (Anggota) :</span>
                                            <span x-show="ckpData.is_ketua_tim">Pegawai (Ketua Tim) :</span>
                                            <span x-show="ckpData.is_pimpinan">Pimpinan : </span>
                                        </span>
                                        <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="ckpData.nama_pegawai"></span>
                                    </div>
                                    <div :class="ckpData.nama_pegawai ? 'mt-2' : ''">
                                        <span class="text-gray-500 dark:text-gray-400">Satuan :</span>
                                        <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="ckpData.satuan"></span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="text-gray-500 dark:text-gray-400">Target Kuantitas :</span>
                                        <span class="ml-2 font-medium text-gray-900 dark:text-white" x-text="ckpData.target_kuantitas"></span>
                                    </div>
                                </div>
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

                            <!-- Bulan CKP — Anggota Tim: hanya bulan yang ada pengiriman Diterima -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Bulan CKP <span class="text-red-500">*</span>
                                </label>

                                {{-- Anggota Tim: dropdown dengan hanya bulan yang ada pengiriman Diterima --}}
                                <template x-if="isAnggotaTim">
                                    <div>
                                        <select name="bulan_ckp" x-model="bulanCkp" required
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                            <option value="">-- Pilih Bulan CKP --</option>
                                            <template x-for="opt in bulanOptions" :key="opt.value">
                                                <option :value="opt.value" 
                                                        x-text="opt.disabled ? (opt.reason === 'sudah_ckp' ? opt.label + ' — ✓ Sudah masuk ke CKP' : opt.label + ' — Belum ada pengiriman Diterima') : opt.label"
                                                        :disabled="opt.disabled"
                                                        :style="opt.disabled && opt.reason === 'sudah_ckp' ? 'color: #16a34a; background-color: #f0fdf4;' : (opt.disabled ? 'color: #9ca3af; background-color: #f3f4f6;' : '')"></option>
                                            </template>
                                        </select>
                                        <div class="mt-1.5 flex items-start gap-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 px-3 py-2">
                                            <svg class="w-4 h-4 shrink-0 text-blue-500 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                                Bulan CKP hanya bisa dipilih dari bulan yang sudah memiliki pengiriman <strong>Diterima</strong>. 
                                                Ini memastikan sinkronisasi antara bulan pengiriman dan bulan CKP.
                                            </p>
                                        </div>
                                    </div>
                                </template>

                                {{-- Ketua Tim & Pimpinan: dropdown bebas --}}
                                <template x-if="!isAnggotaTim">
                                    <div>
                                        <select name="bulan_ckp" x-model="bulanCkp" required
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                            <option value="">-- Pilih Bulan CKP --</option>
                                            <template x-for="opt in bulanOptions" :key="opt.value">
                                                <option :value="opt.value" x-text="opt.label"></option>
                                            </template>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                            Pilih bulan untuk CKP ini. Setiap bulan hanya boleh memiliki 1 CKP per entitas.
                                        </p>
                                    </div>
                                </template>
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
                            <span x-show="!ckpData.is_ketua_tim && !ckpData.is_pimpinan">Simpan sebagai CKP Anggota</span>
                            <span x-show="ckpData.is_ketua_tim">Simpan sebagai CKP Ketua Tim</span>
                            <span x-show="ckpData.is_pimpinan">Simpan sebagai CKP Pimpinan</span>
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
            bulanCkp: '',
            ckpData: {
                id_penugasan: null,
                id_sub_kegiatan: null,
                id_agenda: null,
                nama_pegawai: '',
                nama_sub_kegiatan: '',
                nama_agenda: '',
                uraian: '',
                satuan: '',
                target_kuantitas: 0,
                keterangan: '',
                is_ketua_tim: false,
                is_pimpinan: false,
                tanggal_mulai: '',
                tanggal_selesai: '',
                bulanDiterima: [],
            },

            // Helper: apakah ini mode Anggota Tim (dari penugasan)?
            get isAnggotaTim() {
                return !this.ckpData.is_ketua_tim && !this.ckpData.is_pimpinan;
            },

            get bulanOptions() {
                if (!this.ckpData.tanggal_mulai || !this.ckpData.tanggal_selesai) return [];
                const start = new Date(this.ckpData.tanggal_mulai);
                const end = new Date(this.ckpData.tanggal_selesai);
                const options = [];
                const bulanNama = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                const bulanDiterima = this.ckpData.bulanDiterima || [];
                let current = new Date(start.getFullYear(), start.getMonth(), 1);
                while (current <= end) {
                    const y = current.getFullYear();
                    const m = String(current.getMonth() + 1).padStart(2, '0');
                    const val = y + '-' + m;
                    const label = bulanNama[current.getMonth()] + ' ' + y;

                    if (this.isAnggotaTim) {
                        // Anggota Tim: hanya bulan yang ada pengiriman Diterima & belum CKP
                        const bulanSudahCkp = this.ckpData.bulanSudahCkp || [];
                        const isAccepted = bulanDiterima.includes(val);
                        const isSudahCkp = bulanSudahCkp.includes(val);
                        
                        let disabled = false;
                        let reason = '';
                        
                        if (isSudahCkp) {
                            disabled = true;
                            reason = 'sudah_ckp';
                        } else if (!isAccepted) {
                            disabled = true;
                            reason = 'belum_diterima';
                        }
                        
                        options.push({ value: val, label, disabled, reason });
                    } else {
                        // Ketua Tim & Pimpinan: semua bulan aktif
                        options.push({ value: val, label, disabled: false, reason: '' });
                    }
                    current.setMonth(current.getMonth() + 1);
                }
                return options;
            },

            submitCkpForm() {
                if (!this.bulanCkp) {
                    alert('Pilih bulan CKP terlebih dahulu');
                    return;
                }
                const form = document.getElementById('ckpUniversalForm');
                if (form) {
                    this.showCkpModal = false;
                    form.submit();
                }
            }
        }
    }
</script>