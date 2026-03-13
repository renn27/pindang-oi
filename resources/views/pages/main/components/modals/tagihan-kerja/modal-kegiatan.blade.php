<x-ui.smart-modal id="modal-kegiatan" class="max-w-2xl"
    @open-smart-modal.window="
            if ($event.detail.modalId !== 'modal-kegiatan') return;

            mode    = $event.detail.mode ?? 'create';
            itemKey = $event.detail.key ?? null;

            const payload = $event.detail.data ?? {
                id_bidang: {{ $bidang->id_bidang }},
                nama_bidang: {{ $bidang->nama_bidang }},
                id_penanggung_jawab: '',
                nama_penanggung_jawab: '',
                tahun_kegiatan: '',
                rk_jpt: '',
                iki_jpt: '',
                nama_rk_kegiatan: ''
            };

            // ✅ PENTING: mutate, jangan replace
            Object.assign(formData, payload);

            // autocomplete pegawai
            selectedId = formData.id_penanggung_jawab ?? '';
            search     = formData.nama_penanggung_jawab ?? '';

            // ✅ PENTING: tunggu DOM & Alpine sync
            {{-- $nextTick(async () => {
                if (formData.rk_jpt) {
                    const selectedIki = formData.iki_jpt;

                    // reset dulu
                    formData.iki_jpt = '';

                    await loadIkiByRk(formData.rk_jpt, formData);

                    // set ulang SETELAH options ada
                    formData.iki_jpt = selectedIki;
                }
            }); --}}

            $nextTick(async () => {
                if (formData.rk_jpt) {
                    const selectedIki = formData.iki_jpt;

                    // reset dulu supaya Alpine re-evaluate
                    formData.iki_jpt = '';

                    await loadIkiByRk(formData.rk_jpt, formData);

                    // set ulang SETELAH options masuk
                    formData.iki_jpt = selectedIki;
                }
            });


            {{-- $nextTick(() => {
                if (formData.rk_jpt) {
                    loadIkiByRk(formData.rk_jpt, formData);
                }
            }); --}}
        ">
    <form
        :action="mode === 'edit'
            ? `/kegiatan/${itemKey}`
            : '{{ route('kegiatan.store', $bidang->slug) }}'"
        method="POST" class="grid grid-cols-1 gap-y-5">
        @csrf
        <template x-if="mode === 'edit'">
            @method('PUT')
        </template>
        <div class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden
                rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">
            <!-- HEADER -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white"
                    x-text="mode === 'create' ? 'Tambah Kegiatan/RK Ketua' : 'Edit Kegiatan/RK Ketua'"></h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                    x-text="mode === 'create' ? 'Masukkan kegiatan yang baru' : 'Edit kegiatan yang sudah ada'"></p>
            </div>

            <!-- BODY (SCROLL DI SINI) -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900">
                <!-- Nama Bidang (readonly tampilan) -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Bidang
                    </label>

                    <input type="text" value="{{ $bidang->nama_bidang }}" disabled
                        class="w-full mb-4 h-11 rounded-lg border border-gray-300 bg-gray-100 px-4 text-sm text-gray-800
                                cursor-not-allowed dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                </div>

                <!-- ID Bidang (yang benar-benar dikirim ke backend) -->
                <input type="hidden" name="id_bidang" value="{{ $bidang->id_bidang }}">

                {{-- Nama Anggota --}}
                <div x-data="ketuaDropdown()"
                    @open-smart-modal.window="
                        if ($event.detail.modalId !== 'modal-kegiatan') return;
                        initFromModal($event.detail);
                    " class="mb-4">

                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Ketua
                    </label>

                    <!-- Hidden ID Pegawai (WAJIB buat submit) -->
                    <input type="hidden" name="id_penanggung_jawab" x-model="selectedId">

                    <!-- Input Visible -->
                    <div class="relative">
                        <input
                            type="text"
                            x-model="search"
                            @click="mode === 'create' && (open = true)"
                            @input="mode === 'create' && (open = true)"
                            @keydown.escape="open = false"
                            :readonly="mode === 'edit'"
                            placeholder="Pilih Ketua Kegiatan..."
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">

                        <!-- Dropdown -->
                        <div x-show="open && mode === 'create'"
                            x-transition
                            @click.outside="open = false"
                            class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto
                                    rounded-lg border border-gray-200 bg-white shadow-lg
                                    dark:border-gray-700 dark:bg-gray-800">
                            <template x-if="filteredPegawais.length === 0">
                                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 italic">
                                    Ketua tidak ditemukan
                                </div>
                            </template>

                            <template x-for="pegawai in filteredPegawais" :key="pegawai.id_pegawai">
                                <button type="button"
                                        @click="selectPegawai(pegawai)"
                                        class="flex w-full items-center px-4 py-3 text-left text-sm
                                            hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                    <div class="font-medium text-gray-800 dark:text-gray-200"
                                        x-text="pegawai.nama_pegawai"></div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Tahun Kegiatan --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Tahun Kegiatan
                    </label>
                    <input type="text" x-model="formData.tahun_kegiatan" name="tahun_kegiatan"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                {{-- Rencana JPT --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Rencana JPT
                    </label>
                    <select id="rk_jpt" name="rk_jpt" x-model="formData.rk_jpt"
                        @change="
                            formData.iki_jpt = '';
                            loadIkiByRk(formData.rk_jpt, formData)
                            "
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <option value="" class="dark:text-gray-400">-- Pilih RK JPT --</option>
                        @foreach ($rkJpts as $rk)
                            <option value="{{ $rk->id }}" class="dark:text-gray-300">
                                {{ $rk->nama_rencana_jpt }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Indikator JPT --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Indikator JPT
                    </label>

                    <select id="iki_jpt" name="iki_jpt" x-model="formData.iki_jpt"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">

                        <!-- OPTION DINAMIS -->
                        <option value=""
                            x-text="formData.rk_jpt
                                    ? '-- Pilih IKI JPT --'
                                    : '-- Harap pilih RK JPT dulu --'"
                            class="dark:text-gray-400">
                        </option>

                        <template x-for="iki in formData.ikiOptions" :key="iki.id">
                            <option :value="iki.id" x-text="iki.nama_indikator_jpt" class="dark:text-gray-300">
                            </option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Kegiatan
                    </label>
                    <input type="text" x-model="formData.nama_rk_kegiatan" name="nama_rk_kegiatan"
                        placeholder="Contoh : SNLIK2026"
                        class="h-11 w-full mb-4 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>
            </div>

            <!-- FOOTER -->
            <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-700">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button @click="open = false" type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        Batal
                    </button>

                    <button type="submit"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto dark:bg-brand-600 dark:hover:bg-brand-700">
                        <span x-text="mode === 'create' ? 'Simpan' : 'Update'"></span>
                    </button>
                </div>
            </div>

        </div>
    </form>
</x-ui.smart-modal>
<script>
    function ketuaDropdown() {
        @php
            $user = auth()->user();
            $filteredKetua = $ketuaTims;
            
            if ($user && method_exists($user, 'isActiveRole')) {
                if ($user->isActiveRole('Admin') || $user->isActiveRole('Pimpinan')) {
                    $filteredKetua = $ketuaTims;
                } elseif ($user->isActiveRole('Ketua Tim')) {
                    $filteredKetua = $ketuaTims->filter(fn($p) => $p->id_pegawai == $user->id_pegawai);
                }
            }
        @endphp

        return {
            open: false,
            search: '',
            selectedId: '',
            mode: 'create',

            ketuaTims: @js(
                $filteredKetua->map(fn($p) => [
                    'id_pegawai'   => $p->id_pegawai,
                    'nama_pegawai' => $p->nama_pegawai
                ])->values()
            ),

            initFromModal(detail) {
                this.mode = detail.mode ?? 'create';

                if (this.mode === 'edit') {
                    this.selectedId = detail.data.id_penanggung_jawab;
                    this.search     = detail.data.nama_penanggung_jawab;
                    this.open       = false;
                } else {
                    if (this.ketuaTims.length === 1) {
                        this.selectedId = this.ketuaTims[0].id_pegawai;
                        this.search     = this.ketuaTims[0].nama_pegawai;
                        this.open       = false;
                    } else {
                        this.selectedId = '';
                        this.search     = '';
                        this.open       = true;
                    }
                }
            },

            get filteredPegawais() {
                if (!this.search) return this.ketuaTims;

                return this.ketuaTims.filter(p =>
                    p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            selectPegawai(p) {
                this.selectedId = p.id_pegawai;
                this.search     = p.nama_pegawai;
                this.open       = false;
            }
        }
    }
</script>
