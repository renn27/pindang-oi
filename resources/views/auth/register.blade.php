<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 px-4 py-4">
        <div class="w-full max-w-3xl bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">

            {{-- HEADER BPS --}}
            <div class="relative bg-gradient-to-r from-blue-700 via-green-600 to-orange-500 px-6 py-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div>
                        <h1 class="text-lg font-bold text-white mb-1">PINDANG OI</h1>
                        <p class="text-xs text-white mt-1 font-semibold">Portal Integrasi Data dan Informasi</p>
                        <h2 class="text-base font-semibold text-white/90">Badan Pusat Statistik - Ogan Ilir</h2>
                        <p class="text-xs text-white/80 mt-1">Registrasi Akun Pegawai</p>
                    </div>
                    <div class="mt-3 md:mt-0">
                        <div class="inline-flex items-center bg-white/20 backdrop-blur-sm rounded-md px-3 py-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-white text-xs font-medium">Pendaftaran Pegawai</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM --}}
            <form method="POST" action="{{ route('register') }}" class="px-6 py-6">
                @csrf

                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <div class="h-8 w-1 bg-blue-700 rounded-r mr-2"></div>
                        <h3 class="text-base font-semibold text-gray-800">Informasi Pribadi Pegawai</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- NAMA --}}
                        <div class="space-y-1.5">
                            <x-input-label for="name" value="Nama Lengkap" class="font-medium text-gray-700 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <x-text-input
                                    id="name"
                                    class="block mt-1 w-full pl-9 text-sm py-2"
                                    type="text"
                                    name="name"
                                    :value="old('name')"
                                    required
                                    placeholder="Masukkan nama lengkap"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
                        </div>

                        {{-- JABATAN --}}
                        <div class="space-y-1.5">
                            <x-input-label for="jabatan" value="Jabatan" class="font-medium text-gray-700 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                    </svg>
                                </div>
                                <x-text-input
                                    id="jabatan"
                                    class="block mt-1 w-full pl-9 text-sm py-2"
                                    type="text"
                                    name="jabatan"
                                    :value="old('jabatan')"
                                    required
                                    placeholder="Masukkan jabatan"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('jabatan')" class="mt-1 text-xs" />
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <div class="h-8 w-1 bg-green-600 rounded-r mr-2"></div>
                        <h3 class="text-base font-semibold text-gray-800">Informasi Akun Sistem</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- USERNAME --}}
                        <div class="space-y-1.5">
                            <x-input-label for="username" value="Username" class="font-medium text-gray-700 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <x-text-input
                                    id="username"
                                    class="block mt-1 w-full pl-9 text-sm py-2"
                                    type="text"
                                    name="username"
                                    :value="old('username')"
                                    required
                                    placeholder="Masukkan username"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('username')" class="mt-1 text-xs" />
                        </div>

                        {{-- EMAIL --}}
                        <div class="space-y-1.5">
                            <x-input-label for="email" value="Email" class="font-medium text-gray-700 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </div>
                                <x-text-input
                                    id="email"
                                    class="block mt-1 w-full pl-9 text-sm py-2"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    placeholder="nama@bps.go.id"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                        </div>

                        {{-- PASSWORD --}}
                        <div class="space-y-1.5">
                            <x-input-label for="password" value="Password" class="font-medium text-gray-700 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <x-text-input
                                    id="password"
                                    class="block mt-1 w-full pl-9 text-sm py-2"
                                    type="password"
                                    name="password"
                                    required
                                    placeholder="Minimal 8 karakter"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                            <p class="text-xs text-gray-500 mt-0.5">Kombinasi huruf, angka, dan simbol</p>
                        </div>

                        {{-- CONFIRM PASSWORD --}}
                        <div class="space-y-1.5">
                            <x-input-label for="password_confirmation" value="Konfirmasi Password" class="font-medium text-gray-700 text-sm" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <x-text-input
                                    id="password_confirmation"
                                    class="block mt-1 w-full pl-9 text-sm py-2"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    placeholder="Ulangi password"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex flex-col md:flex-row items-center justify-between mt-8 pt-6 border-t border-gray-200">
                    <div class="mb-4 md:mb-0">
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center text-blue-700 hover:text-blue-800 font-medium text-sm transition-colors group"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 group-hover:translate-x-1 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Sudah memiliki akun? Masuk di sini</span>
                        </a>
                    </div>

                    <div class="flex space-x-3">
                        <button
                            type="reset"
                            class="px-4 py-2 rounded-md text-sm font-semibold text-gray-700
                                    bg-gray-100 hover:bg-gray-200 transition-all duration-200
                                    border border-gray-300 shadow-sm hover:shadow">
                            Reset
                        </button>

                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-md text-sm font-semibold text-white
                                    bg-gradient-to-r from-blue-700 to-green-600 hover:from-blue-800 hover:to-green-700
                                    transition-all duration-200 shadow hover:shadow-md
                                    transform hover:-translate-y-0.5 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Daftarkan Akun
                        </button>
                    </div>
                </div>
            </form>

            {{-- FOOTER --}}
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-center text-xs">
                    <div class="text-gray-600 mb-1 md:mb-0">
                        © {{ date('Y') }} Badan Pusat Statistik. Hak Cipta Dilindungi.
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div class="h-2 w-2 rounded-full bg-blue-700 mr-1.5"></div>
                            <span class="text-gray-600">Formal</span>
                        </div>
                        <div class="flex items-center">
                            <div class="h-2 w-2 rounded-full bg-green-600 mr-1.5"></div>
                            <span class="text-gray-600">Terpercaya</span>
                        </div>
                        <div class="flex items-center">
                            <div class="h-2 w-2 rounded-full bg-orange-500 mr-1.5"></div>
                            <span class="text-gray-600">Aman</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
