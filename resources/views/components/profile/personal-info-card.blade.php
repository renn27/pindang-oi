<div x-data="{saveProfile(){
    console.log('Saving profile...');
}}">
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <!-- Header Clean -->
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Informasi Pribadi
                </h4>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 lg:p-8">
            <div class="space-y-6">
                <!-- Row 1: Nama & Email -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Nama Lengkap -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Nama Lengkap
                            </p>
                        </div>
                        <div class="pl-6">
                            @if(Auth::user()->nama_pegawai)
                                <p class="text-base font-medium text-gray-800 dark:text-gray-200">
                                    {{ Auth::user()->nama_pegawai }}
                                </p>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-400 dark:text-gray-500 italic">
                                        Belum diisi
                                    </span>
                                    <button @click="$dispatch('open-profile-info-modal')"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline transition-colors">
                                        Tambah
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="h-px bg-gray-100 dark:bg-gray-800 mt-3"></div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Email
                            </p>
                        </div>
                        <div class="pl-6">
                            @if(Auth::user()->email)
                                <p class="text-base font-medium text-gray-800 dark:text-gray-200">
                                    {{ Auth::user()->email }}
                                </p>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-400 dark:text-gray-500 italic">
                                        Belum diisi
                                    </span>
                                    <button @click="$dispatch('open-profile-info-modal')"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline transition-colors">
                                        Tambah
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="h-px bg-gray-100 dark:bg-gray-800 mt-3"></div>
                    </div>
                </div>

                <!-- Row 2: Jabatan & Alamat -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Jabatan -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Jabatan
                            </p>
                        </div>
                        <div class="pl-6">
                            @if(Auth::user()->jabatan)
                                <p class="text-base font-medium text-gray-800 dark:text-gray-200">
                                    {{ Auth::user()->jabatan }}
                                </p>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-400 dark:text-gray-500 italic">
                                        Belum diisi
                                    </span>
                                    <button @click="$dispatch('open-profile-info-modal')"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline transition-colors">
                                        Tambah
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="h-px bg-gray-100 dark:bg-gray-800 mt-3"></div>
                    </div>

                    <!-- Alamat -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                Alamat
                            </p>
                        </div>
                        <div class="pl-6">
                            @if(Auth::user()->alamat)
                                <p class="text-base font-medium text-gray-800 dark:text-gray-200 leading-relaxed">
                                    {{ Auth::user()->alamat }}
                                </p>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-400 dark:text-gray-500 italic">
                                        Belum diisi
                                    </span>
                                    <button @click="$dispatch('open-profile-info-modal')"
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline transition-colors">
                                        Tambah
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="h-px bg-gray-100 dark:bg-gray-800 mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>