<div class="relative" x-data="{
    dropdownOpen: false,
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
    },
    closeDropdown() {
        this.dropdownOpen = false;
    }
}" @click.away="closeDropdown()">

    <!-- User Button -->
    <button
        class="flex items-center text-gray-700 dark:text-gray-400"
        @click.prevent="toggleDropdown()"
        type="button"
    >
        <span class="mr-3 overflow-hidden rounded-full h-11 w-11">
            <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/user/userlogodefault.png') }}"
                class="h-full w-full object-cover"/>
        </span>

        <span class="block mr-1 font-medium text-theme-sm">
            {{ Auth::user()->username }}
        </span>

        <!-- Chevron -->
        <svg
            class="w-5 h-5 transition-transform duration-200"
            :class="{ 'rotate-180': dropdownOpen }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 9l-7 7-7-7">
            </path>
        </svg>
    </button>

    <!-- Dropdown -->
    <div
        x-show="dropdownOpen"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-3 w-[280px] rounded-xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800 z-50"
        style="display: none;"
    >

        <!-- User Info -->
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 overflow-hidden rounded-full border border-gray-200 dark:border-gray-600">
                    <img 
                        src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/user/userlogodefault.png') }}"
                        class="h-full w-full object-cover"
                        alt="{{ Auth::user()->username }}"
                    />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 dark:text-white text-sm truncate">
                        {{ Auth::user()->nama_pegawai }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                        {{ Auth::user()->email }}
                    </p>
                </div>
            </div>
        </div>

        @php
            $user = Auth::user();
            $roles = $user->roles->pluck('nama_role')->toArray();
            $isAnggotaTim = $user->penugasanSebagaiAnggota()->exists();

            if ($isAnggotaTim) {
                $roles[] = 'Anggota Tim';
            }

            $roles = array_values(array_unique($roles));
            $activeRole = $user->active_role ?? null;
            $hasRole = !empty($roles);
        @endphp

        <!-- Switch Role -->
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    @if(!$hasRole)
                        STATUS ROLE
                    @elseif(count($roles) > 1)
                        SWITCH ROLE
                    @else
                        ROLE AKTIF
                    @endif
                </span>
            </div>

            @if(!$hasRole)
                <!-- Tidak ada role -->
                <div class="text-sm text-gray-500 dark:text-gray-400 italic px-1 py-2">
                    Belum ada role aktif
                </div>

            @elseif(count($roles) > 1)
                <!-- Multi role -->
                <div class="space-y-2">
                    @foreach($roles as $role)
                        <form method="POST" action="{{ route('pegawai-role.switchRolePegawai', $role) }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full text-left px-4 py-3 rounded-lg text-sm transition-colors duration-150
                                    {{ $activeRole === $role
                                        ? 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800'
                                        : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 border border-transparent hover:border-gray-200 dark:hover:border-gray-700'
                                    }}"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="font-medium">{{ $role }}</span>
                                    @if($activeRole === $role)
                                        <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </button>
                        </form>
                    @endforeach
                </div>

            @else
                <!-- Single role -->
                <div class="px-4 py-3 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">{{ $roles[0] }}</span>
                        <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            @endif
        </div>

        <!-- Menu -->
        <div class="px-2 py-2">
            <a
                href="{{ route('profile') }}"
                class="flex items-center gap-3 px-4 py-3.5 rounded-lg text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors duration-150"
            >
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="font-medium">Edit Profile</span>
            </a>
        </div>

        <!-- Logout -->
        <div class="px-2 py-2 border-t border-gray-100 dark:border-gray-700">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button
                    type="submit"
                    class="flex items-center gap-3 w-full px-4 py-3.5 rounded-lg text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors duration-150"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="font-medium">Log Out</span>
                </button>
            </form>
        </div>
    </div>
</div>