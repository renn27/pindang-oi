@extends('layouts.dashboard')

@section('content')
<x-common.page-breadcrumb pageTitle="{{$title}}" />

    <!-- Top Action Bar -->
    <div class="flex flex-row items-center justify-between rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 mb-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Kelola menu eksternal dan grup tautan di bagian bawah sidebar (Informasi). Semua link otomatis terbuka di tab baru.
            </p>
        </div>

        <button class="gap-2 rounded-full border border-gray-300
            bg-white px-4 py-3 text-sm font-medium text-gray-700
            shadow-theme-xs hover:bg-gray-50 hover:text-gray-800
            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                @click="$dispatch('open-smart-modal', {
                    modalId: 'modal-sidebar-links',
                    mode: 'create'
            })">
            Tambah Link Sidebar
        </button>
    </div>

    <!-- Modal Form CRUD -->
    <x-ui.smart-modal id="modal-sidebar-links" class="max-w-2xl"
            @open-smart-modal.window="
            if ($event.detail.modalId !== 'modal-sidebar-links') return;

            mode    = $event.detail.mode ?? 'create';
            itemKey  = $event.detail.key ?? null;
            formData = $event.detail.data ?? {
                name: '',
                type: 'direct',
                parent_id: '',
                url: '',
                icon: '',
                color: '',
                background_color: '',
                sort_order: '',
                is_special: false,
                children: []
            };
            if (!formData.children) {
                formData.children = [];
            }
            ">
        <form :action="mode === 'edit'
                ? `{{ url('sidebar-links') }}/${itemKey}`
                : `{{ route('sidebar-links.store') }}`"
            method="POST" class="flex flex-col h-[80vh] md:h-[85vh] overflow-hidden">
            @csrf
            <template x-if="mode === 'edit'">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <!-- HEADER -->
            <div class="shrink-0 border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white" x-text="mode === 'create' ? 'Tambah Link Sidebar' : 'Edit Link Sidebar'"></h4>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="mode === 'create' ? 'Masukkan link sidebar baru' : 'Edit link sidebar yang sudah ada'"></p>
            </div>

            <!-- BODY -->
            <div class="flex-1 overflow-y-auto px-6 py-5 custom-scrollbar dark:bg-gray-900 space-y-5">
                <!-- Tipe Link -->
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Jenis Link <span class="text-red-500">*</span>
                    </label>
                    <select x-model="formData.type" name="type" required
                        class="md:w-3/4 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <option value="direct">Link Langsung (Direct Link)</option>
                        <option value="group">Grup Dropdown (Parent Link)</option>
                        <option value="sub">Sub-link dari Grup (Child Link)</option>
                    </select>
                </div>

                <!-- Pilihan Parent (Khusus untuk Sub-link) -->
                <div x-show="formData.type === 'sub'" x-transition
                    class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Pilih Grup Utama <span class="text-red-500">*</span>
                    </label>
                    <select x-model="formData.parent_id" name="parent_id" :required="formData.type === 'sub'"
                        class="md:w-3/4 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <option value="">-- Pilih Grup Utama --</option>
                        @foreach ($parentLinks as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Nama Link -->
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Nama Tampilan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="formData.name" name="name" required
                        placeholder="Masukkan nama tampilan link"
                        class="md:w-3/4 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                <!-- URL Target (Sembunyikan jika bertipe group dropdown) -->
                <div x-show="formData.type !== 'group'" x-transition
                    class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        URL Target <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="formData.url" name="url" :required="formData.type !== 'group'"
                        placeholder="Contoh: https://besti.bpsoganilir.com/ atau /pengumuman"
                        class="md:w-3/4 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                <!-- Icon SVG Code / Key -->
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Key Icon (Opsional)
                    </label>
                    <input type="text" x-model="formData.icon" name="icon"
                        placeholder="Contoh: besti, drive, sinergi, dll"
                        class="md:w-3/4 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                <!-- Urutan / Sort Order -->
                <div class="flex flex-col gap-2 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Urutan Tampil
                    </label>
                    <input type="number" x-model="formData.sort_order" name="sort_order" required
                        placeholder="Misal: 1, 2, 3"
                        class="md:w-3/4 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                </div>

                <!-- Kustomisasi Warna & Background -->
                <div class="flex flex-col gap-4 md:flex-row md:items-center">
                    <label class="block text-sm font-medium text-gray-700 md:w-1/4 dark:text-gray-300">
                        Warna Kustom
                    </label>
                    <div class="md:w-3/4 grid grid-cols-2 gap-4 w-full">
                        <!-- Warna Teks -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-gray-500 dark:text-gray-400 font-medium">Warna Teks</label>
                            <div class="flex gap-2 items-center">
                                <input type="color" x-model="formData.color"
                                    class="w-11 h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent p-0 cursor-pointer" />
                                <input type="text" x-model="formData.color" name="color"
                                    placeholder="HEX (cth: #ea580c)"
                                    class="h-11 flex-1 min-w-0 rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                            </div>
                        </div>

                        <!-- Warna Background -->
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs text-gray-500 dark:text-gray-400 font-medium">Warna Background</label>
                            <div class="flex gap-2 items-center">
                                <input type="color" x-model="formData.background_color"
                                    class="w-11 h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent p-0 cursor-pointer" />
                                <input type="text" x-model="formData.background_color" name="background_color"
                                    placeholder="HEX (cth: #fef08a)"
                                    class="h-11 flex-1 min-w-0 rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:placeholder:text-gray-500" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toggles / Checkboxes (Styling) -->
                <div class="grid grid-cols-1 gap-4 mt-2">
                    <!-- is_special (Hanya untuk link utama/direct/group) -->
                    <div x-show="formData.type !== 'sub'" x-transition class="flex flex-col gap-1 bg-amber-50 border border-amber-200/50 p-4 rounded-xl dark:bg-amber-950/20 dark:border-amber-900/30">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" x-model="formData.is_special" name="is_special" value="1" id="is_special"
                                class="h-4.5 w-4.5 rounded-sm border-gray-300 text-brand-500 focus:ring-brand-500/10">
                            <label for="is_special" class="text-sm font-semibold text-gray-800 dark:text-white">
                                Style Khusus (Highlight Menu)
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 pl-7.5 dark:text-gray-400 mt-1">
                            Menceklis style highlight akan membuat link menjadi eye catching seperti link pengumuman.
                        </p>
                    </div>
                </div>

                <!-- Good UX - Inline Children Builder (Khusus Create Mode Tipe Grup Dropdown) -->
                <div x-show="formData.type === 'group' && mode === 'create'" x-transition class="border-t border-gray-200 pt-4 mt-4 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-sm font-semibold text-gray-800 dark:text-white">Daftar Sub-link (Child Links)</h5>
                        <button type="button" @click="(formData.children = formData.children || []).push({ name: '', url: '', icon: '', sort_order: (formData.children || []).length + 1, color: '', background_color: '' })"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-brand-300 bg-white text-xs font-medium text-brand-700 shadow-theme-xs hover:bg-brand-50 dark:border-gray-700 dark:bg-gray-800 dark:text-brand-400 dark:hover:bg-gray-700">
                            + Tambah Sub-link
                        </button>
                    </div>

                    <div class="space-y-3 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                        <template x-for="(child, index) in (formData.children || [])" :key="index">
                            <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700 relative">
                                <!-- Delete Button -->
                                <button type="button" @click="formData.children.splice(index, 1)"
                                    class="absolute top-2 right-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                                    <!-- Child Name -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Nama Tampilan <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="child.name" :name="'children['+index+'][name]'" required
                                            placeholder="Nama Sub-link"
                                            class="h-9 w-full rounded-md border border-gray-300 bg-transparent px-3 py-1 text-xs text-gray-800 focus:border-brand-300 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                                    </div>

                                    <!-- Child URL -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">URL Target <span class="text-red-500">*</span></label>
                                        <input type="text" x-model="child.url" :name="'children['+index+'][url]'" required
                                            placeholder="https://... atau /path"
                                            class="h-9 w-full rounded-md border border-gray-300 bg-transparent px-3 py-1 text-xs text-gray-800 focus:border-brand-300 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                                    </div>

                                    <!-- Child Icon -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Icon Key (Opsional)</label>
                                        <input type="text" x-model="child.icon" :name="'children['+index+'][icon]'"
                                            placeholder="Contoh: drive, sinergi"
                                            class="h-9 w-full rounded-md border border-gray-300 bg-transparent px-3 py-1 text-xs text-gray-800 focus:border-brand-300 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                                    </div>

                                    <!-- Child Urutan -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Urutan Tampil <span class="text-red-500">*</span></label>
                                        <input type="number" x-model="child.sort_order" :name="'children['+index+'][sort_order]'" required
                                            placeholder="Urutan"
                                            class="h-9 w-full rounded-md border border-gray-300 bg-transparent px-3 py-1 text-xs text-gray-800 focus:border-brand-300 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                                    </div>

                                    <!-- Child Color -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Warna Teks</label>
                                        <div class="flex gap-1.5 items-center">
                                            <input type="color" x-model="child.color" class="w-8 h-8 rounded border border-gray-300 dark:border-gray-700 bg-transparent p-0 cursor-pointer" />
                                            <input type="text" x-model="child.color" :name="'children['+index+'][color]'" placeholder="#000000"
                                                class="h-8 flex-1 min-w-0 rounded-md border border-gray-300 bg-transparent px-2 py-1 text-xs text-gray-800 focus:border-brand-300 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                                        </div>
                                    </div>

                                    <!-- Child Background Color -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Warna Background</label>
                                        <div class="flex gap-1.5 items-center">
                                            <input type="color" x-model="child.background_color" class="w-8 h-8 rounded border border-gray-300 dark:border-gray-700 bg-transparent p-0 cursor-pointer" />
                                            <input type="text" x-model="child.background_color" :name="'children['+index+'][background_color]'" placeholder="#ffffff"
                                                class="h-8 flex-1 min-w-0 rounded-md border border-gray-300 bg-transparent px-2 py-1 text-xs text-gray-800 focus:border-brand-300 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="!formData.children || formData.children.length === 0">
                            <p class="text-xs text-gray-400 text-center py-4 border border-dashed border-gray-300 rounded-lg dark:border-gray-700 dark:text-gray-500">
                                Belum ada sub-link yang diinputkan.
                            </p>
                        </template>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="shrink-0 border-t border-gray-200 px-6 py-4 bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                    <button @click="open = false" type="button"
                            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto dark:bg-brand-600 dark:hover:bg-brand-700">
                        <span x-text="mode === 'create' ? 'Simpan Data' : 'Update Data'"></span>
                    </button>
                </div>
            </div>
        </form>
    </x-ui.smart-modal>

    <!-- Main Table Listing -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24 dark:text-gray-400">Urutan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Nama Link / Grup</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">URL Target</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24 dark:text-gray-400">Icon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32 dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                @forelse ($links as $link)
                    <!-- Parent Row / Direct Link -->
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 {{ $link->children->isNotEmpty() ? 'bg-gray-50/50 dark:bg-gray-800/40 font-semibold' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $link->sort_order }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    @if ($link->children->isNotEmpty())
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                    <span>{{ $link->name }}</span>
                                    @if (!$link->url && $link->children->isNotEmpty())
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-950/30 dark:text-blue-400 dark:border-blue-800">Grup</span>
                                    @endif
                                    @if ($link->is_special)
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-amber-50 text-amber-600 border border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-800">Special</span>
                                    @endif
                                </div>
                                <!-- Visual Indicator Warna/Background -->
                                @if ($link->color || $link->background_color)
                                    <div class="flex gap-2 text-[10px] text-gray-500 items-center mt-1 font-mono">
                                        @if ($link->color)
                                            <span class="flex items-center gap-1">
                                                <span class="inline-block w-2.5 h-2.5 rounded-full border border-gray-300 dark:border-gray-600" style="background-color: {{ $link->color }}"></span>
                                                Teks: {{ $link->color }}
                                            </span>
                                        @endif
                                        @if ($link->background_color)
                                            <span class="flex items-center gap-1">
                                                <span class="inline-block w-2.5 h-2.5 rounded-full border border-gray-300 dark:border-gray-600" style="background-color: {{ $link->background_color }}"></span>
                                                Bg: {{ $link->background_color }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                            {{ $link->url ?? '— (Grup Dropdown)' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">
                                {{ $link->icon ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <div class="relative inline-block group">
                                <button class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:border-green-400 hover:text-green-600 transition-all duration-200 shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:border-green-500 dark:hover:text-green-400">
                                    Aksi
                                </button>
                                <div class="absolute right-5 top-0 w-36 origin-top-right rounded-md bg-white border border-gray-200 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50 dark:bg-gray-800 dark:border-gray-700">
                                    <div class="py-1">
                                        <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 dark:text-gray-300 dark:hover:bg-gray-700"
                                            @click="$dispatch('open-smart-modal', {
                                                modalId: 'modal-sidebar-links',
                                                mode: 'edit',
                                                key: '{{ $link->id }}',
                                                data: {
                                                    name: '{{ $link->name }}',
                                                    type: '{{ $link->url ? 'direct' : 'group' }}',
                                                    url: '{{ $link->url ?? '' }}',
                                                    icon: '{{ $link->icon ?? '' }}',
                                                    color: '{{ $link->color ?? '' }}',
                                                    background_color: '{{ $link->background_color ?? '' }}',
                                                    sort_order: '{{ $link->sort_order }}',
                                                    is_special: {{ $link->is_special ? 'true' : 'false' }}
                                                }
                                            })">
                                            Edit
                                        </button>
                                        <form id="delete-link-{{ $link->id }}" action="{{ route('sidebar-links.delete', $link->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="SwalHelper.confirmDelete('delete-link-{{ $link->id }}', {{ json_encode($link->name) }})"
                                                class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2 dark:text-red-400 dark:hover:bg-gray-700">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Children / Sub-links Rows -->
                    @foreach ($link->children as $child)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-3 whitespace-nowrap text-xs text-gray-500 pl-10 dark:text-gray-400">
                                └─ {{ $child->sort_order }}
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-900 pl-12 dark:text-gray-300">
                                <div class="flex flex-col gap-0.5">
                                    <span>{{ $child->name }}</span>
                                    <!-- Visual Indicator Warna/Background Anak -->
                                    @if ($child->color || $child->background_color)
                                        <div class="flex gap-2 text-[9px] text-gray-400 items-center mt-0.5 font-mono">
                                            @if ($child->color)
                                                <span class="flex items-center gap-0.5">
                                                    <span class="inline-block w-2 h-2 rounded-full border border-gray-300 dark:border-gray-600" style="background-color: {{ $child->color }}"></span>
                                                    Teks: {{ $child->color }}
                                                </span>
                                            @endif
                                            @if ($child->background_color)
                                                <span class="flex items-center gap-0.5">
                                                    <span class="inline-block w-2 h-2 rounded-full border border-gray-300 dark:border-gray-600" style="background-color: {{ $child->background_color }}"></span>
                                                    Bg: {{ $child->background_color }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-450 truncate max-w-xs pl-8">
                                {{ $child->url }}
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-xs text-gray-400">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-gray-50 dark:bg-gray-800 font-mono">
                                    {{ $child->icon ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-xs text-center">
                                <div class="relative inline-block group">
                                    <button class="inline-flex items-center gap-1.5 rounded bg-white border border-gray-200 px-2 py-1 text-[11px] font-medium text-gray-600 hover:border-green-400 hover:text-green-600 dark:bg-gray-850 dark:border-gray-700 dark:text-gray-300">
                                        Aksi
                                    </button>
                                    <div class="absolute right-5 top-0 w-36 origin-top-right rounded-md bg-white border border-gray-200 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50 dark:bg-gray-800 dark:border-gray-700">
                                        <div class="py-1">
                                            <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2 dark:text-gray-300 dark:hover:bg-gray-700"
                                                @click="$dispatch('open-smart-modal', {
                                                    modalId: 'modal-sidebar-links',
                                                    mode: 'edit',
                                                    key: '{{ $child->id }}',
                                                    data: {
                                                        name: '{{ $child->name }}',
                                                        type: 'sub',
                                                        parent_id: '{{ $child->parent_id }}',
                                                        url: '{{ $child->url }}',
                                                        icon: '{{ $child->icon ?? '' }}',
                                                        color: '{{ $child->color ?? '' }}',
                                                        background_color: '{{ $child->background_color ?? '' }}',
                                                        sort_order: '{{ $child->sort_order }}',
                                                        is_special: false
                                                    }
                                                })">
                                                Edit
                                            </button>
                                            <form id="delete-link-{{ $child->id }}" action="{{ route('sidebar-links.delete', $child->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    onclick="SwalHelper.confirmDelete('delete-link-{{ $child->id }}', {{ json_encode($child->name) }})"
                                                    class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2 dark:text-red-400 dark:hover:bg-gray-700">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            Belum ada link sidebar yang ditambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
