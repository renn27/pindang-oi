@php
    $user = auth()->user();
    $notificationTableReady = \Illuminate\Support\Facades\Schema::hasTable('notifications');
    $notifications = $user && $notificationTableReady
        ? $user->notifications()->latest()->limit(10)->get()
        : collect();
    $unreadNotifications = $notifications->filter(fn ($notification) => is_null($notification->read_at));
    $unreadCount = $user && $notificationTableReady ? $user->unreadNotifications()->count() : 0;
    $contextForNotification = function ($notification) {
        $data = $notification->data ?? [];
        $tag = $data['tag'] ?? '';

        if (! empty($data['role_context'])) {
            return $data['role_context'];
        }

        if (str_starts_with($tag, 'pengiriman-')) {
            return 'ketua_tim';
        }

        if (str_starts_with($tag, 'travel-pending-')) {
            return 'pimpinan';
        }

        if (
            str_starts_with($tag, 'penugasan-') ||
            str_starts_with($tag, 'penerimaan-') ||
            str_starts_with($tag, 'travel-')
        ) {
            return 'anggota';
        }

        if (str_starts_with($tag, 'announcement-')) {
            return 'umum';
        }

        return 'umum';
    };
    $ownedRoleNames = $user?->roles?->pluck('nama_role')->all() ?? [];
    $hasKetuaContext = $user && (
        in_array('Ketua Tim', $ownedRoleNames, true) ||
        $user->active_role === 'Ketua Tim' ||
        $user->kegiatanYangDipimpin()->exists()
    );
    $hasAnggotaContext = $user && (
        in_array('Anggota Tim', $ownedRoleNames, true) ||
        $user->active_role === 'Anggota Tim' ||
        $user->penugasanSebagaiAnggota()->exists()
    );
    $hasPimpinanContext = $user && (
        in_array('Pimpinan', $ownedRoleNames, true) ||
        $user->active_role === 'Pimpinan'
    );
    $availableRoleFilters = [
        'ketua_tim' => 'Ketua',
        'anggota' => 'Anggota',
        'pimpinan' => 'Pimpinan',
    ];
    $contextAllowed = function ($context) use ($hasKetuaContext, $hasAnggotaContext, $hasPimpinanContext) {
        return match ($context) {
            'ketua_tim' => $hasKetuaContext,
            'anggota' => $hasAnggotaContext,
            'pimpinan' => $hasPimpinanContext,
            default => false,
        };
    };
    $roleFilters = collect(['all' => 'Semua'])
        ->merge(collect($availableRoleFilters)->filter(function ($label, $context) use ($contextAllowed, $notifications, $contextForNotification) {
            return match ($context) {
                'ketua_tim', 'anggota', 'pimpinan' => $contextAllowed($context)
                    || $notifications->contains(fn ($notification) => $contextForNotification($notification) === $context),
                default => false,
            };
        }))
        ->merge(['umum' => 'Umum'])
        ->all();
    $allRoleCounts = collect($roleFilters)->mapWithKeys(function ($label, $context) use ($notifications, $contextForNotification) {
        return [$context => $context === 'all' ? $notifications->count() : $notifications->filter(fn ($notification) => $contextForNotification($notification) === $context)->count()];
    });
    $unreadRoleCounts = collect($roleFilters)->mapWithKeys(function ($label, $context) use ($unreadNotifications, $contextForNotification) {
        return [$context => $context === 'all' ? $unreadNotifications->count() : $unreadNotifications->filter(fn ($notification) => $contextForNotification($notification) === $context)->count()];
    });
@endphp

<div class="relative"
    x-data="{ dropdownOpen: false, activeTab: 'all', roleFilter: 'all' }"
    @click.away="dropdownOpen = false"
    @open-push-settings.window="dropdownOpen = true; activeTab = 'settings'; roleFilter = 'all'">
    <button
        class="relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        @click="dropdownOpen = !dropdownOpen"
        type="button"
        aria-label="Buka daftar notifikasi"
    >
        @if ($unreadCount > 0)
            <span class="absolute right-0 top-0.5 z-1 flex h-4 min-w-4 items-center justify-center">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-75"></span>
                <span class="relative flex h-4 min-w-4 items-center justify-center rounded-full bg-orange-500 px-1 text-[10px] font-semibold text-white shadow-sm">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            </span>
        @endif

        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
                fill="" />
        </svg>
    </button>

    <div
        x-show="dropdownOpen"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute -right-[146px] mt-[17px] flex h-[460px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900 sm:w-[380px] lg:right-0"
        style="display: none;"
    >
        <div class="mb-3 flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
            <div>
                <h5 class="text-base font-semibold text-gray-800 dark:text-white">Notifikasi</h5>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $unreadCount }} belum dibaca</p>
            </div>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">
                        Tandai dibaca
                    </button>
                </form>
            @endif
        </div>

        @unless ($notificationTableReady)
            <div class="mb-3 rounded-xl border border-warning-200 bg-warning-50 p-3 text-xs text-warning-700 dark:border-warning-500/30 dark:bg-warning-500/10 dark:text-warning-300">
                Tabel notifikasi belum tersedia. Jalankan migration agar daftar notifikasi aktif.
            </div>
        @endunless

        <div class="mb-3 grid grid-cols-3 rounded-xl bg-gray-100 p-1 text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">
            <button type="button"
                class="rounded-lg px-2 py-1.5 transition"
                :class="activeTab === 'all' ? 'bg-white text-gray-800 shadow-sm dark:bg-gray-900 dark:text-white' : 'hover:text-gray-700 dark:hover:text-white'"
                @click="activeTab = 'all'; roleFilter = 'all'"
            >
                Semua
            </button>
            <button type="button"
                class="rounded-lg px-2 py-1.5 transition"
                :class="activeTab === 'unread' ? 'bg-white text-gray-800 shadow-sm dark:bg-gray-900 dark:text-white' : 'hover:text-gray-700 dark:hover:text-white'"
                @click="activeTab = 'unread'; roleFilter = 'all'"
            >
                Belum dibaca
            </button>
            <button type="button"
                class="rounded-lg px-2 py-1.5 transition"
                :class="activeTab === 'settings' ? 'bg-white text-gray-800 shadow-sm dark:bg-gray-900 dark:text-white' : 'hover:text-gray-700 dark:hover:text-white'"
                @click="activeTab = 'settings'"
            >
                Pengaturan
            </button>
        </div>

        <div x-show="activeTab !== 'settings'" class="mb-2 flex flex-wrap gap-1" style="display: none;">
            @foreach ($roleFilters as $context => $label)
                <button type="button"
                    class="inline-flex h-7 items-center gap-1 rounded-full border px-2 text-[11px] font-medium transition"
                    :class="roleFilter === '{{ $context }}' ? 'border-brand-500 bg-brand-50 text-brand-700 dark:border-brand-400 dark:bg-brand-500/15 dark:text-brand-300' : 'border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800'"
                    @click="roleFilter = '{{ $context }}'"
                >
                    <span>{{ $label }}</span>
                    <span class="rounded-full bg-gray-100 px-1 text-[10px] leading-4 text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                        x-text="activeTab === 'unread' ? '{{ $unreadRoleCounts[$context] }}' : '{{ $allRoleCounts[$context] }}'"></span>
                </button>
            @endforeach
        </div>

        <ul x-show="activeTab === 'all'" class="custom-scrollbar min-h-0 flex-1 flex-col overflow-y-auto overflow-x-hidden">
            @forelse ($notifications as $notification)
                @php
                    $data = $notification->data ?? [];
                    $title = $data['title'] ?? 'Notifikasi';
                    $body = $data['body'] ?? 'Ada pembaruan baru.';
                    $isUnread = is_null($notification->read_at);
                    $roleContext = $contextForNotification($notification);
                    $roleLabel = $roleFilters[$roleContext] ?? 'Umum';
                @endphp

                <li x-show="roleFilter === 'all' || roleFilter === '{{ $roleContext }}'">
                    <a class="flex gap-3 rounded-xl border-b border-gray-100 px-3 py-3 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]"
                        href="{{ route('notifications.read', $notification->id) }}"
                    >
                        <span class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $isUnread ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875Z"
                                    fill="" />
                            </svg>
                        </span>

                        <span class="min-w-0 flex-1">
                            <span class="mb-1 block text-sm font-medium text-gray-800 dark:text-white">
                                {{ $title }}
                            </span>
                            <span class="line-clamp-2 block text-xs text-gray-500 dark:text-gray-400">
                                {{ $body }}
                            </span>
                            <span class="mt-1 block text-[11px] text-gray-400">
                                {{ $notification->created_at?->diffForHumans() }}
                                <span class="ml-1 rounded-full bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                    {{ $roleLabel }}
                                </span>
                            </span>
                        </span>

                        @if ($isUnread)
                            <span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-orange-500"></span>
                        @endif
                    </a>
                </li>
            @empty
                <li x-show="roleFilter === 'all'" class="flex flex-1 items-center justify-center px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                    Belum ada notifikasi.
                </li>
            @endforelse

            @foreach ($roleFilters as $context => $label)
                @if ($context !== 'all' && $allRoleCounts[$context] === 0)
                    <li x-show="roleFilter === '{{ $context }}'" class="flex flex-1 items-center justify-center px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400" style="display: none;">
                        Tidak ada notifikasi {{ strtolower($label) }}.
                    </li>
                @endif
            @endforeach
        </ul>

        <ul x-show="activeTab === 'unread'" class="custom-scrollbar min-h-0 flex-1 flex-col overflow-y-auto overflow-x-hidden" style="display: none;">
            @forelse ($unreadNotifications as $notification)
                @php
                    $data = $notification->data ?? [];
                    $title = $data['title'] ?? 'Notifikasi';
                    $body = $data['body'] ?? 'Ada pembaruan baru.';
                    $roleContext = $contextForNotification($notification);
                    $roleLabel = $roleFilters[$roleContext] ?? 'Umum';
                @endphp

                <li x-show="roleFilter === 'all' || roleFilter === '{{ $roleContext }}'">
                    <a class="flex gap-3 rounded-xl border-b border-gray-100 px-3 py-3 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]"
                        href="{{ route('notifications.read', $notification->id) }}"
                    >
                        <span class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875Z"
                                    fill="" />
                            </svg>
                        </span>

                        <span class="min-w-0 flex-1">
                            <span class="mb-1 block text-sm font-medium text-gray-800 dark:text-white">
                                {{ $title }}
                            </span>
                            <span class="line-clamp-2 block text-xs text-gray-500 dark:text-gray-400">
                                {{ $body }}
                            </span>
                            <span class="mt-1 block text-[11px] text-gray-400">
                                {{ $notification->created_at?->diffForHumans() }}
                                <span class="ml-1 rounded-full bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                    {{ $roleLabel }}
                                </span>
                            </span>
                        </span>

                        <span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-orange-500"></span>
                    </a>
                </li>
            @empty
                <li x-show="roleFilter === 'all'" class="flex flex-1 items-center justify-center px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                    Tidak ada notifikasi belum dibaca.
                </li>
            @endforelse

            @foreach ($roleFilters as $context => $label)
                @if ($context !== 'all' && $unreadRoleCounts[$context] === 0)
                    <li x-show="roleFilter === '{{ $context }}'" class="flex flex-1 items-center justify-center px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400" style="display: none;">
                        Tidak ada notifikasi {{ strtolower($label) }} yang belum dibaca.
                    </li>
                @endif
            @endforeach
        </ul>

        <div x-show="activeTab === 'settings'" class="custom-scrollbar min-h-0 flex-1 flex-col overflow-y-auto overflow-x-hidden" style="display: none;">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-3 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-white">Web push browser</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" data-push-status>Aktifkan agar notifikasi muncul di luar tab aplikasi.</p>
                    </div>

                    <button id="pushNotificationToggle"
                        class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full bg-gray-300 transition-colors dark:bg-gray-700 disabled:cursor-wait disabled:opacity-60"
                        type="button"
                        title="Aktifkan notifikasi browser"
                        aria-label="Aktifkan notifikasi browser"
                        data-enabled="false"
                    >
                        <span class="pointer-events-none inline-block h-5 w-5 translate-x-0.5 rounded-full bg-white shadow transition-transform"
                            data-push-toggle-thumb></span>
                    </button>
                </div>

                <button type="button" data-push-test
                    class="mt-3 rounded-md border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-white dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Tes push
                </button>
            </div>
        </div>
    </div>
</div>
