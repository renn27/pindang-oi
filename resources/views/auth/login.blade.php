<x-guest-layout>
    <div class="flex items-center justify-center">
        {{-- tampilan form --}}
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            {{-- HEADER BPS --}}
            <div class="relative bg-gradient-to-tr from-[#3a4fd9] via-[#465fff] to-[#5a6fff] px-6 py-4">
                <div class="flex flex-col items-center text-center">
                    <div class="w-full">
                        <img src="/images/logo/logo-dark.svg" alt="Logo"
                            class="mx-auto w-auto max-w-[150px] h-[40px] mb-2" />
                        <p class="text-xs text-white mt-1 font-semibold">Portal Integrasi Data dan Informasi</p>
                        <h2 class="text-base font-semibold text-white/90">Badan Pusat Statistik - Ogan Ilir</h2>
                    </div>
                </div>
            </div>

            {{-- SESSION STATUS --}}
            <div class="px-6 pt-6">
                <x-auth-session-status class="mb-4 text-sm" :status="session('status')" />
            </div>

            {{-- FORM LOGIN --}}
            <form method="POST" action="{{ route('login') }}" class="px-6 py-4">
                @csrf

                <!-- Email/Username Address -->
                <div class="space-y-1.5 mb-4">
                    <x-input-label for="username" :value="__('Username')" class="font-medium text-gray-700 text-sm" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#465fff]" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <x-text-input id="username" class="block mt-1 w-full pl-9 text-sm py-2" type="text"
                            name="username" :value="old('username')" required autofocus autocomplete="username"
                            placeholder="Masukkan username Anda" />
                    </div>
                    <x-input-error :messages="$errors->get('username')" class="mt-1 text-xs" />
                </div>

                <!-- Password -->
                <div class="space-y-1.5 mb-4">
                    <x-input-label for="password" :value="__('Password')" class="font-medium text-gray-700 text-sm" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#465fff]" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <x-text-input id="password" class="block mt-1 w-full pl-9 text-sm py-2" type="password"
                            name="password" required autocomplete="current-password"
                            placeholder="Masukkan password Anda" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mb-6">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox"
                            class="rounded border-gray-300 text-[#465fff] shadow-sm focus:ring-[#465fff] focus:border-[#465fff]"
                            name="remember">
                        <span class="ms-2 text-sm text-gray-600 hover:text-gray-800 transition-colors">
                            {{ __('Ingat saya') }}
                        </span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-[#465fff] hover:text-[#3a4fd9] hover:underline font-medium transition-colors"
                            href="{{ route('password.request') }}">
                            {{ __('Lupa password?') }}
                        </a>
                    @endif
                </div>

                <!-- Login Button -->
                <div class="mb-4">
                    <button type="submit"
                        class="w-full px-4 py-2.5 rounded-md text-sm font-semibold text-white
                                bg-[#465fff] hover:bg-[#3a4fd9]
                                transition-all duration-200 shadow hover:shadow-md
                                transform hover:-translate-y-0.5 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('Masuk ke Sistem') }}
                    </button>
                </div>

                <!-- Register Link -->
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600">
                        Belum memiliki akun?
                        <a href="{{ route('register') }}"
                            class="ml-1 font-semibold text-[#465fff] hover:text-[#3a4fd9] hover:underline transition-colors">
                            Daftar di sini
                        </a>
                    </p>
                </div>
            </form>

            {{-- FOOTER --}}
            <div class="px-6 py-2 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col items-center text-xs text-gray-600">
                    <div>
                        © {{ date('Y') }} Badan Pusat Statistik. Hak Cipta Dilindungi.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
