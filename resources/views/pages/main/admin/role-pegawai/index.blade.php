@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="{{ $title }}" />

    <div class="mb-6 flex justify-end">
        <button class="gap-2 rounded-full border border-gray-300
            bg-white px-4 py-3 text-sm font-medium text-gray-700
            shadow-theme-xs hover:bg-gray-50 hover:text-gray-800"
                @click="$dispatch('open-smart-modal', {
                    modalId: 'modal-assign-role',
            })">
            Assign Role
        </button>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 w-16 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Nama Pegawai
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach ($pegawais as $i => $pegawai)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-center text-sm text-gray-700">
                                {{ $i + 1 }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $pegawai->nama_pegawai }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $pegawai->jabatan }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                @php
                                    $roleColors = [
                                        'Admin'     => 'bg-red-50 text-red-600 border-red-200',
                                        'Pimpinan'  => 'bg-purple-50 text-purple-600 border-purple-200',
                                        'Ketua Tim' => 'bg-blue-50 text-blue-600 border-blue-200',
                                        'Anggota Tim' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'Belum Ada' => 'bg-gray-50 text-gray-600 border-gray-200',
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <x-ui.smart-modal id="modal-assign-role" class="max-w-[700px]">
        <div class="border-b px-6 py-3">
            <h4 class="text-xl font-semibold">Assign Role Pegawai</h4>
        </div>

        <form action="{{ route('pegawai-role.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            {{-- Nama Seluruh Pegawai --}}
            <div x-data="{
                open: false,
                search: '',
                selectedId: '',
                highlightedIndex: -1,
                pegawais: @js($pegawais),

                init() {
                    // ketika modal edit dibuka
                    if (this.$root.formData?.nama_anggota) {
                        this.search = this.$root.formData.nama_anggota;
                        this.selectedId = this.$root.formData.id_anggota;
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
                    // sinkron ke formData (PENTING)
                    this.$root.formData.nama_anggota = p.nama_pegawai;
                    this.$root.formData.id_anggota = p.id_pegawai;

                    this.open = false;
                    this.highlightedIndex = -1;
                },

                highlightNext() { if (this.highlightedIndex < this.filtered().length - 1) this.highlightedIndex++; },
                highlightPrev() { if (this.highlightedIndex > 0) this.highlightedIndex--; },
                selectHighlighted() { if (this.highlightedIndex >= 0) this.selectPegawai(this.filtered()[this.highlightedIndex]); }
            }">
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    Nama Pegawai
                </label>
                <!-- Input search -->
                <input type="text" x-model="search" class="h-11 w-full mb-4 rounded-lg border px-4 py-2 text-sm"
                    placeholder="Ketik untuk cari nama" {{-- x-model="formData.nama_anggota" --}} @focus="open = true"
                    @input="open = true; selectedId = ''" {{-- @focus="open = !!search" @input="open = search.length > 0; selectedId = ''" --}}
                    @keydown.arrow-down.prevent="highlightedIndex++" @keydown.arrow-up.prevent="highlightedIndex--"
                    @keydown.enter.prevent="if(highlightedIndex>=0){ search = pegawais[highlightedIndex].nama_pegawai; selectedId = pegawais[highlightedIndex].id_pegawai; open=false; }">

                <!-- Hidden input -->
                <input type="hidden" name="pegawai_id" :value="selectedId" required>

                <!-- Dropdown -->
                <div x-show="open" x-transition
                    class="absolute z-50 mt-1 w-full mb-4 rounded-lg border bg-white max-h-60 overflow-y-auto">
                    <template
                        x-for="(pegawai, index) in pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase()))"
                        :key="pegawai.id_pegawai">
                        <div @click="search = pegawai.nama_pegawai; selectedId = pegawai.id_pegawai; open = false"
                            :class="{
                                'bg-blue-100': highlightedIndex ===
                                    index,
                                'hover:bg-gray-100': highlightedIndex !== index
                            }"
                            class="cursor-pointer px-4 py-2 text-sm" x-text="pegawai.nama_pegawai"></div>
                    </template>
                    <template
                        x-if="pegawais.filter(p => p.nama_pegawai.toLowerCase().includes(search.toLowerCase())).length === 0">
                        <div class="px-4 py-2 text-sm text-gray-500">Data tidak ditemukan</div>
                    </template>
                </div>
            </div>

            <!-- Roles -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">
                    Role Pegawai
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($roles as $role)
                        <label
                            class="flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-2
                                hover:bg-gray-50 cursor-pointer transition">
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->id }}"
                                class="h-4 w-4 rounded border-gray-300 text-brand-500
                                    focus:ring-brand-500">
                            <span class="text-sm text-gray-800">
                                {{ $role->nama_role }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t pt-4">
                <button
                    type="button"
                    @click="open=false"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2
                        text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-brand-500 px-4 py-2
                        text-sm font-medium text-white hover:bg-brand-600">
                    Simpan
                </button>
            </div>
        </form>
    </x-ui.smart-modal>
@endsection
