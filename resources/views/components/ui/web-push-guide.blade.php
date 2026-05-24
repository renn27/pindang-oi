<div
    x-data="{
        open: false,
        closed: false,
        pushEnabled: false,
        permission: typeof Notification !== 'undefined' ? Notification.permission : 'unsupported',
        dismiss() {
            this.closed = true;
        },
        openSettings() {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('open-push-settings'));
            }, 0);
        },
        refreshPermission() {
            this.permission = typeof Notification !== 'undefined' ? Notification.permission : 'unsupported';
        },
        async refreshPushStatus() {
            this.refreshPermission();

            if (!('serviceWorker' in navigator) || !('PushManager' in window) || this.permission !== 'granted') {
                this.pushEnabled = false;
                return;
            }

            const registration = await navigator.serviceWorker.register('/sw.js');
            const subscription = await registration.pushManager.getSubscription();
            this.pushEnabled = Boolean(subscription);
        }
    }"
    x-init="
        const runRefreshPushStatus = () => refreshPushStatus();
        if ('requestIdleCallback' in window) {
            window.requestIdleCallback(runRefreshPushStatus, { timeout: 2500 });
        } else {
            setTimeout(runRefreshPushStatus, 800);
        }
        window.addEventListener('push-status-updated', () => refreshPushStatus())
    "
    @open-web-push-guide.window="open = true"
>
    <div
        x-show="!closed && !pushEnabled"
        x-transition
        class="mx-4 mt-4 rounded-lg border border-sky-200 bg-sky-50 p-4 shadow-sm dark:border-sky-500/30 dark:bg-sky-500/10 md:mx-6 md:mt-6"
    >
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-sky-600 shadow-sm dark:bg-sky-500/15 dark:text-sky-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 01-6 0m10-7a8.9 8.9 0 00-2.6-5.8M5.6 4.2A8.9 8.9 0 003 10" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Fitur Web Push Pindang OI sudah tersedia</p>
                    <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                        Aktifkan notifikasi browser agar pemberitahuan penugasan, pengiriman hasil kerja, persetujuan DL/Translok, dan pengumuman dapat muncul walaupun Anda sedang tidak membuka halaman PINDANG OI.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                <button type="button"
                    @click="openSettings()"
                    class="inline-flex items-center gap-2 rounded-md bg-sky-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 01-6 0" />
                    </svg>
                    Buka Pengaturan
                </button>
                <button type="button"
                    @click="open = true"
                    class="rounded-md border border-sky-200 bg-white px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:border-sky-500/30 dark:bg-transparent dark:text-sky-300 dark:hover:bg-sky-500/10">
                    Lihat Panduan
                </button>
                <button type="button"
                    @click="dismiss()"
                    class="rounded-md px-2 py-2 text-slate-400 hover:bg-white/70 hover:text-slate-600 dark:hover:bg-white/5 dark:hover:text-slate-200"
                    aria-label="Tutup pemberitahuan web push">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[99999]"
        role="dialog"
        aria-modal="true"
        aria-labelledby="web-push-guide-title"
    >
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>

        <div class="fixed inset-0 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 sm:items-center">
                <div
                    x-show="open"
                    x-transition
                    @click.away="open = false"
                    class="w-full max-w-4xl overflow-hidden rounded-lg border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-gray-900"
                >
                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5 dark:border-slate-800">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-sky-600 dark:text-sky-300">Panduan Fitur</p>
                            <h2 id="web-push-guide-title" class="mt-1 text-xl font-bold text-slate-950 dark:text-white">Web Push Notification</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                                Web push adalah notifikasi dari Pindang OI yang dapat muncul di browser atau sistem operasi. Fitur ini membantu pegawai menerima kabar penting tanpa harus terus membuka dropdown notifikasi.
                            </p>
                        </div>
                        <button type="button" @click="open = false" class="rounded-md p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-white/5 dark:hover:text-slate-200">
                            <span class="sr-only">Tutup panduan</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="grid gap-0 lg:grid-cols-[1fr_320px]">
                        <div class="space-y-5 px-6 py-5">
                            <section>
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Notifikasi Yang Akan Muncul</h3>
                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-800">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white">Anggota Tim</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-600 dark:text-slate-400">Penugasan baru, perubahan penugasan, pembatalan, status penerimaan, dan keputusan DL/Translok.</p>
                                    </div>
                                    <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-800">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white">Ketua Tim</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-600 dark:text-slate-400">Pengiriman hasil kerja anggota, pembatalan pengiriman, penghapusan kegiatan atau sub kegiatan terkait.</p>
                                    </div>
                                    <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-800">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white">Pimpinan</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-600 dark:text-slate-400">Pengajuan DL atau Translok yang menunggu persetujuan.</p>
                                    </div>
                                    <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-800">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white">Umum</p>
                                        <p class="mt-1 text-xs leading-5 text-slate-600 dark:text-slate-400">Pengumuman baru atau pengumuman yang diaktifkan kembali oleh admin.</p>
                                    </div>
                                </div>
                            </section>

                            <section>
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Cara Mengaktifkan</h3>
                                <ol class="mt-3 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                                    <li class="flex gap-3">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-sky-100 text-xs font-bold text-sky-700 dark:bg-sky-500/15 dark:text-sky-300">1</span>
                                        <span>Klik ikon lonceng di kanan atas, lalu buka tab <strong>Pengaturan</strong>.</span>
                                    </li>
                                    <li class="flex gap-3">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-sky-100 text-xs font-bold text-sky-700 dark:bg-sky-500/15 dark:text-sky-300">2</span>
                                        <span>Aktifkan tombol <strong>Web push browser</strong>, lalu pilih <strong>Allow/Izinkan</strong> saat browser meminta izin.</span>
                                    </li>
                                    <li class="flex gap-3">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-sky-100 text-xs font-bold text-sky-700 dark:bg-sky-500/15 dark:text-sky-300">3</span>
                                        <span>Klik <strong>Tes push</strong>. Jika muncul notifikasi tes, browser sudah siap menerima notifikasi.</span>
                                    </li>
                                </ol>
                            </section>

                            <section>
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Jika Notifikasi Tidak Muncul</h3>
                                <div class="mt-3 space-y-2 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                    <p>Pastikan izin notifikasi untuk situs ini tidak diblokir di Chrome, Edge, atau browser yang digunakan.</p>
                                    <p>Periksa pengaturan notifikasi Windows dan pastikan mode Focus Assist/Do Not Disturb tidak sedang menahan notifikasi.</p>
                                    <p>Jika memakai browser yang sama untuk akun berbeda, buka tab Pengaturan notifikasi dan aktifkan ulang agar subscription tersambung ke akun yang sedang login.</p>
                                </div>
                            </section>
                        </div>

                        <aside class="border-t border-slate-100 bg-slate-50 px-6 py-5 dark:border-slate-800 dark:bg-white/[0.03] lg:border-l lg:border-t-0">
                            <div class="rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-gray-900">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Status Browser Ini</p>
                                <div class="mt-3 space-y-2 text-sm">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-slate-500 dark:text-slate-400">Izin notifikasi</span>
                                        <span class="rounded-full px-2 py-1 text-xs font-semibold"
                                            :class="permission === 'granted' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : permission === 'denied' ? 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300'"
                                            x-text="permission === 'granted' ? 'Aktif' : permission === 'denied' ? 'Diblokir' : permission === 'unsupported' ? 'Tidak didukung' : 'Belum diizinkan'">
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-slate-500 dark:text-slate-400">Service worker</span>
                                        <span class="rounded-full px-2 py-1 text-xs font-semibold"
                                            :class="'serviceWorker' in navigator ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300'"
                                            x-text="'serviceWorker' in navigator ? 'Tersedia' : 'Tidak tersedia'">
                                        </span>
                                    </div>
                                </div>
                                <button type="button"
                                    @click="openSettings(); open = false"
                                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                                    Buka Pengaturan Lonceng
                                </button>
                            </div>

                            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-xs leading-5 text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
                                Notifikasi dropdown tetap menjadi arsip utama. Web push adalah pengingat cepat; jika popup tidak muncul karena pengaturan browser atau OS, notifikasinya tetap bisa dibaca melalui ikon lonceng.
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
