<x-guest-layout>
    <div class="relative h-dvh overflow-hidden bg-[#f7fbff] text-[#17233f]">
        <div class="absolute inset-0 bg-[linear-gradient(116deg,#ffffff_0%,#f8fbff_39%,#edf5ff_100%)]"></div>
        <div class="absolute -left-[11%] -top-[43%] h-[76%] w-[48%] rounded-b-full bg-[#edf6ff]/90"></div>
        <div class="absolute -left-[16%] -top-[52%] h-[84%] w-[53%] rounded-b-full bg-white/80"></div>
        <div class="absolute left-[25%] top-0 h-full w-[18%] bg-[#eaf3ff]/55"
            style="clip-path: polygon(36% 0, 100% 0, 51% 100%, 0 100%);">
        </div>
        <div class="absolute right-0 top-[31%] h-[46%] w-[37%] bg-[#eaf3ff]/72"
            style="clip-path: ellipse(78% 43% at 100% 50%);">
        </div>
        <div class="absolute bottom-0 right-[11%] h-[38%] w-[37%] bg-[#dcecff]/64"
            style="clip-path: polygon(22% 100%, 100% 0, 100% 100%);">
        </div>
        <div class="absolute -bottom-[32%] right-[-6%] h-[51%] w-[38%] rounded-tl-full bg-[#d8eaff]/80"></div>
        <div class="absolute -bottom-[44%] right-[-12%] h-[59%] w-[44%] rounded-tl-full bg-white/55"></div>
        <div class="absolute left-8 top-[63%] hidden h-[245px] w-[245px] opacity-45 sm:block"
            style="background-image: radial-gradient(circle, #60a5fa 1.35px, transparent 1.35px); background-size: 23px 23px;">
        </div>
        <div class="absolute right-8 top-8 hidden h-[210px] w-[390px] opacity-35 md:block"
            style="background-image: radial-gradient(circle, #60a5fa 1.2px, transparent 1.2px); background-size: 24px 24px;">
        </div>

        <main class="relative z-10 flex h-full items-center">
            <div class="mx-auto grid h-full w-full max-w-[1320px] items-center gap-3 px-4 py-3 sm:gap-5 sm:px-8 sm:py-5 lg:grid-cols-[0.95fr_1.05fr] lg:gap-6 lg:px-10">
                <section class="flex min-h-0 flex-col items-center text-center lg:-translate-y-2 lg:items-start lg:pl-10 lg:text-left">
                    <div class="flex flex-col items-center lg:items-start">
                        <img src="/images/logo/logo.svg" alt="PINDANG OI" class="h-10 w-auto sm:h-12 lg:h-14">

                        <div class="mt-4 hidden max-w-full items-center gap-3 rounded-2xl border border-[#b7d2ff] bg-white/80 px-4 py-2.5 text-xs font-medium text-[#1f2d4a] shadow-[0_8px_22px_rgba(45,103,207,0.14)] backdrop-blur-sm sm:mt-5 sm:inline-flex sm:px-5 sm:py-3 sm:text-sm lg:mt-8 lg:text-base">
                            <svg class="h-5 w-5 shrink-0 text-[#1463ff]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 20V10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                                <path d="M12 20V5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                                <path d="M19 20V14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                            </svg>
                            <span>Portal Integrasi Data Kinerja dan Informasi Penunjang</span>
                        </div>

                        <div class="mt-3 h-0.5 w-16 rounded-full bg-[#7eb4ff] sm:mt-4 lg:mt-7"></div>

                        <h1 class="mt-3 text-lg font-semibold tracking-normal text-[#17233f] sm:text-xl lg:mt-5 lg:text-2xl">
                            Badan Pusat Statistik Kabupaten Ogan Ilir
                        </h1>
                    </div>
                </section>

                <section class="flex min-h-0 justify-center lg:justify-end lg:pr-8">
                    <div class="w-full max-w-[500px] rounded-[22px] border border-white/80 bg-white/[0.92] px-5 py-5 shadow-[0_28px_90px_rgba(31,45,74,0.12)] backdrop-blur-xl sm:rounded-[26px] sm:px-8 sm:py-7">
                        <div class="text-center">
                            <h2 class="text-xl font-bold tracking-normal text-[#17233f] sm:text-2xl lg:text-[26px]">
                                Selamat datang kembali
                            </h2>
                            <p class="mt-1.5 text-sm font-medium text-[#72809d] sm:mt-2 sm:text-base">
                                Silakan masuk untuk mengakses portal Pindang OI.
                            </p>
                        </div>

                        @if (session('status'))
                            <div class="mt-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
                                <x-auth-session-status class="text-sm font-medium text-green-700" :status="session('status')" />
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="mt-5 sm:mt-6"
                            x-data="rememberLoginForm(@js(old('username', '')))"
                            x-init="init()"
                            @submit="persistRememberPreference()">
                            @csrf

                            <div>
                                <label for="username" class="block text-sm font-semibold text-[#17233f]">
                                    Username
                                </label>
                                <div class="group relative mt-2.5">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-5">
                                        <svg class="h-5 w-5 text-[#6e7b92] transition-colors group-focus-within:text-[#1463ff]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path d="M12 12a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Z" />
                                            <path d="M4.5 21a7.5 7.5 0 0 1 15 0H4.5Z" />
                                        </svg>
                                    </div>
                                    <input id="username" type="text" name="username" x-model="username" required autofocus autocomplete="username"
                                        class="h-12 w-full rounded-xl border border-[#d8e1ef] bg-white pl-14 pr-4 text-base font-medium text-[#17233f] shadow-[0_5px_14px_rgba(31,45,74,0.08)] outline-none transition placeholder:text-[#9aa7bd] focus:border-[#1463ff] focus:ring-4 focus:ring-[#1463ff]/10 sm:h-[52px]"
                                        placeholder="Masukkan username" />
                                </div>
                                <x-input-error :messages="$errors->get('username')" class="mt-2 text-sm text-red-500" />
                            </div>

                            <div class="mt-4 sm:mt-5" x-data="{ show: false }">
                                <label for="password" class="block text-sm font-semibold text-[#17233f]">
                                    Password
                                </label>
                                <div class="group relative mt-2.5">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-5">
                                        <svg class="h-5 w-5 text-[#6e7b92] transition-colors group-focus-within:text-[#1463ff]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M7 9V7a5 5 0 0 1 10 0v2h.25A2.75 2.75 0 0 1 20 11.75v6.5A2.75 2.75 0 0 1 17.25 21H6.75A2.75 2.75 0 0 1 4 18.25v-6.5A2.75 2.75 0 0 1 6.75 9H7Zm2.2 0h5.6V7a2.8 2.8 0 1 0-5.6 0v2Z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                        class="h-12 w-full rounded-xl border border-[#d8e1ef] bg-white pl-14 pr-14 text-base font-medium text-[#17233f] shadow-[0_5px_14px_rgba(31,45,74,0.08)] outline-none transition placeholder:text-[#9aa7bd] focus:border-[#1463ff] focus:ring-4 focus:ring-[#1463ff]/10 sm:h-[52px]"
                                        placeholder="Masukkan password" />
                                    <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-0 flex items-center pr-5 text-[#718096] transition hover:text-[#1463ff]"
                                        :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                                        <svg x-show="!show" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6-9.75-6-9.75-6Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg x-show="show" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.58 10.58A3 3 0 0 0 14 14m-5.76-5.76C5.72 9.56 3.75 12 3.75 12s3.5 6 9.75 6c1.37 0 2.62-.29 3.72-.75M14.73 5.35c5.15.98 7.52 6.65 7.52 6.65a15.18 15.18 0 0 1-3.11 3.7" />
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500" />
                            </div>

                            <label for="remember" class="mt-3 inline-flex select-none items-center gap-2.5 text-sm font-semibold text-[#5f6f8c] sm:mt-4">
                                <input id="remember" type="checkbox" name="remember" value="1" x-model="remember"
                                    class="h-4 w-4 rounded border-[#c9d6e8] bg-white text-[#0968ff] shadow-sm focus:ring-3 focus:ring-[#0968ff]/15">
                                <span>Ingat saya</span>
                            </label>

                            <button type="submit"
                                class="mt-4 flex h-12 w-full items-center justify-center gap-3 rounded-xl bg-[#0968ff] text-base font-bold text-white shadow-[0_14px_28px_rgba(9,104,255,0.26)] transition hover:bg-[#0056e7] focus:outline-none focus:ring-4 focus:ring-[#0968ff]/20 sm:mt-5 sm:h-[52px]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M8 7 3 12l5 5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M3 12h14" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15 7h2.5A3.5 3.5 0 0 1 21 10.5v3A3.5 3.5 0 0 1 17.5 17H15" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Masuk
                            </button>

                            <div class="mt-4 flex items-center gap-4 sm:mt-6 sm:gap-5">
                                <div class="h-px flex-1 bg-[#d8e1ef]"></div>
                                <span class="whitespace-nowrap text-sm font-medium text-[#8390aa]">
                                    &copy; {{ date('Y') }} BPS Kabupaten Ogan Ilir
                                </span>
                                <div class="h-px flex-1 bg-[#d8e1ef]"></div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </main>

        <script>
            @if (session('clear_remember_login'))
                localStorage.removeItem('pindang_oi_remember_login');
            @endif

            function rememberLoginForm(initialUsername) {
                return {
                    username: initialUsername || '',
                    remember: false,
                    storageKey: 'pindang_oi_remember_login',
                    maxAgeMs: 7 * 24 * 60 * 60 * 1000,
                    init() {
                        const saved = this.readSavedPreference();

                        if (!saved) {
                            return;
                        }

                        this.remember = true;
                        if (!this.username) {
                            this.username = saved.username || '';
                        }
                    },
                    readSavedPreference() {
                        try {
                            const raw = localStorage.getItem(this.storageKey);
                            if (!raw) {
                                return null;
                            }

                            const saved = JSON.parse(raw);
                            if (!saved.expiresAt || saved.expiresAt <= Date.now()) {
                                localStorage.removeItem(this.storageKey);
                                return null;
                            }

                            return saved;
                        } catch (error) {
                            localStorage.removeItem(this.storageKey);
                            return null;
                        }
                    },
                    persistRememberPreference() {
                        if (!this.remember) {
                            localStorage.removeItem(this.storageKey);
                            return;
                        }

                        localStorage.setItem(this.storageKey, JSON.stringify({
                            username: this.username,
                            expiresAt: Date.now() + this.maxAgeMs,
                        }));
                    },
                };
            }
        </script>
    </div>
</x-guest-layout>
