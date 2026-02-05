@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    <div class="mb-6 flex justify-end">
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
    </div>

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

    <x-ui.smart-modal id="modal-assign-role" class="max-w-xl" x-data="{
        open: false, // ⬅️ INI YANG HILANG
        mode: 'create',
        itemKey: null,
        formData: {
            id_pegawai: '',
            nama_pegawai: '',
            roles: []
        }
    }">


        <!-- HEADER -->
        <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h4 class="text-xl font-semibold text-gray-800 dark:text-white"
                x-text="mode === 'create' ? 'Tambah Role Pegawai' : 'Edit Role Pegawai'"></h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                x-text="mode === 'create' ? 'Atur role baru untuk pegawai' : 'Edit role yang sudah ada'"></p>
        </div>

        <!-- BODY -->
        <div class="flex-1 overflow-y-auto max-h-[calc(100vh-200px)]">
            <form
                :action="mode === 'edit'
                    ?
                    `{{ url('role-pegawai') }}/${itemKey}` :
                    `{{ route('pegawai-role.store') }}`"
                method="POST" class="px-6 py-5 space-y-6">
                @csrf

                <!-- METHOD SPOOFING SAAT EDIT -->
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="POST">
                </template>

                {{-- Nama Seluruh Pegawai --}}
                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: '',
                    highlightedIndex: -1,
                    pegawais: @js($pegawais),
                
                    init() {
                        this.syncFormData();
                
                        this.$watch('$parent.formData', () => {
                            this.syncFormData();
                        });
                    },
                
                    syncFormData() {
                        if (this.$parent.formData?.nama_pegawai) {
                            this.search = this.$parent.formData.nama_pegawai;
                            this.selectedId = this.$parent.formData.id_pegawai;
                        } else {
                            this.search = '';
                            this.selectedId = '';
                            this.open = false;
                        }
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
                
                        // sinkron ke parent (INI PENTING)
                        this.$parent.formData.nama_pegawai = p.nama_pegawai;
                        this.$parent.formData.id_pegawai = p.id_pegawai;
                
                        this.open = false;
                        this.highlightedIndex = -1;
                    },
                
                    highlightNext() { if (this.highlightedIndex < this.filtered().length - 1) this.highlightedIndex++; },
                    highlightPrev() { if (this.highlightedIndex > 0) this.highlightedIndex--; },
                    selectHighlighted() { if (this.highlightedIndex >= 0) this.selectPegawai(this.filtered()[this.highlightedIndex]); }
                }" class="relative">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nama Pegawai
                        <span class="text-red-500">*</span>
                    </label>

                    <!-- Input search -->
                    <input type="text" x-model="formData.nama_pegawai"
                        class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors"
                        placeholder="Ketik untuk mencari nama pegawai..." @focus="open = true"
                        @input="open = true; selectedId = ''" @keydown.arrow-down.prevent="highlightNext()"
                        @keydown.arrow-up.prevent="highlightPrev()" @keydown.enter.prevent="selectHighlighted()"
                        @keydown.escape.window="open = false">

                    <!-- Hidden input -->
                    <input type="hidden" name="id_pegawai" :value="selectedId" required>


                    <!-- Dropdown -->
                    <div x-show="open && search.length > 0" x-transition
                        class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 shadow-lg max-h-60 overflow-y-auto">
                        <template x-for="(pegawai, index) in filtered()" :key="pegawai.id_pegawai">
                            <div @click="selectPegawai(pegawai)"
                                :class="{
                                    'bg-blue-50 dark:bg-blue-900/30': highlightedIndex === index,
                                    'hover:bg-gray-50 dark:hover:bg-gray-700': highlightedIndex !== index
                                }"
                                class="cursor-pointer px-4 py-3 text-sm text-gray-800 dark:text-gray-300 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                                x-text="pegawai.nama_pegawai"></div>
                        </template>
                        <template x-if="filtered().length === 0">
                            <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 italic">Tidak ada hasil yang
                                cocok</div>
                        </template>
                    </div>

                    <!-- Selected indicator -->
                    <div x-show="selectedId" class="mt-2">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 text-xs">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span x-text="'Dipilih: ' + search"></span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Role Pegawai
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($roles as $role)
                                <label
                                    class="flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3.5
                                hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-all duration-200">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        :checked="formData.roles && formData.roles.includes({{ $role->id }})"
                                        class="h-5 w-5 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                                    text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 dark:checked:bg-blue-600">
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-300">
                                        {{ $role->nama_role }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-5">
                    <button type="button" @click="open=false"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                        px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300
                        hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Batal
                    </button>

                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 transition-colors">
                        <span x-text="mode === 'create' ? 'Simpan Role' : 'Update Role'"></span>
                    </button>
                </div>
            </form>
        </div>
    </x-ui.smart-modal>
@endsection
