@extends('layouts.dashboard')

@php
    $kepalaBps = Auth::user()->active_role === 'Pimpinan'
        && Auth::user()->nama_pegawai === 'Sukendro Suryo Wiguno, SST, M.Ec.Dev'
        && str_contains(Auth::user()->jabatan, 'Kepala BPS Ogan Ilir');
@endphp

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    @php
        $years = $agendas->flatMap(function($agenda) {
            if (!$agenda->tanggal_mulai || !$agenda->tanggal_selesai) return [];
            $start = \Carbon\Carbon::parse($agenda->tanggal_mulai)->year;
            $end = \Carbon\Carbon::parse($agenda->tanggal_selesai)->year;
            return range($start, $end);
        })->unique()->sort()->values();

        $currentYear = date('Y');
        if (!$years->contains($currentYear)) {
            $years->push((int)$currentYear);
            $years = $years->sort()->values();
        }
    @endphp

    <!-- Bagian Filter Tahun dan Bulan -->
    <div
        class="flex flex-row items-center justify-between rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 mb-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto">
            <!-- Filter Group: Tahun -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 whitespace-nowrap dark:text-gray-300">
                    Tahun:
                </label>
                <div x-data="{ isOptionSelected: true }" class="relative z-20 bg-transparent">
                    <select id="tahunFilter"
                        class="dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:placeholder:text-gray-500 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-28 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-3 pr-8 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden">
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }} class="text-gray-700 dark:text-gray-300">
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                    <span
                        class="pointer-events-none absolute top-1/2 right-2.5 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                        <svg class="stroke-current" width="14" height="14" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Filter Group: Bulan -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 whitespace-nowrap dark:text-gray-300">
                    Bulan:
                </label>
                <div x-data="{ isOptionSelected: true }" class="relative z-20 bg-transparent">
                    <select id="bulanFilter"
                        class="dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:placeholder:text-gray-500 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-36 appearance-none rounded-lg border border-gray-300 bg-transparent bg-none pl-3 pr-8 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden">
                        @php
                            $indonesianMonths = [
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember'
                            ];
                            $currentMonthVal = date('m');
                        @endphp
                        @foreach ($indonesianMonths as $num => $name)
                            <option value="{{ $num }}" {{ $num == $currentMonthVal ? 'selected' : '' }} class="text-gray-700 dark:text-gray-300">
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    <span
                        class="pointer-events-none absolute top-1/2 right-2.5 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                        <svg class="stroke-current" width="14" height="14" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        <!-- Tombol Tambah Agenda -->
        <button
            class="gap-2 rounded-full border border-gray-300
        bg-white px-4 py-3 text-sm font-medium text-gray-700
        shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
            @click="$dispatch('open-smart-modal', {
            modalId: 'modal-agenda',
            mode: 'create'
        })">
            Tambah Agenda
        </button>
    </div>

    <!-- MODAL AGENDA PIMPINAN -->
    <x-ui.smart-modal id="modal-agenda" class="max-w-2xl"
        @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-agenda') return;

        mode = $event.detail.mode ?? 'create';
        itemKey = $event.detail.key ?? null;

        let baseData = $event.detail.data || {};
        formData = {
            nama_agenda: '',
            tanggal_mulai: '',
            tanggal_selesai: '',
            rk_jpt: '',
            iki_jpt: '',
            target: '',
            satuan_target: '',
            realisasi: '',
            link_bukti: '',
            status: 'Belum Selesai',
            butuh_dl: 0,
            ...baseData
        };">

        <form
            id="addAgendaForm"
            :action="mode === 'edit'
                ? `/agenda-pimpinan/${itemKey}`
                : `{{ route('agenda.store') }}`"
            method="POST" class="grid grid-cols-1 gap-y-5">
            @csrf
            <template x-if="mode === 'edit'">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="relative flex h-[90vh] w-full max-w-[800px] flex-col overflow-hidden rounded-3xl bg-white dark:bg-gray-900 dark:border dark:border-gray-800">

                <!-- HEADER -->
                <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
                    <h4 class="text-2xl font-semibold text-gray-800 dark:text-white"
                        x-text="mode === 'create' ? 'Tambah Agenda Pimpinan' : 'Edit Agenda Pimpinan'"></h4>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        x-text="mode === 'create' ? 'Masukkan agenda pimpinan baru' : 'Edit agenda pimpinan yang sudah ada'">
                    </p>
                </div>

                <!-- BODY (SCROLL) -->
                <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900">
                    {{-- VALIDATION BANNER --}}
                    <div id="validationBannerAgenda"
                        class="hidden rounded-xl border border-red-300 dark:border-red-500/40 bg-red-50 dark:bg-red-500/10 px-4 py-3 mb-4">
                        <p class="text-sm font-medium text-red-700 dark:text-red-400 mb-1">
                            ⚠ Ada beberapa field yang belum diisi atau tidak valid:
                        </p>
                        <ul id="validationListAgenda" class="list-disc pl-5 space-y-1"></ul>
                    </div>

                    <!-- Nama Kegiatan -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nama Agenda <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="formData.nama_agenda" name="nama_agenda" id="nama_agenda"
                            placeholder="Masukkan Nama Kegiatan"
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                    </div>

                    <!-- Waktu Mulai -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Waktu Mulai <span class="text-red-500">*</span>
                        </label>
                        <x-form.date-picker id="tanggal_mulai" x-model="formData.tanggal_mulai" name="tanggal_mulai"
                            placeholder="Pilih Tanggal" />
                    </div>

                    <!-- Waktu Selesai -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Waktu Selesai <span class="text-red-500">*</span>
                        </label>
                        <x-form.date-picker id="tanggal_selesai" name="tanggal_selesai" x-model="formData.tanggal_selesai"
                            placeholder="Pilih Tanggal" />
                    </div>

                    <!-- Rencana JPT -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Rencana JPT <span class="text-red-500">*</span>
                        </label>
                        <select id="rk_jpt" name="rk_jpt" x-model="formData.rk_jpt"
                            @change="
                                formData.iki_jpt = '';
                                loadIkiByRkForAgenda(formData.rk_jpt, {})
                            "
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <option value="" class="dark:text-gray-400">-- Pilih RK JPT --</option>
                            @foreach ($rkJpts as $rk)
                                <option value="{{ $rk->id }}" class="dark:text-gray-300">
                                    {{ $rk->nama_rencana_jpt }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Indikator JPT -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Indikator JPT <span class="text-red-500">*</span>
                        </label>
                        <input type="hidden" name="iki_jpt" id="iki_jpt_hidden">
                        <select id="iki_jpt"
                            @change="document.getElementById('iki_jpt_hidden').value = $event.target.value"
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <option value="">-- Harap pilih RK JPT dulu --</option>
                        </select>
                    </div>

                    <!-- Target -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Target <span class="text-red-500">*</span>
                        </label>
                        <input type="number" x-model="formData.target" name="target" id="target"
                            placeholder="Masukkan Target"
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                    </div>

                    <!-- Satuan Target -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Satuan Target <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="formData.satuan_target" name="satuan_target" id="satuan_target"
                            placeholder="Masukkan Satuan Target"
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                    </div>

                    <!-- Realisasi -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Realisasi <span class="text-red-500">*</span>
                        </label>
                        <input type="number" x-model="formData.realisasi" name="realisasi" id="realisasi"
                            placeholder="Masukkan Realisasi"
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                    </div>

                    <!-- Link Bukti -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Link Bukti
                        </label>
                        <input type="url" x-model="formData.link_bukti" name="link_bukti" id="link_bukti"
                            placeholder="https://example.com/bukti"
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                    </div>

                    <!-- Toggle DL -->
                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Kebutuhan Dinas Luar
                        </label>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="formData.butuh_dl = formData.butuh_dl ? 0 : 1"
                                :class="formData.butuh_dl ? 'bg-blue-500' : 'bg-gray-300'"
                                class="relative inline-flex h-7 w-14 items-center rounded-full transition">
                                <span :class="formData.butuh_dl ? 'translate-x-7' : 'translate-x-1'"
                                    class="inline-block h-5 w-5 bg-white rounded-full transition">
                                </span>
                            </button>
                            <span class="text-sm font-medium" :class="formData.butuh_dl ? 'text-blue-600' : 'text-gray-500'">
                                Dinas Luar (DL)
                            </span>
                        </div>
                        <input type="hidden" name="butuh_dl" :value="formData.butuh_dl">
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" x-model="formData.status"
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <option value="Selesai" class="dark:text-gray-300">Selesai</option>
                            <option value="Belum Selesai" class="dark:text-gray-300">Belum Selesai</option>
                        </select>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="shrink-0 border-t border-gray-200 px-6 py-3 dark:border-gray-700">
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button @click="open = false" type="button"
                            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            Batal
                        </button>
                        <button x-show="mode === 'create'" id="saveAgendaButton" type="button"
                            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                            Simpan Agenda
                        </button>
                        <button x-show="mode !== 'create'" type="submit"
                            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto dark:bg-brand-600 dark:hover:bg-brand-700">
                            Ubah Data Agenda
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </x-ui.smart-modal>

    <!-- TABEL UTAMA AGENDA PIMPINAN -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-x-auto">
        <table class="min-w-[1200px] lg:min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12 dark:text-gray-400">
                        No.
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-48 dark:text-gray-400">
                        Nama Kegiatan
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28 dark:text-gray-400">
                        Waktu
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">
                        RK JPT
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">
                        IKI JPT
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">
                        Target
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24 dark:text-gray-400">
                        Realisasi
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24 dark:text-gray-400">
                        Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32 dark:text-gray-400">
                        Bukti Dukung
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20 dark:text-gray-400">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody id="agendaTableBody" class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                <!-- Empty state placeholder -->
                <tr id="noDataRow" class="hidden">
                    <td colspan="10" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Tidak ada data agenda untuk bulan yang dipilih.
                    </td>
                </tr>
                @foreach ($agendas as $index => $agenda)
                    @php
                        $startMonth = \Carbon\Carbon::parse($agenda->tanggal_mulai)->startOfMonth();
                        $endMonth = \Carbon\Carbon::parse($agenda->tanggal_selesai)->startOfMonth();
                        $totalBulan = $startMonth->diffInMonths($endMonth) + 1;
                        
                        $target = $agenda->target ?? 0;
                        $realisasi = $agenda->realisasi ?? 0;
                        $progressPercent = $target > 0 ? round(($realisasi / $target) * 100) : 0;
                        
                        $ckpSelesai100Persen = ($progressPercent >= 100) && $agenda->ckpBulanan->contains(function ($ckp) use ($target) {
                            return $ckp->realisasi >= $target;
                        });

                        $isCkpPimpinan = $ckpSelesai100Persen || $agenda->ckpBulanan->count() >= $totalBulan;

                        // Calculate spanned months in YYYY-MM format
                        $spannedMonths = [];
                        $current = $startMonth->copy();
                        while ($current->lessThanOrEqualTo($endMonth)) {
                            $spannedMonths[] = $current->format('Y-m');
                            $current->addMonth();
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 agenda-row {{ $isCkpPimpinan ? 'bg-green-100/50 hover:bg-green-100/80 dark:bg-green-900/50 hover:dark:bg-green-900/80' : '' }}"
                        data-months='@json($spannedMonths)'
                        data-tahun="{{ date('Y', strtotime($agenda->tanggal_mulai)) }}">
                        <td
                            class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 text-center dark:text-gray-300 agenda-index">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                            <div class="max-w-xs break-words">{{ $agenda->nama_agenda }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                            <div>{{ \Carbon\Carbon::parse($agenda->tanggal_mulai)->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-400">sd</div>
                            <div>{{ \Carbon\Carbon::parse($agenda->tanggal_selesai)->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            <div class="max-w-md">
                                <div>
                                    {{ $agenda->rencanaJpt->nama_rencana_jpt ?? '-' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            <div class="max-w-md">
                                <div>
                                    {{ $agenda->indikatorJpt->nama_indikator_jpt ?? '-' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-orange-600 dark:text-orange-400">
                            <div class="max-w-md">
                                <div>
                                    {{ $agenda->target ?? '-' }} {{ $agenda->satuan_target ?? '-' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-teal-600 dark:text-teal-400">
                            <div class="max-w-md">
                                <div>
                                    {{ $agenda->realisasi ?? '-' }} {{ $agenda->satuan_target ?? '-' }}
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span
                                class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                {{ $agenda->status == 'Selesai'
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                {{ $agenda->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            @if ($agenda->link_bukti)
                                <a href="{{ $agenda->link_bukti }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 border border-blue-300 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-all duration-200 dark:bg-blue-900/30 dark:border-blue-700 dark:text-blue-400 dark:hover:bg-blue-900/50"
                                    title="{{ $agenda->link_bukti }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                        </path>
                                    </svg>
                                    Lihat
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <div class="relative inline-block" x-data="{
                                showDropdown: false,
                                dropdownPosition: { x: 0, y: 0 },
                                openDropdown(event) {
                                    const button = event.currentTarget;
                                    const rect = button.getBoundingClientRect();
                                    const dropdownWidth = 192;
                                    
                                    this.dropdownPosition = {
                                        x: rect.left - dropdownWidth + 10,
                                        y: rect.top - 10
                                    };
                                    this.showDropdown = true;
                                },
                                closeDropdown() {
                                    this.showDropdown = false;
                                }}" x-on:mouseleave="closeDropdown()">

                                <button x-on:mouseenter="openDropdown($event)"
                                    class="inline-flex items-center gap-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <svg class="fill-current" width="16" height="16" viewBox="0 0 18 18" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" />
                                    </svg>
                                    Aksi
                                </button>

                                <div x-show="showDropdown" x-transition
                                    class="fixed z-[9999] bg-white dark:bg-gray-800 rounded shadow-xl border border-gray-200 dark:border-gray-700 min-w-[192px]"
                                    :style="`left: ${dropdownPosition.x}px; top: ${dropdownPosition.y}px;`"
                                    x-on:mouseenter="showDropdown = true" x-on:mouseleave="closeDropdown()">
                                    <div class="py-1">
                                        <button class="w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 flex items-center gap-2 border-b border-gray-100 dark:border-gray-700"
                                            @click="$dispatch('open-smart-modal', {
                                            modalId: 'modal-agenda',
                                            mode: 'edit',
                                            key: '{{ $agenda->id_agenda }}',
                                            data: {
                                                nama_agenda: '{{ addslashes($agenda->nama_agenda) }}',
                                                tanggal_mulai: '{{ $agenda->tanggal_mulai }}',
                                                tanggal_selesai: '{{ $agenda->tanggal_selesai }}',
                                                rk_jpt: '{{ $agenda->rk_jpt }}',
                                                iki_jpt: '{{ $agenda->iki_jpt }}',
                                                target: '{{ $agenda->target }}',
                                                satuan_target: '{{ $agenda->satuan_target }}',
                                                realisasi: '{{ $agenda->realisasi }}',
                                                link_bukti: '{{ $agenda->link_bukti }}',
                                                status: '{{ $agenda->status }}',
                                                butuh_dl: {{ $agenda->butuh_dl ? 1 : 0 }}
                                            }
                                        })">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Edit
                                        </button>

                                        @if($agenda->ckpBulanan->count() > 0)
                                            <div class="relative group flex w-full">
                                                <button type="button" disabled
                                                    class="w-full text-left px-4 py-3 text-sm text-gray-400 dark:text-gray-500 flex items-center gap-2 cursor-not-allowed">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                                <!-- Tooltip di sebelah kiri dropdown -->
                                                <div class="absolute right-full top-1/2 -translate-y-1/2 mr-2 hidden group-hover:block w-40 p-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs rounded-lg shadow-xl ring-1 ring-black/5 dark:ring-white/10 z-50 text-center leading-relaxed whitespace-normal">
                                                    Sudah masuk CKP Pimpinan.<br>Gak bisa dibatalkan.
                                                    <div class="absolute top-1/2 -right-2 -translate-y-1/2 border-4 border-transparent border-l-white dark:border-l-gray-800"></div>
                                                </div>
                                            </div>
                                        @else
                                            <form id="delete-agenda-{{ $agenda->id_agenda }}"
                                                action="{{ route('agenda.delete', $agenda->id_agenda) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    onclick="confirmDeleteAgenda('delete-agenda-{{ $agenda->id_agenda }}', {{ json_encode($agenda->nama_agenda) }})"
                                                    class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 dark:text-red-400 dark:hover:bg-red-900/20">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif

                                        @if($kepalaBps)
                                            @if($isCkpPimpinan)
                                                <button disabled
                                                    class="border-t border-gray-100 dark:border-gray-700 w-full text-left px-4 py-3 text-sm flex items-center gap-2 text-green-600 bg-green-50/50 dark:bg-green-900/20 dark:text-green-400 cursor-not-allowed opacity-80"
                                                    title="{{ $ckpSelesai100Persen ? 'Semua target sudah jadi CKP' : 'Sudah jadi CKP Pimpinan untuk seluruh bulan' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    {{ $ckpSelesai100Persen ? 'Sudah CKP Semua' : 'Sudah CKP' }}
                                                </button>
                                            @elseif($agenda->status === "Selesai" && $realisasi > 0)
                                                <button type="button"
                                                    @click="$dispatch('open-smart-modal', {
                                                        modalId: 'modal-ckp-universal',
                                                        data : {
                                                            id_agenda: @js($agenda->id_agenda),
                                                            nama_pegawai: @js(Auth::user()->nama_pegawai ?? ''),
                                                            nama_agenda: @js($agenda->nama_agenda),
                                                            uraian: @js('Melaksanakan dan Menyelesaikan ' . $agenda->nama_agenda . ' dengan target ' . $realisasi . ' dari total target ' . $target),
                                                            target_kuantitas: {{ $agenda->target }},
                                                            realisasi_kuantitas: {{ $realisasi }},
                                                            satuan: @js($agenda->satuan_target),
                                                            is_pimpinan: true,
                                                            tanggal_mulai: @js($agenda->tanggal_mulai),
                                                            tanggal_selesai: @js($agenda->tanggal_selesai),
                                                            bulanSudahCkp : @js($agenda->ckpBulanan->pluck('bulan_ckp')->values()->toArray())
                                                        }
                                                    })"
                                                    class="border-t border-gray-100 dark:border-gray-700 w-full text-left px-4 py-3 text-sm flex items-center gap-2 text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/20"
                                                    title="Jadikan CKP Pimpinan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Buat CKP
                                                </button>
                                            @else
                                                <button disabled
                                                    class="border-t border-gray-100 dark:border-gray-700 w-full text-left px-4 py-3 text-sm flex items-center gap-2 text-gray-400 cursor-not-allowed bg-gray-50 dark:bg-gray-800"
                                                    title="{{ $agenda->status !== 'Selesai' ? 'Status Agenda belum Selesai' : 'Belum ada realisasi' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Belum CKP
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MODAL CKP (Universal: Anggota Tim, Ketua Tim, dan Pimpinan) --}}
    @include('pages.main.components.modals.tagihan-kerja.modal-ckp-universal')

    <script>
        // FUNGSI LOAD IKI BERDASARKAN RK (untuk modal agenda pimpinan)
        async function loadIkiByRkForAgenda(rkId, formData) {
            const selectEl = document.getElementById('iki_jpt');
            const hiddenEl = document.getElementById('iki_jpt_hidden');

            if (!rkId) {
                if (selectEl) {
                    selectEl.innerHTML = '<option value="">-- Harap pilih RK JPT dulu --</option>';
                }
                if (hiddenEl) hiddenEl.value = '';
                return;
            }

            // Tampilkan loading sementara
            if (selectEl) {
                selectEl.innerHTML = '<option value="">Memuat...</option>';
                selectEl.disabled = true;
            }

            try {
                const response = await fetch(`/rencana-indikator-jpt/${rkId}/indikator`);
                const data = await response.json();

                if (!selectEl) return;

                // Build options langsung ke DOM
                const pending = formData._pendingIndikatorId ? String(formData._pendingIndikatorId) : '';
                if (formData._pendingIndikatorId !== undefined) formData._pendingIndikatorId = '';

                let html = '<option value="">-- Pilih IKI JPT --</option>';
                data.forEach(iki => {
                    const selected = String(iki.id) === pending ? 'selected' : '';
                    html += `<option value="${iki.id}" ${selected}>${iki.nama_indikator_jpt}</option>`;
                });

                selectEl.innerHTML = html;
                selectEl.disabled = false;

                // Sync ke hidden input
                if (hiddenEl) hiddenEl.value = selectEl.value;

            } catch (error) {
                if (selectEl) {
                    selectEl.innerHTML = '<option value="">Gagal memuat data</option>';
                    selectEl.disabled = false;
                }
            }
        }

        // VALIDASI FRONTEND
        function clearValidationAgenda() {
            document.querySelectorAll('.input-invalid').forEach(el => {
                el.classList.remove(
                    'input-invalid',
                    'border-red-500', 'dark:border-red-500',
                    'bg-red-50', 'dark:bg-red-500/10'
                );
            });

            document.querySelectorAll('.field-error-msg').forEach(el => {
                el.classList.add('hidden');
            });

            const banner = document.getElementById('validationBannerAgenda');
            if (banner) banner.classList.add('hidden');
        }

        function markInvalidAgenda(el) {
            if (!el) return;
            el.classList.add(
                'input-invalid',
                'border-red-500', 'dark:border-red-500',
                'bg-red-50', 'dark:bg-red-500/10'
            );
        }

        function validateFormAgenda() {
            clearValidationAgenda();
            const errors = [];

            function addError(message, focusEl, inputEl) {
                errors.push({ message, focusEl });
                if (inputEl) markInvalidAgenda(inputEl);
                const errorMsg = inputEl?.closest('.mb-4')?.querySelector('.field-error-msg');
                if (errorMsg) errorMsg.classList.remove('hidden');
            }

            // Nama Kegiatan
            const namaKegiatan = document.getElementById('nama_agenda');
            if (!namaKegiatan?.value?.trim()) {
                addError('Nama kegiatan wajib diisi', namaKegiatan, namaKegiatan);
            }

            // Waktu Mulai
            const tglMulai = document.getElementById('tanggal_mulai');
            if (!tglMulai?.value) {
                addError('Waktu mulai wajib diisi', tglMulai, tglMulai);
            }

            // Waktu Selesai
            const tglSelesai = document.getElementById('tanggal_selesai');
            if (!tglSelesai?.value) {
                addError('Waktu selesai wajib diisi', tglSelesai, tglSelesai);
            } else if (tglMulai?.value && tglSelesai.value < tglMulai.value) {
                addError('Waktu selesai harus setelah atau sama dengan waktu mulai', tglSelesai, tglSelesai);
            }

            // RK JPT
            const rkJpt = document.getElementById('rk_jpt');
            if (!rkJpt?.value) {
                addError('Rencana JPT wajib dipilih', rkJpt, rkJpt);
            }

            // IKI JPT
            const ikiJpt = document.getElementById('iki_jpt');
            if (!ikiJpt?.value) {
                addError('Indikator JPT wajib dipilih', ikiJpt, ikiJpt);
            }

            // Target
            const target = document.getElementById('target');
            if (!target?.value) {
                addError('Target wajib diisi', target, target);
            }

            // Satuan Target
            const satuanTarget = document.getElementById('satuan_target');
            if (!satuanTarget?.value?.trim()) {
                addError('Satuan target wajib diisi', satuanTarget, satuanTarget);
            }

            // Realisasi
            const realisasi = document.getElementById('realisasi');
            if (!realisasi?.value) {
                addError('Realisasi wajib diisi', realisasi, realisasi);
            } else if (target?.value && parseFloat(realisasi.value) > parseFloat(target.value)) {
                addError('Realisasi tidak boleh melebihi target', realisasi, realisasi);
            }

            // Link Bukti
            const linkBukti = document.getElementById('link_bukti');
            if (linkBukti?.value?.trim()) {
                const urlPattern = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
                if (!urlPattern.test(linkBukti.value.trim())) {
                    addError('Link bukti harus berupa URL yang valid', linkBukti, linkBukti);
                }
            }

            // Status
            const status = document.querySelector('select[name="status"]');
            if (!status?.value) {
                addError('Status wajib dipilih', status, status);
            }

            return errors;
        }

        function showValidationAgendaBanner(errors) {
            const banner = document.getElementById('validationBannerAgenda');
            const list = document.getElementById('validationListAgenda');
            if (!banner || !list) return;

            list.innerHTML = '';
            errors.forEach(err => {
                const li = document.createElement('li');
                li.textContent = err.message;
                li.className = 'text-xs text-red-600 dark:text-red-400 cursor-pointer hover:underline';
                if (err.focusEl) {
                    li.onclick = () => {
                        err.focusEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => err.focusEl.focus(), 300);
                    };
                }
                list.appendChild(li);
            });

            banner.classList.remove('hidden');
            banner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function saveAgenda(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const errors = validateFormAgenda();
            if (errors.length > 0) {
                showValidationAgendaBanner(errors);
                return;
            }

            const form = document.querySelector('#modal-agenda form');
            if (form) form.submit();
        }

        function confirmSaveAgenda() {
            const form = document.getElementById('addAgendaForm');
            if (!form) {
                alert('Form tidak ditemukan');
                return;
            }
            form.submit();
        }

        function confirmDeleteAgenda(formId, namaKegiatan) {
            SwalHelper.confirmDelete(formId, namaKegiatan);
        }

        // FILTER TAHUN
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('saveAgendaButton')?.addEventListener('click', saveAgenda);
            
            // Listen event yang sama dengan yang di-dispatch tombol Edit
            window.addEventListener('open-smart-modal', function(e) {
                const detail = e.detail;
                if (detail.modalId !== 'modal-agenda' || detail.mode !== 'edit') return;
                if (!detail.data?.rk_jpt) return;

                // Simpan pending data
                window._agendaEditPending = {
                    rkId: String(detail.data.rk_jpt),
                    indikatorId: String(detail.data.iki_jpt || '')
                };

                const checkAndLoad = (attempt) => {
                    const selectEl = document.getElementById('iki_jpt');
                    if (!selectEl) {
                        if (attempt < 20) requestAnimationFrame(() => checkAndLoad(attempt + 1));
                        return;
                    }

                    // Cek apakah modal sudah visible
                    const modalEl = document.getElementById('modal-agenda');
                    const isVisible = modalEl && getComputedStyle(modalEl).display !== 'none';

                    if (isVisible && window._agendaEditPending) {
                        const { rkId, indikatorId } = window._agendaEditPending;
                        window._agendaEditPending = null;
                        loadIkiByRkForAgenda(rkId, { _pendingIndikatorId: indikatorId });
                    } else if (attempt < 30) {
                        requestAnimationFrame(() => checkAndLoad(attempt + 1));
                    }
                };

                requestAnimationFrame(() => checkAndLoad(0));
            }); 

            const tahunSelect = document.getElementById('tahunFilter');
            const bulanSelect = document.getElementById('bulanFilter');
            const rows = document.querySelectorAll('.agenda-row');
            
            let currentTahun = tahunSelect ? tahunSelect.value : String(new Date().getFullYear());
            let currentBulan = bulanSelect ? bulanSelect.value : String(new Date().getMonth() + 1).padStart(2, '0');

            function filterTable() {
                let visibleIndex = 1;
                rows.forEach(row => {
                    const rowMonths = JSON.parse(row.getAttribute('data-months') || '[]');
                    const targetYearMonth = `${currentTahun}-${currentBulan}`;
                    const hasMatch = rowMonths.includes(targetYearMonth);
                    
                    if (hasMatch) {
                        row.style.display = '';
                        const indexCell = row.querySelector('.agenda-index');
                        if (indexCell) {
                            indexCell.textContent = visibleIndex++;
                        }
                    } else {
                        row.style.display = 'none';
                    }
                });

                const noDataRow = document.getElementById('noDataRow');
                if (noDataRow) {
                    if (visibleIndex === 1) {
                        noDataRow.classList.remove('hidden');
                    } else {
                        noDataRow.classList.add('hidden');
                    }
                }
            }



            if (tahunSelect) {
                tahunSelect.addEventListener('change', function() {
                    currentTahun = tahunSelect.value;
                    filterTable();
                });
            }

            if (bulanSelect) {
                bulanSelect.addEventListener('change', function() {
                    currentBulan = bulanSelect.value;
                    filterTable();
                });
            }

            filterTable();
        });
    </script>
@endsection
