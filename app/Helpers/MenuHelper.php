<?php

namespace App\Helpers;

use App\Models\Bidang;
use Illuminate\Support\Facades\Auth;

class MenuHelper
{
    public static function getMainNavItems()
    {
        /** @var \App\Models\Pegawai|null $user */
        $user = Auth::user();
        $activeRole = self::getActiveRoleName();

        $menus = [
            [
                'icon' => 'dashboard',
                'name' => 'Dashboard',
                'path' => '/',
            ],
            [
                'icon' => 'capaian-kinerja',
                'name' => 'CKP Saya',
                'path' => '/ckp-pegawai',
            ],
            [
                'icon' => 'admin',
                'name' => 'Admin',
                'subItems' => [
                    ['icon' => 'admin', 'name' => 'Kelola Bidang Kerja', 'path' => '/bidang-kerja'],
                    ['icon' => 'admin', 'name' => 'Kelola Jenis Kegiatan', 'path' => '/jenis-kegiatan'],
                    ['icon' => 'admin', 'name' => 'Kelola Role Pegawai', 'path' => '/role-pegawai'],
                    ['icon' => 'admin','name' => 'Kelola Pengumuman', 'path' => '/announcements'],
                    ['icon' => 'admin', 'name' => 'Kelola Link Sidebar', 'path' => '/sidebar-links'],
                ],
            ],
            [
                'icon' => 'pimpinan',
                'name' => 'Pimpinan',
                'subItems' => [
                    ['icon' => 'pimpinan', 'name' => 'RK & IKI Pimpinan', 'path' => '/rencana-indikator-jpt/rencana'],
                    ['icon' => 'pimpinan', 'name' => 'Agenda Pimpinan', 'path' => '/agenda-pimpinan'],
                    ['icon' => 'pimpinan', 'name' => 'Laporan CKP Pegawai', 'path' => '/laporan-ckp-pegawai'],
                ],
            ],
            [
                'icon' => 'rencana-kerja',
                'name' => 'Rencana Kinerja',
                'subItems' => [
                    ['icon' => 'rencana-kerja', 'name' => 'Rencana Kerja Per Fungsi', 'path' => '/master-kegiatan'],
                    ['icon' => 'rencana-kerja', 'name' => 'Rencana Kerja Perlu DL/Translok', 'path' => '/rencana-kerja-dl'],
                ],
            ],
            [
                'icon' => 'tagihan-kerja',
                'name' => 'Tagihan Kerja',
                'subItems' => Bidang::getNavItems()
            ],
            [
                'icon' => 'kalender',
                'name' => 'Kalender',
                'subItems' => [
                    ['icon' => 'kalender', 'name' => 'Kalender DL / Translok', 'path' => '/kalender-dl'],
                    ['icon' => 'kalender', 'name' => 'Kalender Kegiatan', 'path' => '/kalender-kegiatan'],
                ],
            ],
            [
                'icon' => 'kalender',
                'name' => 'Panduan Pengguna',
                'path' => '/panduan-pengguna',
            ],

        ];

        if (!$user || !$activeRole) {
            return array_map(
                fn($menu) => self::normalizeMenuItem($menu),
                array_filter($menus, fn($m) => $m['name'] === 'Dashboard')
            );
        }

        // Filter subItems based on Gates
        foreach ($menus as &$menu) {
            if ($menu['name'] === 'Admin' && isset($menu['subItems'])) {
                $menu['subItems'] = array_values(array_filter($menu['subItems'], function ($sub) {
                    if ($sub['path'] === '/announcements') {
                        return \Illuminate\Support\Facades\Gate::allows('kelola-pengumuman');
                    }
                    return \Illuminate\Support\Facades\Gate::allows('kelola-master-data');
                }));
            }
            if ($menu['name'] === 'Pimpinan' && isset($menu['subItems'])) {
                $menu['subItems'] = array_values(array_filter($menu['subItems'], function ($sub) {
                    return \Illuminate\Support\Facades\Gate::allows('kelola-master-data');
                }));
            }
        }
        unset($menu);

        // Remove menu groups whose subItems became empty
        $menus = array_filter($menus, function ($m) {
            if (isset($m['subItems'])) {
                return !empty($m['subItems']);
            }
            return true;
        });

        // ADMIN dan PIMPINAN → semua menu kecuali CKP Saya
        if ($user->isActiveRole('Admin')) {
            return array_values(array_map(
                fn($menu) => self::normalizeMenuItem($menu),
                array_filter($menus, fn($menu) => $menu['name'] !== "CKP Saya")
            ));
        }

        if ($user->isActiveRole('Pimpinan')) {
            return array_map(
                fn($menu) => self::normalizeMenuItem($menu),
                array_filter($menus, fn($menu) => $menu['name'])
            );
        }

        $allowed = $user->isActiveRole('Ketua Tim')
            ? ['Dashboard', 'Tagihan Kerja', 'Rencana Kinerja', 'Kalender', 'Panduan Pengguna', 'CKP Saya']
            : ['Dashboard', 'Rencana Kinerja', 'Tagihan Kerja', 'Kalender', 'Panduan Pengguna', 'CKP Saya'];

        if (\Illuminate\Support\Facades\Gate::allows('kelola-pengumuman')) {
            $allowed[] = 'Admin';
        }

        return array_map(
            fn($menu) => self::normalizeMenuItem($menu),
            array_filter($menus, fn($m) => in_array($m['name'], $allowed))
        );
    }

    public static function getOthersItems()
    {
        $links = \App\Models\SidebarLink::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order', 'asc')
            ->get();

        return $links->map(function ($link) {
            $item = [
                'icon' => $link->icon,
                'name' => $link->name,
                'is_external' => true,
                'is_special' => (bool) $link->is_special,
                'color' => $link->color,
                'background_color' => $link->background_color,
            ];

            if ($link->url) {
                $item['path'] = $link->url;
            }

            if ($link->children->isNotEmpty()) {
                $item['subItems'] = $link->children->map(function ($child) {
                    return [
                        'icon' => $child->icon,
                        'name' => $child->name,
                        'path' => $child->url,
                        'is_external' => true,
                        'is_special' => (bool) $child->is_special,
                        'color' => $child->color,
                        'background_color' => $child->background_color,
                    ];
                })->toArray();
            }

            return $item;
        })->toArray();
    }

    public static function getMenuGroups()
    {
        return [
            [
                'title' => 'Menu',
                'items' => self::getMainNavItems()
            ],
            [
                'title' => 'Informasi',
                'items' => self::getOthersItems()
            ]
        ];
    }

    private static function getActiveRoleName(): ?string
    {
        /** @var \App\Models\Pegawai|null $user */
        $user = Auth::user();
        return $user->getActiveRole();
    }


    /**
     * ===============================
     * NORMALIZE MENU (FINAL VERSION)
     * ===============================
     */
    private static function normalizeMenuItem(array $item): array
    {

        if (!empty($item['is_external'])) {
            $item['is_active'] = false;
            return $item;
        }

        $currentPath = trim(request()->path(), '/');

        // ===============================
        // MENU TANPA SUB
        // ===============================
        if (isset($item['path']) && empty($item['subItems'])) {

            // JANGAN OVERRIDE is_active JIKA SUDAH ADA (Bidang)
            if (!isset($item['is_active'])) {
                if ($item['path'] === '/') {
                    $item['is_active'] = $currentPath === '';
                } else {
                    $itemPath = trim($item['path'], '/');
                    $item['is_active'] = request()->is($itemPath . '*');
                }
            }

            return $item;
        }

        // ===============================
        // MENU DENGAN SUB
        // ===============================
        if (!empty($item['subItems'])) {

            $item['subItems'] = array_map(
                fn($sub) => self::normalizeMenuItem($sub),
                $item['subItems']
            );

            // 🔥 PARENT ACTIVE JIKA ADA CHILD ACTIVE
            $hasActiveChild = collect($item['subItems'])
                ->contains(fn($sub) => $sub['is_active'] === true);

            $item['is_active'] = $hasActiveChild;
            $item['is_open'] = $hasActiveChild;
        }

        return $item;
    }

    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill="currentColor"></path></svg>',
            'rencana-kerja' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M12 12H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M12 16H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M9 9H9.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M9 13H9.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M9 17H9.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M9 3C9 2.44772 9.44772 2 10 2H14C14.5523 2 15 2.44772 15 3V5H9V3Z" stroke="currentColor" stroke-width="1.5"/>
                <rect x="3" y="9" width="18" height="12" rx="1" stroke="currentColor" stroke-width="1.5"/>
                </svg>',
            'tagihan-kerja' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 8V5L19 2L21 4L22 3L20 1L17 4H14V8H16Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                <path d="M22 12V19C22 20.1046 21.1046 21 20 21H4C2.89543 21 2 20.1046 2 19V5C2 3.89543 2.89543 3 4 3H12" stroke="currentColor" stroke-width="1.5"/>
                <rect x="7" y="7" width="10" height="1" rx="0.5" fill="currentColor"/>
                <rect x="7" y="10" width="10" height="1" rx="0.5" fill="currentColor"/>
                <rect x="7" y="13" width="7" height="1" rx="0.5" fill="currentColor"/>
                <rect x="7" y="16" width="10" height="1" rx="0.5" fill="currentColor"/>
                <circle cx="18" cy="18" r="1" fill="currentColor"/>
                </svg>',
            'capaian-kinerja' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3L13.5 7.5L18 8L14.5 11L16 15.5L12 13L8 15.5L9.5 11L6 8L10.5 7.5L12 3Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                <path d="M20 21H4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M18 17L20 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M6 17L4 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <rect x="10" y="17" width="4" height="4" rx="1" stroke="currentColor" stroke-width="1.5"/>
                <circle cx="12" cy="19" r="0.75" fill="currentColor"/>
                </svg>',
            'kalender' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/>
                <path d="M16 2V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M8 2V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M3 10H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <circle cx="7.5" cy="14.5" r="0.5" fill="currentColor"/>
                <circle cx="10.5" cy="14.5" r="0.5" fill="currentColor"/>
                <circle cx="13.5" cy="14.5" r="0.5" fill="currentColor"/>
                <circle cx="16.5" cy="14.5" r="0.5" fill="currentColor"/>
                <circle cx="7.5" cy="17.5" r="0.5" fill="currentColor"/>
                <circle cx="10.5" cy="17.5" r="0.5" fill="currentColor"/>
                <circle cx="13.5" cy="17.5" r="0.5" fill="currentColor"/>
                <circle cx="16.5" cy="17.5" r="0.5" fill="currentColor"/>
                </svg>',
            'admin' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 12C14.2091 12 16 10.2091 16 8C16 5.79086 14.2091 4 12 4C9.79086 4 8 5.79086 8 8C8 10.2091 9.79086 12 12 12Z" stroke="currentColor" stroke-width="1.5"/>
                <path d="M20 18C20 15.7909 18.2091 14 16 14H8C5.79086 14 4 15.7909 4 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M12 2L16 4.5V7.5C16 10 14 12.5 12 14C10 12.5 8 10 8 7.5V4.5L12 2Z" stroke="currentColor" stroke-width="1.5"/>
                </svg>',
            'pimpinan' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="1.5"/>
                <path d="M5 20C5 16.134 8.13401 14 12 14C15.866 14 19 16.134 19 20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M12 2L13.5 5.5L17 6L14.5 8.5L15 12L12 10.5L9 12L9.5 8.5L7 6L10.5 5.5L12 2Z" stroke="currentColor" stroke-width="1.5" fill="none"/>
                </svg>',
            'announcement' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>',
        ];

        if (array_key_exists($iconName, $icons)) {
            return $icons[$iconName];
        }

        return '<i data-lucide="' . e($iconName) . '" class="w-5 h-5 shrink-0"></i>';
    }
}
