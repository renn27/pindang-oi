<x-guest-layout>
    <div class="flex items-center justify-center">
        {{-- Container card --}}
        <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            {{-- Header dengan gradien BPS --}}
            <div class="relative bg-gradient-to-tr from-[#3a4fd9] via-[#465fff] to-[#5a6fff] px-6 py-5">
                <div class="flex flex-col items-center text-center">
                    <div class="w-full">
                        <img src="/images/logo/logo-dark.svg" alt="Logo BPS" 
                             class="mx-auto w-auto max-w-[140px] h-[35px] mb-2" />
                        <p class="text-xs text-white/90 mt-1 font-medium">Portal Integrasi Data dan Informasi</p>
                        <h2 class="text-sm font-semibold text-white/95 mt-0.5">Badan Pusat Statistik - Ogan Ilir</h2>
                    </div>
                </div>
            </div>

            {{-- Session Status --}}
            <div class="px-6 pt-6">
                <x-auth-session-status class="mb-4 text-sm dark:text-gray-300" :status="session('status')" />
            </div>

            {{-- Form Login --}}
            <form method="POST" action="{{ route('login') }}" class="px-6 py-5">
                @csrf

                <!-- Username Field -->
                <div class="space-y-2 mb-5">
                    <x-input-label for="username" :value="__('Username')" 
                                   class="font-medium text-gray-700 dark:text-gray-300 text-sm" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#465fff] dark:text-[#5a6fff]" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <x-text-input id="username" 
                                   class="block w-full pl-10 h-11 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors" 
                                   type="text"
                                   name="username" :value="old('username')" required autofocus autocomplete="username"
                                   placeholder="Masukkan username Anda" />
                    </div>
                    <x-input-error :messages="$errors->get('username')" class="mt-1 text-xs dark:text-red-400" />
                </div>

                <!-- Password Field with show/hide toggle -->
                <div class="space-y-2 mb-6" x-data="{ showPassword: false }">
                    <x-input-label for="password" :value="__('Password')" 
                                   class="font-medium text-gray-700 dark:text-gray-300 text-sm" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#465fff] dark:text-[#5a6fff]" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="password" 
                               class="block w-full pl-10 pr-10 h-11 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors border-gray-300 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                               :type="showPassword ? 'text' : 'password'"
                               name="password" required autocomplete="current-password"
                               placeholder="Masukkan password Anda" />
                        
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
                </div>

                <!-- Login Button -->
                <div class="mb-6">
                    <button type="submit"
                        class="w-full h-11 rounded-lg text-sm font-semibold text-white
                                bg-[#465fff] hover:bg-[#3a4fd9] dark:bg-[#5a6fff] dark:hover:bg-[#4a5fff]
                                transition-all duration-200 shadow-sm hover:shadow
                                flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('Masuk ke Sistem') }}
                    </button>
                </div>

                <!-- Register Link -->
                <div class="pt-5 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                        Belum memiliki akun?
                        <a href="{{ route('register') }}"
                            class="ml-1 font-semibold text-[#465fff] hover:text-[#3a4fd9] dark:text-[#5a6fff] dark:hover:text-[#7a8fff] hover:underline transition-colors">
                            Daftar di sini
                        </a>
                    </p>
                </div>
            </form>

            {{-- Footer --}}
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col items-center text-xs text-gray-600 dark:text-gray-400">
                    <div class="text-center">
                        © {{ date('Y') }} Badan Pusat Statistik. Hak Cipta Dilindungi.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>