@extends('layouts.dashboard')

@section('content')
    <x-common.page-breadcrumb pageTitle="Panduan Developer" />

    {{-- ===== HEADER PENGANTAR TEKNIS ===== --}}
    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xs mb-6 dark:border-gray-800 dark:bg-gray-900">
        <div>
            <div>
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <div class="mb-1.5">
                            <span class="inline-flex items-center gap-2 rounded-lg border border-indigo-100 bg-indigo-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-indigo-700 dark:border-indigo-500/20 dark:bg-indigo-500/10 dark:text-indigo-300">
                                Arsitektur &amp; Logika Backend
                             </span>
                        </div>
                        <h1 class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-white md:text-3xl">
                            Pusat Dokumentasi &amp; Panduan Developer
                        </h1>
                    </div>
                    <div class="shrink-0 flex items-center">
                        <img class="dark:hidden h-11 w-auto" src="/images/logo/logo.svg" alt="Pindang OI Logo" />
                        <img class="hidden dark:block h-11 w-auto" src="/images/logo/logo-dark.svg" alt="Pindang OI Logo" />
                    </div>
                </div>
                <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                    Halaman rahasia ini memuat panduan struktur kode, referensi database/model, serta logika validasi backend (business logic) untuk memudahkan pengembangan dan maintenance aplikasi Pindang OI.
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1 rounded-lg border border-indigo-100 bg-indigo-50/50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:border-indigo-500/20 dark:bg-indigo-500/10 dark:text-indigo-300">
                        <code class="text-[10px] font-mono">Laravel 11</code>
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-100 bg-emerald-50/50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                        <code class="text-[10px] font-mono">Alpine.js</code>
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-lg border border-purple-100 bg-purple-50/50 px-2.5 py-1 text-xs font-semibold text-purple-700 dark:border-purple-500/20 dark:bg-purple-500/10 dark:text-purple-300">
                        <code class="text-[10px] font-mono">Tailwind CSS</code>
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== DOKUMENTASI INTERAKTIF (ALPINE.JS) ===== --}}
    @if(count($availableTabs) > 0)
        <div x-show="isAuthorized" x-cloak x-data="{
            isAuthorized: false,
            activeTab: '{{ $availableTabs[0] }}',
            activeSlug: '',
            guides: @js($groupedPanduans),
            get currentGuides() {
                return this.guides[this.activeTab] || [];
            },
            get activeGuide() {
                return this.currentGuides.find(g => g.slug === this.activeSlug) || this.currentGuides[0] || null;
            },
            selectTab(tab) {
                this.activeTab = tab;
                const guides = this.currentGuides;
                this.activeSlug = guides.length > 0 ? guides[0].slug : '';
            },
            selectGuide(slug) {
                this.activeSlug = slug;
            },
            copyCode(codeText, label) {
                if (!codeText) return;
                navigator.clipboard.writeText(codeText).then(() => {
                    if (window.SwalHelper) {
                        window.SwalHelper.success(label + ' berhasil disalin ke clipboard!');
                    } else {
                        alert(label + ' berhasil disalin!');
                    }
                }).catch(err => {
                    console.error('Failed to copy text: ', err);
                });
            },
            authenticateDeveloper() {
                if (sessionStorage.getItem('dev_authorized') === 'true') {
                    this.isAuthorized = true;
                    return;
                }
                const self = this;
                setTimeout(() => {
                    if (typeof Swal === 'undefined') {
                        const userVal = prompt('Masukkan Username Developer:');
                        const passVal = prompt('Masukkan Password Developer:');
                        if (userVal === 'tukangPindang' && passVal === 'dahmasakbelum?') {
                            sessionStorage.setItem('dev_authorized', 'true');
                            self.isAuthorized = true;
                        } else {
                            alert('Kredensial salah!');
                            window.location.href = '/';
                        }
                        return;
                    }
                    Swal.fire({
                        title: 'Autentikasi Developer',
                        html: `
                            <div class='p-3 text-left'>
                                <p class='text-xs text-gray-500 dark:text-gray-400 mb-4 leading-relaxed'>Halaman ini memuat dokumen arsitektur dan kode backend rahasia. Masukkan kredensial developer untuk mengakses.</p>
                                <div class='mb-4'>
                                    <label class='block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5'>Username</label>
                                    <input id='swal-username' type='text' class='w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:outline-none focus:border-indigo-500 font-medium' placeholder='Username'>
                                </div>
                                <div>
                                    <label class='block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1.5'>Password</label>
                                    <input id='swal-password' type='password' class='w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:outline-none focus:border-indigo-500 font-medium' placeholder='Password'>
                                </div>
                            </div>
                        `,
                        focusConfirm: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showCancelButton: true,
                        cancelButtonText: 'Batal',
                        confirmButtonText: 'Masuk',
                        customClass: {
                            popup: '!rounded-2xl !border !border-gray-200 !shadow-2xl !bg-white dark:!border-gray-850 dark:!bg-gray-900',
                            confirmButton: 'px-5 py-2.5 rounded-xl font-bold bg-indigo-600 hover:bg-indigo-700 text-white text-xs transition-colors',
                            cancelButton: 'px-5 py-2.5 rounded-xl font-bold bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-300 text-xs transition-colors'
                        },
                        preConfirm: () => {
                            const rawSwal = (window.SwalHelper && window.SwalHelper.raw) ? window.SwalHelper.raw : null;
                            const popup = rawSwal ? rawSwal.getPopup() : document;
                            const username = popup.querySelector('#swal-username').value;
                            const password = popup.querySelector('#swal-password').value;
                            if (!username || !password) {
                                if (rawSwal) {
                                    rawSwal.showValidationMessage('Username dan password wajib diisi');
                                } else {
                                    alert('Username dan password wajib diisi');
                                }
                                return false;
                            }
                            if (username !== 'tukangPindang' || password !== 'dahmasakbelum?') {
                                if (rawSwal) {
                                    rawSwal.showValidationMessage('Kredensial developer salah!');
                                } else {
                                    alert('Kredensial developer salah!');
                                }
                                return false;
                            }
                            return { username: username, password: password };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            sessionStorage.setItem('dev_authorized', 'true');
                            self.isAuthorized = true;
                        } else {
                            window.location.href = '/';
                        }
                    });
                }, 100);
            },
            init() {
                this.authenticateDeveloper();
                this.selectTab(this.activeTab);
                
                this.$watch('activeSlug', () => {
                    this.$nextTick(() => {
                        if (window.Prism) {
                            window.Prism.highlightAll();
                        }
                    });
                });
            }
        }" class="space-y-6">
            
            {{-- Tabs Selector --}}
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-800 pb-4">
                @foreach ($availableTabs as $tab)
                    <button
                        type="button"
                        @click="selectTab('{{ $tab }}')"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 cursor-pointer border shadow-xs"
                        :class="activeTab === '{{ $tab }}'
                            ? 'bg-indigo-600 border-indigo-600 text-white dark:bg-indigo-600 dark:border-indigo-600 shadow-md shadow-indigo-500/20'
                            : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-800 dark:bg-gray-900 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200'"
                    >
                        {{ $tab }}
                    </button>
                @endforeach
            </div>

            {{-- Split Layout Sidebar + Content --}}
            <div class="flex flex-col lg:flex-row gap-6">
                
                {{-- Sidebar Kiri --}}
                <div class="w-full lg:w-72 shrink-0">
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 shadow-xs max-h-[500px] overflow-y-auto">
                        <div class="mb-3 px-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Daftar Modul Teknis</span>
                        </div>
                        <nav class="space-y-1">
                            <template x-for="guide in currentGuides" :key="guide.slug">
                                <button
                                    @click="selectGuide(guide.slug)"
                                    class="w-full text-left px-3 py-2 rounded-xl text-xs transition-all duration-200 flex items-center justify-between group border"
                                    :class="activeSlug === guide.slug
                                        ? 'bg-indigo-50/80 border-indigo-100 text-indigo-700 dark:bg-indigo-950/20 dark:border-indigo-900/40 dark:text-indigo-400 font-bold'
                                        : 'bg-transparent border-transparent text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200'"
                                >
                                    <span x-text="guide.menu_name"></span>
                                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </template>
                        </nav>
                    </div>
                </div>

                {{-- Detail Konten Kanan --}}
                <div class="flex-1 min-w-0">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-xs min-h-[400px]">
                        <template x-if="activeGuide">
                            <div class="space-y-6">
                                {{-- Judul --}}
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white" x-text="activeGuide.title"></h2>
                                        
                                        {{-- Badges Peran --}}
                                        <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tingkat Akses:</span>
                                            <template x-for="role in activeGuide.roles_allowed" :key="role">
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 dark:bg-indigo-950/30 dark:text-indigo-300 dark:border-indigo-900/30" x-text="role"></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- Penjelasan --}}
                                <div class="space-y-2">
                                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Penjelasan Fungsional</h3>
                                    <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400" x-html="activeGuide.explanation"></p>
                                </div>

                                {{-- Lokasi Berkas & Komponen Teknis --}}
                                <div class="space-y-4">
                                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Lokasi Berkas &amp; Komponen Teknis</h3>
                                    
                                    <div class="space-y-4">
                                        {{-- Migration Section --}}
                                        <template x-if="activeGuide.migration_path">
                                            <div class="p-4 bg-gray-950 text-gray-200 rounded-xl border border-gray-800 dark:border-gray-800/80 space-y-3">
                                                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-gray-800/60">
                                                    <div class="flex flex-wrap items-center gap-2 min-w-0">
                                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 tracking-wider">
                                                            Migration
                                                        </span>
                                                        <span class="text-[11px] text-gray-400 font-mono select-all break-all" x-text="activeGuide.migration_path"></span>
                                                    </div>
                                                    <template x-if="activeGuide.migration_code">
                                                        <button 
                                                            @click="copyCode(activeGuide.migration_code, 'Migration')"
                                                            class="flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold text-gray-400 hover:text-white bg-gray-900 hover:bg-gray-800 rounded-md border border-gray-800 hover:border-gray-700 transition duration-150 cursor-pointer"
                                                            title="Copy code"
                                                        >
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 002 2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                                            </svg>
                                                            <span>Copy</span>
                                                        </button>
                                                    </template>
                                                </div>
                                                <template x-if="activeGuide.migration_code">
                                                    <div class="relative mt-2">
                                                        <pre class="bg-transparent text-gray-100 overflow-auto text-xs max-h-[300px] font-mono scrollbar-thin"><code class="language-php" x-text="activeGuide.migration_code"></code></pre>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- Model Section --}}
                                        <template x-if="activeGuide.model_path">
                                            <div class="p-4 bg-gray-950 text-gray-200 rounded-xl border border-gray-800 dark:border-gray-800/80 space-y-3">
                                                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-gray-800/60">
                                                    <div class="flex flex-wrap items-center gap-2 min-w-0">
                                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 tracking-wider">
                                                            Model
                                                        </span>
                                                        <span class="text-[11px] text-gray-400 font-mono select-all break-all" x-text="activeGuide.model_path"></span>
                                                    </div>
                                                    <template x-if="activeGuide.model_code">
                                                        <button 
                                                            @click="copyCode(activeGuide.model_code, 'Model')"
                                                            class="flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold text-gray-400 hover:text-white bg-gray-900 hover:bg-gray-800 rounded-md border border-gray-800 hover:border-gray-700 transition duration-150 cursor-pointer"
                                                            title="Copy code"
                                                        >
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 002 2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                                            </svg>
                                                            <span>Copy</span>
                                                        </button>
                                                    </template>
                                                </div>
                                                <template x-if="activeGuide.model_code">
                                                    <div class="relative mt-2">
                                                        <pre class="bg-transparent text-gray-100 overflow-auto text-xs max-h-[300px] font-mono scrollbar-thin"><code class="language-php" x-text="activeGuide.model_code"></code></pre>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- Controller Section --}}
                                        <template x-if="activeGuide.controller_path">
                                            <div class="p-4 bg-gray-950 text-gray-200 rounded-xl border border-gray-800 dark:border-gray-800/80 space-y-3">
                                                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-gray-800/60">
                                                    <div class="flex flex-wrap items-center gap-2 min-w-0">
                                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 tracking-wider">
                                                            Controller
                                                        </span>
                                                        <span class="text-[11px] text-gray-400 font-mono select-all break-all" x-text="activeGuide.controller_path"></span>
                                                    </div>
                                                    <template x-if="activeGuide.controller_code">
                                                        <button 
                                                            @click="copyCode(activeGuide.controller_code, 'Controller')"
                                                            class="flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold text-gray-400 hover:text-white bg-gray-900 hover:bg-gray-800 rounded-md border border-gray-800 hover:border-gray-700 transition duration-150 cursor-pointer"
                                                            title="Copy code"
                                                        >
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 002 2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                                            </svg>
                                                            <span>Copy</span>
                                                        </button>
                                                    </template>
                                                </div>
                                                <template x-if="activeGuide.controller_code">
                                                    <div class="relative mt-2">
                                                        <pre class="bg-transparent text-gray-100 overflow-auto text-xs max-h-[300px] font-mono scrollbar-thin"><code class="language-php" x-text="activeGuide.controller_code"></code></pre>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- Policy Section --}}
                                        <template x-if="activeGuide.policy_path">
                                            <div class="p-4 bg-gray-950 text-gray-200 rounded-xl border border-gray-800 dark:border-gray-800/80 space-y-3">
                                                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-gray-800/60">
                                                    <div class="flex flex-wrap items-center gap-2 min-w-0">
                                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 tracking-wider">
                                                            Policy
                                                        </span>
                                                        <span class="text-[11px] text-gray-400 font-mono select-all break-all" x-text="activeGuide.policy_path"></span>
                                                    </div>
                                                    <template x-if="activeGuide.policy_code">
                                                        <button 
                                                            @click="copyCode(activeGuide.policy_code, 'Policy')"
                                                            class="flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold text-gray-400 hover:text-white bg-gray-900 hover:bg-gray-800 rounded-md border border-gray-800 hover:border-gray-700 transition duration-150 cursor-pointer"
                                                            title="Copy code"
                                                        >
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 002 2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                                            </svg>
                                                            <span>Copy</span>
                                                        </button>
                                                    </template>
                                                </div>
                                                <template x-if="activeGuide.policy_code">
                                                    <div class="relative mt-2">
                                                        <pre class="bg-transparent text-gray-100 overflow-auto text-xs max-h-[300px] font-mono scrollbar-thin"><code class="language-php" x-text="activeGuide.policy_code"></code></pre>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Other Components Grid --}}
                                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mt-4">
                                        <template x-if="activeGuide.view_path">
                                            <div class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-gray-150 dark:border-gray-800">
                                                <span class="text-[10px] font-extrabold uppercase text-gray-400 dark:text-gray-500 tracking-wider">View / Template</span>
                                                <div class="font-mono text-xs text-gray-800 dark:text-gray-200 mt-1 select-all break-all" x-text="activeGuide.view_path"></div>
                                            </div>
                                        </template>
                                        <template x-if="activeGuide.route_definition">
                                            <div class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-gray-150 dark:border-gray-800">
                                                <span class="text-[10px] font-extrabold uppercase text-gray-400 dark:text-gray-500 tracking-wider">Route Definition</span>
                                                <div class="font-mono text-xs text-gray-800 dark:text-gray-200 mt-1 select-all break-all" x-text="activeGuide.route_definition"></div>
                                            </div>
                                        </template>
                                        <template x-if="activeGuide.policy_gate">
                                            <div class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-gray-150 dark:border-gray-800">
                                                <span class="text-[10px] font-extrabold uppercase text-gray-400 dark:text-gray-500 tracking-wider">Policy / Authorization</span>
                                                <div class="font-mono text-xs text-gray-800 dark:text-gray-200 mt-1 select-all break-all" x-text="activeGuide.policy_gate"></div>
                                            </div>
                                        </template>
                                        <template x-if="activeGuide.middleware">
                                            <div class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-gray-150 dark:border-gray-800">
                                                <span class="text-[10px] font-extrabold uppercase text-gray-400 dark:text-gray-500 tracking-wider">Middleware</span>
                                                <div class="font-mono text-xs text-gray-800 dark:text-gray-200 mt-1 select-all break-all" x-text="activeGuide.middleware"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>



                                {{-- Form & Validation Details (Jika ada CRUD / Parameters) --}}
                                <template x-if="activeGuide.form_details && activeGuide.form_details.length > 0">
                                    <div class="space-y-3">
                                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">Struktur Model, Tabel, atau API Parameter</h3>
                                        <div class="border border-gray-150 dark:border-gray-800 rounded-xl overflow-x-auto overflow-hidden shadow-xs">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-xs">
                                                <thead class="bg-gray-50 dark:bg-gray-800">
                                                    <tr>
                                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase dark:text-gray-400">Komponen / Kolom</th>
                                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase dark:text-gray-400">Tipe</th>
                                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase dark:text-gray-400">Aturan Teknis</th>
                                                        <th class="px-4 py-3 text-left font-bold text-gray-500 uppercase dark:text-gray-400">Deskripsi Validasi</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-150 dark:divide-gray-800 bg-white dark:bg-gray-900">
                                                    <template x-for="field in activeGuide.form_details" :key="field.field">
                                                        <tr>
                                                            <td class="px-4 py-3 font-mono font-semibold text-gray-900 dark:text-white" x-text="field.field"></td>
                                                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400" x-text="field.type"></td>
                                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300" x-text="field.rules"></td>
                                                            <td class="px-4 py-3 font-medium text-indigo-600 dark:text-indigo-400" x-text="field.validation"></td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>

                                {{-- Output --}}
                                <template x-if="activeGuide.output">
                                    <div class="p-4 rounded-xl border border-emerald-100 bg-emerald-50/20 dark:border-emerald-950/30 dark:bg-emerald-950/10">
                                        <h3 class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-wider mb-1.5">Hasil Akhir (Output Sistem)</h3>
                                        <p class="text-sm text-emerald-900 dark:text-emerald-300" x-text="activeGuide.output"></p>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="!activeGuide">
                            <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Tidak ada panduan developer yang tersedia saat ini.</span>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    @else
        <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data panduan developer.</p>
        </div>
    @endif
@endsection

@push('styles')
    <!-- PrismJS Dark Theme Tomorrow -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" />
    <style>
        /* Custom overrides to make Prism Tomorrow look even cleaner and fit our container */
        pre[class*="language-"] {
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            max-height: 300px !important;
            overflow: auto !important;
        }
        code[class*="language-"] {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
            text-shadow: none !important;
        }
        /* Custom scrollbar for pre blocks */
        .scrollbar-thin::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 4px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
@endpush

@push('scripts')
    <!-- PrismJS Core -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <!-- PrismJS Markup-templating (required for PHP) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup-templating.min.js"></script>
    <!-- PrismJS PHP Language syntax highlighting -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initial syntax highlight check
            if (window.Prism) {
                Prism.highlightAll();
            }
        });
    </script>
@endpush
