@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    {{-- <div class="mb-6 flex justify-end">
        <button
            class="gap-2 rounded-full border border-gray-300
            bg-white px-4 py-3 text-sm font-medium text-gray-700
            shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
            @click="$dispatch('open-smart-modal', {
                    modalId: 'modal-assign-role',
            })">
            Assign Role
        </button>
    </div> --}}

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th
                            class="px-4 py-3 w-16 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            No
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Nama Pegawai
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            Role
                        </th>
                        <th
                            class="px-4 py-3 w-32 text-center text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">
                            Edit Role
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($pegawais as $i => $pegawai)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                                {{ $i + 1 }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $pegawai->nama_pegawai }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $pegawai->jabatan }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                @php
                                    $roleColors = [
                                        'Admin' =>
                                            'bg-red-50 text-red-600 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800',
                                        'Pimpinan' =>
                                            'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800',
                                        'Ketua Tim' =>
                                            'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
                                        'Anggota Tim' =>
                                            'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800',
                                        'Belum Ada' =>
                                            'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700',
                                    ];

                                    $badges = collect();

                                    // 1. role struktural
                                    foreach ($pegawai->roles as $role) {
                                        $badges->push($role->nama_role);
                                    }

                                    // 2. status anggota tim (IMPLISIT)
                                    if ($pegawai->penugasanSebagaiAnggota()->exists()) {
                                        $badges->push('Anggota Tim');
                                    }

                                    // 3. fallback
                                    if ($badges->isEmpty()) {
                                        $badges->push('Belum Ada');
                                    }
                                @endphp

                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($badges->unique() as $badge)
                                        <span
                                            class="inline-flex items-center rounded-full
                                                px-3 py-1 text-xs font-medium border
                                                {{ $roleColors[$badge] ?? $roleColors['Belum Ada'] }}">
                                            {{ $badge }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-1.5
                                        text-xs font-medium text-gray-700 bg-white hover:border-brand-400 hover:text-brand-600
                                        dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:border-brand-500 dark:hover:text-brand-400"
                                    @click="$dispatch('open-smart-modal', {
                                        modalId: 'modal-assign-role',
                                        mode: 'edit',
                                        key: '{{ $pegawai->id_pegawai }}',
                                        data: {
                                            id_pegawai: '{{ $pegawai->id_pegawai }}',
                                            nama_pegawai: '{{ $pegawai->nama_pegawai }}',
                                            roles: @js($pegawai->roles->pluck('id'))
                                        }
                                    })">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <x-ui.smart-modal id="modal-assign-role" class="max-w-xl"
        @open-smart-modal.window="
        if ($event.detail.modalId !== 'modal-assign-role') return;

        mode     = $event.detail.mode ?? 'create'
        itemKey  = $event.detail.key ?? null
        formData = $event.detail.data ?? { id_pegawai: '', nama_pegawai: '', roles: [] }">

        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-3 dark:border-gray-700">
            <h4 class="text-2xl font-semibold text-gray-800 dark:text-white" x-text="mode === 'create' ? 'Tambah Role Pegawai' : 'Edit Role Pegawai'"></h4>
        </div>

        <!-- BODY -->
        <div class="flex-1 px-6 py-5 dark:bg-gray-900">
            <form :action="mode === 'edit'
                    ? `{{ url('role-pegawai') }}/${itemKey}`
                    : `{{ route('pegawai-role.store') }}`"
                method="POST" class="px-6 py-5 space-y-4 dark:bg-gray-900">
                @csrf

                <!-- METHOD SPOOFING SAAT EDIT -->
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="POST">
                </template>

                {{-- INPUTAN NAMA PEGAWAI DISINI NANTI --}}
                <div
                    x-data="pegawaiDropdown()"
                    @open-smart-modal.window="
                        if ($event.detail.modalId !== 'modal-assign-role') return;
                        initFromModal($event.detail);
                    "
                    class="space-y-2"
                >


                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Pegawai
                    </label>

                    <!-- Hidden ID Pegawai (WAJIB buat submit) -->
                    <input type="hidden" name="id_pegawai" x-model="selectedId">

                    <!-- Input Visible -->
                    <div class="relative">
                        <input
                            type="text"
                            x-model="search"
                            @click="mode === 'create' && (open = true)"
                            @input="mode === 'create' && (open = true)"
                            @keydown.escape="open = false"
                            :readonly="mode === 'edit'"
                            placeholder="Pilih pegawai..."
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                focus:border-brand-500 focus:ring-brand-500
                                dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200
                                dark:focus:border-brand-500">


                        <!-- Dropdown -->
                        <div
                            x-show="open && mode === 'create'"
                            x-transition
                            @click.outside="open = false"
                            class="absolute z-50 mt-1 max-h-56 w-full overflow-y-auto
                                rounded-lg border border-gray-200 bg-white shadow-lg
                                dark:border-gray-700 dark:bg-gray-800">
                            <template x-if="filteredPegawais.length === 0">
                                <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                    Pegawai tidak ditemukan
                                </div>
                            </template>

                            <template x-for="pegawai in filteredPegawais" :key="pegawai.id_pegawai">
                                <button
                                    type="button"
                                    @click="selectPegawai(pegawai)"
                                    class="flex w-full items-start px-4 py-2 text-left text-sm
                                        hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <div class="font-medium text-gray-800 dark:text-gray-200"
                                        x-text="pegawai.nama_pegawai"></div>
                                </button>
                            </template>

                        </div>
                    </div>
                </div>


                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Role Pegawai
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($roles as $role)
                            <label
                                class="flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2
                                hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                    :checked="formData.roles.includes({{ $role->id }})"
                                    class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                    text-brand-500 focus:ring-brand-500 dark:checked:bg-brand-500">
                                <span class="text-sm text-gray-800 dark:text-gray-300">
                                    {{ $role->nama_role }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <button type="button" @click="open=false"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                            px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300
                            hover:bg-gray-50 dark:hover:bg-gray-700">
                        Batal
                    </button>

                    <button type="submit"
                            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto dark:bg-brand-600 dark:hover:bg-brand-700">
                        <span x-text="mode === 'create' ? 'Simpan Role' : 'Update Role'"></span>
                    </button>
                </div>
            </form>
        </div>
    </x-ui.smart-modal>

    <script>
        function pegawaiDropdown() {
            return {
                open: false,
                search: '',
                selectedId: '',
                mode: 'create',

                pegawais: @js(
                    $pegawais->map(fn($p) => [
                        'id_pegawai'   => $p->id_pegawai,
                        'nama_pegawai' => $p->nama_pegawai
                    ])
                ),

                initFromModal(detail) {
                    this.mode = detail.mode ?? 'create';

                    if (this.mode === 'edit') {
                        this.selectedId = detail.data.id_pegawai;
                        this.search     = detail.data.nama_pegawai;
                        this.open       = false;
                    } else {
                        this.selectedId = '';
                        this.search     = '';
                        this.open       = true; // 🔥 BARU BISA DIJAMIN
                    }
                },

                get filteredPegawais() {
                    if (!this.search) return this.pegawais;

                    return this.pegawais.filter(p =>
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
@endsection

                {{-- <div
                    x-data="{
                        open: false,
                        search: '',
                        selectedId: '',
                        highlightedIndex: -1,
                        pegawais: @js($pegawais),

                        init() {
                            this.$watch('$root.formData.id_pegawai', value => {
                                if (value) {
                                    this.selectedId = value;
                                }
                            });

                            this.$watch('$root.formData.nama_pegawai', value => {
                                if (value) {
                                    this.search = value;
                                }
                            });
                        },

                        filtered() {
                            if (this.search.length === 0) return [];
                            return this.pegawais.filter(p =>
                                p.nama_pegawai.toLowerCase().includes(this.search.toLowerCase())
                            );
                        },

                        selectPegawai(p) {
                            this.search = p.nama_pegawai;
                            this.selectedId = p.id_pegawai;

                            this.$root.formData.nama_pegawai = p.nama_pegawai;
                            this.$root.formData.id_pegawai = p.id_pegawai;

                            this.open = false;
                            this.highlightedIndex = -1;
                        },

                        highlightNext() { if (this.highlightedIndex < this.filtered().length - 1) this.highlightedIndex++; },
                        highlightPrev() { if (this.highlightedIndex > 0) this.highlightedIndex--; },
                        selectHighlighted() { if (this.highlightedIndex >= 0) this.selectPegawai(this.filtered()[this.highlightedIndex]); }
                    }"
                >

                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Pegawai
                    </label>
                    <!-- Input search -->
                    <input
                        type="text"
                        x-model="formData.nama_pegawai"
                        class="h-11 w-full mb-4 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 px-4 py-2 text-sm"
                        placeholder="Ketik untuk cari nama"

                        @focus="open = true"

                        @input="
                            open = true;
                            if (mode === 'create') {
                                selectedId = '';
                            }
                        "

                        @keydown.arrow-down.prevent="highlightNext()"
                        @keydown.arrow-up.prevent="highlightPrev()"
                        @keydown.enter.prevent="selectHighlighted()"
                    />

                    <!-- Hidden input -->
                    <input type="hidden" name="id_pegawai" :value="selectedId" required>

                    <!-- Dropdown -->
                    <div x-show="open" x-transition
                        class="absolute z-50 mt-1 w-full mb-4 rounded-lg border border-gray-300 bg-white dark:border-gray-700 dark:bg-gray-800 max-h-60 overflow-y-auto">
                        <template
                            x-for="(pegawai, index) in pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase()))"
                            :key="pegawai.id_pegawai">
                            <div @click="search = pegawai.nama_pegawai; selectedId = pegawai.id_pegawai; open = false"
                                :class="{
                                    'bg-blue-100 dark:bg-blue-900/40': highlightedIndex === index,
                                    'hover:bg-gray-100 dark:hover:bg-gray-700': highlightedIndex !== index
                                }"
                                class="cursor-pointer px-4 py-2 text-sm dark:text-gray-300" x-text="pegawai.nama_pegawai"></div>
                        </template>
                        <template
                            x-if="pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase())).length === 0">
                            <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">Data tidak ditemukan</div>
                        </template>
                    </div>
                </div> --}}