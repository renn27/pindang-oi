@php
    use App\Helpers\MenuHelper;
    $menuGroups = MenuHelper::getMenuGroups();
@endphp

<aside
    class="fixed top-0 left-0 h-screen w-[290px] bg-white border-r border-gray-200"
    x-data="{
        openSubmenus: {},
        toggle(key) {
            this.openSubmenus[key] = !this.openSubmenus[key];
        },
        isOpen(key, defaultOpen = false) {
            return this.openSubmenus[key] ?? defaultOpen;
        }
    }"
>
    <!-- LOGO -->
    <div class="p-6 font-bold text-lg">
        LOGO
    </div>

    <!-- MENU -->
    <nav class="px-4">
        @foreach ($menuGroups as $group)
            <div class="mb-6">
                <!-- GROUP TITLE -->
                <h2 class="text-xs text-gray-400 uppercase mb-3">
                    {{ $group['title'] }}
                </h2>

                <ul class="space-y-1">
                    @foreach ($group['items'] as $index => $item)
                        @php
                            $key = $group['title'].'-'.$index;
                        @endphp

                        {{-- ================= MENU WITH SUB ================= --}}
                        @if (!empty($item['subItems']))
                            <li>
                                <button
                                    @click="toggle('{{ $key }}')"
                                    class="menu-item w-full flex items-center px-3 py-2 rounded
                                        {{ $item['is_active']
                                            ? 'menu-item-active'
                                            : 'menu-item-inactive' }}"
                                >
                                    <span class="{{ $item['is_active']
                                        ? 'menu-item-icon-active'
                                        : 'menu-item-icon-inactive' }}">
                                        {!! MenuHelper::getIconSvg($item['icon']) !!}
                                    </span>

                                    <span class="ml-3 flex-1 text-left">
                                        {{ $item['name'] }}
                                    </span>

                                    <svg
                                        class="w-4 h-4 transition-transform"
                                        :class="isOpen('{{ $key }}', {{ $item['is_open'] ? 'true' : 'false' }})
                                            ? 'rotate-180'
                                            : ''"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <!-- SUB MENU -->
                                <ul
                                    x-show="isOpen('{{ $key }}', {{ $item['is_open'] ? 'true' : 'false' }})"
                                    x-transition
                                    class="mt-1 ml-8 space-y-1"
                                >
                                    @foreach ($item['subItems'] as $subItem)
                                        <li>
                                            <a
                                                href="{{ $subItem['path'] }}"
                                                class="menu-dropdown-item block px-3 py-2 rounded
                                                    {{ $subItem['is_active']
                                                        ? 'menu-dropdown-item-active'
                                                        : 'menu-dropdown-item-inactive' }}"
                                            >
                                                {{ $subItem['name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>

                        {{-- ================= MENU SINGLE ================= --}}
                        @else
                            <li>
                                <a
                                    href="{{ $item['path'] }}"
                                    class="menu-item flex items-center px-3 py-2 rounded
                                        {{ $item['is_active']
                                            ? 'menu-item-active'
                                            : 'menu-item-inactive' }}"
                                >
                                    <span class="{{ $item['is_active']
                                        ? 'menu-item-icon-active'
                                        : 'menu-item-icon-inactive' }}">
                                        {!! MenuHelper::getIconSvg($item['icon']) !!}
                                    </span>

                                    <span class="ml-3">
                                        {{ $item['name'] }}
                                    </span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>
</aside>
