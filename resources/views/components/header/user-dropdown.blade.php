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
        class="flex items-center text-gray-700"
        @click.prevent="toggleDropdown()"
        type="button"
    >
        <span class="mr-3 overflow-hidden rounded-full h-11 w-11">
            <img src="{{ Auth::user()->photo ? asset('storage/profile/' . Auth::user()->photo) : asset('images/user/owner.png') }}"
                        alt="user" class="h-full w-full object-cover" />
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
        class="absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg z-50"
        style="display: none;"
    >

        <!-- User Info -->
        <div>
            <span class="block font-medium text-gray-700 text-theme-sm">
                {{ Auth::user()->nama_pegawai }}
            </span>
            <span class="mt-0.5 block text-theme-xs text-gray-500">
                {{ Auth::user()->email }}
            </span>
        </div>

        @php
            $user = Auth::user();

            // 1. Ambil role struktural (dari tabel roles)
            $roles = $user->roles
                ->pluck('nama_role')
                ->toArray();

            // 2. Cek apakah user terlibat penugasan (punya role Anggota Tim)
            // asumsi relasi: $user->penugasans
            $isAnggotaTim = $user->penugasanSebagaiAnggota()->exists();

            if ($isAnggotaTim) {
                $roles[] = 'Anggota Tim';
            }

            // 3. Hilangkan duplikat
            $roles = array_values(array_unique($roles));

            // 4. Role aktif
            $activeRole = $user->active_role ?? null;

            // 5. Flag
            $hasRole = !empty($roles);
        @endphp

        <!-- Switch Role -->
        <div class="mt-3 border-t border-gray-200 pt-3">
            <span class="block mb-2 text-xs font-semibold text-gray-500">
                @if(!$hasRole)
                    Belum Ada Role Aktif
                @elseif(count($roles) > 1)
                    Switch Role
                @else
                    Role Aktif
                @endif
            </span>

            @if(!$hasRole)
                <!-- 1. BELUM ADA ROLE SAMA SEKALI -->
                <div class="px-3 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-500 italic">
                    Belum Ada Role Aktif
                </div>

            @elseif(count($roles) > 1)
                <!-- 2. MULTI ROLE (Admin / Ketua Tim / Anggota Tim, dll) -->
                <ul class="space-y-1">
                    @foreach($roles as $role)
                        <li>
                            <form method="POST" action="{{ route('pegawai-role.switchRolePegawai', $role) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium
                                        {{ $activeRole === $role
                                            ? 'bg-green-100 text-green-700'
                                            : 'hover:bg-gray-100 text-gray-700'
                                        }}"
                                >
                                    {{ $role }}
                                    @if($activeRole === $role)
                                        <span class="ml-2 text-xs">(active)</span>
                                    @endif
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>

            @else
                <!-- 3. SINGLE ROLE -->
                <div class="px-3 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-700">
                    {{ $roles[0] }}
                    <span class="ml-2 text-xs">(active)</span>
                </div>
            @endif

        </div>

        <!-- Menu -->
        <ul class="flex flex-col gap-1 pt-4 pb-3 border-b border-gray-200">
            <li>
                <a
                    href="{{ route('profile') }}"
                    class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100"
                >
                    <span class="text-gray-500 group-hover:text-gray-700">
                        <!-- icon -->
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 17.5228 6.47715 20.5 12 20.5C17.5228 20.5 20.5 17.5228 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5Z"
                                fill="currentColor"/>
                        </svg>
                    </span>
                    Edit Profile
                </a>
            </li>
        </ul>

        <!-- Logout -->
        <div class="flex items-center w-full gap-3 px-3 py-2 mt-3 font-medium text-gray-700 rounded-lg hover:bg-gray-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="text-theme-sm font-medium"
                >
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>
