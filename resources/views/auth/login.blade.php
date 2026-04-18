<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-green-50 dark:from-gray-900 dark:via-gray-800 dark:to-blue-950">
        
        {{-- Background Pattern - Dot Grid lebih visible di light mode --}}
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, #3b82f6 2px, transparent 2px); background-size: 40px 40px; opacity: 0.12;"></div>
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, #2563eb 1px, transparent 1px); background-size: 20px 20px; opacity: 0.08;"></div>
        <div class="absolute inset-0 dark:opacity-100" style="background-image: radial-gradient(circle at 1.5px 1.5px, #60a5fa 1.5px, transparent 1.5px); background-size: 40px 40px; opacity: 0.05;"></div>
        
        {{-- Gradient Orbs dengan warna BPS --}}
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-blue-200/50 dark:bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-20 w-80 h-80 bg-green-200/50 dark:bg-green-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 left-1/4 w-48 h-48 bg-yellow-200/40 dark:bg-yellow-500/5 rounded-full blur-3xl"></div>

        {{-- Container Utama --}}
        <div class="relative w-full max-w-sm bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden transition-all duration-300">
            
            {{-- Accent line di atas card warna biru BPS
            <div class="absolute top-0 left-0 right-0 h-1 bg-blue-600"></div> --}}

            {{-- Header Area - Clean & Centered --}}
            <div class="flex flex-col items-center text-center px-8 pt-10 pb-6">
                {{-- Logo dengan ring subtle dan support dark mode --}}
                <div class="mb-5 p-2 bg-white dark:bg-gray-800">
                    {{-- Light mode logo --}}
                    <img src="/images/logo/logo.svg" alt="Logo BPS" class="h-10 w-auto dark:hidden">
                    {{-- Dark mode logo --}}
                    <img src="/images/logo/logo-dark.svg" alt="Logo BPS" class="h-10 w-auto hidden dark:block">
                </div>

                {{-- Judul & Subjudul --}}
                <h1 class="text-xl font-semibold text-blue-700 dark:text-blue-400 tracking-tight">
                    Portal Integrasi Data dan Informasi Penunjang
                </h1>
                <div class="relative mt-4">
                    <div class="absolute inset-0 bg-blue-500 rounded-full blur opacity-10"></div>
                    <p class="relative text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 px-4 py-1.5 rounded-full border border-blue-200 dark:border-blue-800 shadow-sm">
                        Badan Pusat Statistik Kabupaten Ogan Ilir
                    </p>
                </div>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="px-8 pb-2">
                    <x-auth-session-status class="mb-2 text-sm text-green-600 dark:text-green-400" :status="session('status')" />
                </div>
            @endif

            {{-- Form Login - Clean & Simple --}}
            <form method="POST" action="{{ route('login') }}" class="px-8 pb-8">
                @csrf

                {{-- Username --}}
                <div class="mb-5">
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Username
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-600 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl shadow-sm text-sm bg-white/50 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 dark:focus:border-blue-500 transition-all duration-200"
                            placeholder="Masukkan username" />
                    </div>
                    <x-input-error :messages="$errors->get('username')" class="mt-1.5 text-xs text-red-500 dark:text-red-400" />
                </div>

                {{-- Password --}}
                <div class="mb-6" x-data="{ show: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Password
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-600 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                            class="block w-full pl-10 pr-10 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl shadow-sm text-sm bg-white/50 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 dark:focus:border-blue-500 transition-all duration-200"
                            placeholder="••••••••" />
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500 dark:text-red-400" />
                </div>

                {{-- Tombol Login warna biru BPS --}}
                <button type="submit"
                    class="w-full flex justify-center items-center gap-2 py-2.5 px-4 rounded-xl shadow-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Masuk
                </button>
            </form>

            {{-- Footer Copyright --}}
            <div class="relative py-4 px-8 text-center border-t border-gray-200/50 dark:border-gray-700/50">
                <div class="absolute inset-0 bg-blue-50/30 dark:bg-blue-900/10"></div>
                <p class="relative text-xs text-gray-500 dark:text-gray-400">
                    © {{ date('Y') }} BPS Kabupaten Ogan Ilir
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>