<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-900">
        <div class="w-full max-w-3xl bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">

            {{-- Header BPS --}}
            <div class="relative bg-gradient-to-tr from-[#3a4fd9] via-[#465fff] to-[#5a6fff] px-6 py-5">
                <div class="flex flex-col items-center text-center">
                    <div class="w-full">
                        <img src="/images/logo/logo-dark.svg" alt="Logo BPS"
                            class="mx-auto w-auto max-w-[140px] h-[35px] mb-2" />
                        <p class="text-xs text-white/90 mt-1 font-medium">Portal Integrasi Data dan Informasi Penunjang</p>
                        <h2 class="text-sm font-semibold text-white/95 mt-0.5">Badan Pusat Statistik - Ogan Ilir</h2>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('register') }}" class="px-6 py-6">
                @csrf

                {{-- Section 1: Informasi Pribadi --}}
                <div class="mb-8">
                    <div class="flex items-center mb-5">
                        <div class="h-7 w-1 bg-[#465fff] dark:bg-[#5a6fff] rounded-r mr-3"></div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">Informasi Pribadi Pegawai</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Nama Lengkap --}}
                        <div class="space-y-2">
                            <x-input-label for="name" value="Nama Lengkap"
                                class="font-medium text-gray-700 dark:text-gray-300 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#465fff] dark:text-[#5a6fff]"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <x-text-input id="name"
                                    class="block w-full pl-10 h-11 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
                                    type="text"
                                    name="name" :value="old('name')" required placeholder="Masukkan nama lengkap" />
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs dark:text-red-400" />
                        </div>

                        {{-- Jabatan --}}
                        <div class="space-y-2">
                            <x-input-label for="jabatan" value="Jabatan"
                                class="font-medium text-gray-700 dark:text-gray-300 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#465fff] dark:text-[#5a6fff]"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                    </svg>
                                </div>
                                <x-text-input id="jabatan"
                                    class="block w-full pl-10 h-11 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
                                    type="text"
                                    name="jabatan" :value="old('jabatan')" required placeholder="Masukkan jabatan" />
                            </div>
                            <x-input-error :messages="$errors->get('jabatan')" class="mt-1 text-xs dark:text-red-400" />
                        </div>
                    </div>
                </div>

                {{-- Section 2: Informasi Akun Sistem --}}
                <div class="mb-8">
                    <div class="flex items-center mb-5">
                        <div class="h-7 w-1 bg-[#465fff] dark:bg-[#5a6fff] rounded-r mr-3"></div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">Informasi Akun Sistem</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Username --}}
                        <div class="space-y-2">
                            <x-input-label for="username" value="Username"
                                class="font-medium text-gray-700 dark:text-gray-300 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#465fff] dark:text-[#5a6fff]"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <x-text-input id="username"
                                    class="block w-full pl-10 h-11 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
                                    type="text"
                                    name="username" :value="old('username')" required placeholder="Masukkan username" />
                            </div>
                            <x-input-error :messages="$errors->get('username')" class="mt-1 text-xs dark:text-red-400" />
                        </div>

                        {{-- Email --}}
                        <div class="space-y-2">
                            <x-input-label for="email" value="Email"
                                class="font-medium text-gray-700 dark:text-gray-300 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#465fff] dark:text-[#5a6fff]"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </div>
                                <x-text-input id="email"
                                    class="block w-full pl-10 h-11 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors"
                                    type="email"
                                    name="email" :value="old('email')" required placeholder="nama@bps.go.id" />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs dark:text-red-400" />
                        </div>

                        {{-- Password --}}
                        <div class="space-y-2" x-data="{ showPassword: false }">
                            <x-input-label for="password" value="Password"
                                class="font-medium text-gray-700 dark:text-gray-300 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#465fff] dark:text-[#5a6fff]"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input id="password"
                                    class="block w-full pl-10 pr-10 h-11 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors border-gray-300 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password" required autocomplete="new-password"
                                    placeholder="Minimal 8 karakter" />

                                <!-- Show/Hide Password Toggle -->
                                <button type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs dark:text-red-400" />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kombinasi huruf, angka, dan simbol</p>
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="space-y-2" x-data="{ showConfirmPassword: false }">
                            <x-input-label for="password_confirmation" value="Konfirmasi Password"
                                class="font-medium text-gray-700 dark:text-gray-300 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#465fff] dark:text-[#5a6fff]"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input id="password_confirmation"
                                    class="block w-full pl-10 pr-10 h-11 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors border-gray-300 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    name="password_confirmation" required autocomplete="new-password"
                                    placeholder="Ulangi password" />

                                <!-- Show/Hide Confirm Password Toggle -->
                                <button type="button"
                                        @click="showConfirmPassword = !showConfirmPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                                    <svg x-show="!showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs dark:text-red-400" />
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col md:flex-row items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="mb-4 md:mb-0">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center text-[#465fff] hover:text-[#3a4fd9] dark:text-[#5a6fff] dark:hover:text-[#7a8fff] font-medium text-sm transition-colors group">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 mr-2 group-hover:-translate-x-0.5 transition-transform"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="dark:text-gray-300">Sudah memiliki akun? Masuk di sini</span>
                        </a>
                    </div>

                    <div class="flex gap-3">
                        <button type="reset"
                            class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300
                                    bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600
                                    transition-colors border border-gray-300 dark:border-gray-600">
                            Reset
                        </button>

                        <button type="submit"
                            class="px-6 py-2.5 rounded-lg text-sm font-semibold text-white
                                    bg-[#465fff] hover:bg-[#3a4fd9] dark:bg-[#5a6fff] dark:hover:bg-[#4a5fff]
                                    transition-colors shadow-sm hover:shadow">
                            Daftar
                        </button>
                    </div>
                </div>
            </form>

            {{-- Footer --}}
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col items-center text-xs text-gray-600 dark:text-gray-400">
                    <div class="text-center">
                        © {{ date('Y') }} Pindang OI. Hak Cipta Dilindungi.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
