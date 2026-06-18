<?php

namespace Database\Seeders;

use App\Models\SidebarLink;
use Illuminate\Database\Seeder;

class SidebarLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        SidebarLink::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 1. Pengumuman (Tipe Mandiri - Khusus Lokal, tapi buka di tab baru sesuai instruksi)
        SidebarLink::create([
            'name' => 'Pengumuman',
            'url' => '/pengumuman',
            'icon' => 'megaphone',
            'sort_order' => 1,
            'is_external' => true,
            'is_special' => true,
        ]);

        // 2. BESTI OGAN ILIR
        SidebarLink::create([
            'name' => 'BESTI OGAN ILIR',
            'url' => 'https://besti.bpsoganilir.com/',
            'icon' => 'award',
            'sort_order' => 2,
            'is_external' => true,
        ]);

        // 3. GC PBI 2026
        SidebarLink::create([
            'name' => 'GC PBI 2026',
            'url' => 'https://gcpbi.bpsoganilir.com/',
            'icon' => 'globe',
            'sort_order' => 3,
            'is_external' => true,
        ]);

        // 4. GC PLN 2026
        SidebarLink::create([
            'name' => 'GC PLN 2026',
            'url' => 'https://gcpln.bpsoganilir.com/',
            'icon' => 'zap',
            'sort_order' => 4,
            'is_external' => true,
        ]);

        // 5. MUSI
        SidebarLink::create([
            'name' => 'MUSI',
            'url' => 'https://webapps.bps.go.id/sumsel/musi',
            'icon' => 'database',
            'sort_order' => 5,
            'is_external' => true,
        ]);

        // 6. SAKIP (Parent Group)
        $sakip = SidebarLink::create([
            'name' => 'SAKIP',
            'url' => null,
            'icon' => 'file-check',
            'sort_order' => 6,
            'is_external' => true,
        ]);

        // Sub-links SAKIP
        SidebarLink::create([
            'parent_id' => $sakip->id,
            'name' => 'Bukti Dukung SAKIP',
            'url' => 'https://drive.google.com/drive/folders/1hbkLUr_y6KWMF_Hz2iZibHuyVbC2qiMv?usp=sharing',
            'icon' => 'folder-open',
            'sort_order' => 1,
            'is_external' => true,
        ]);

        SidebarLink::create([
            'parent_id' => $sakip->id,
            'name' => 'SINERGI',
            'url' => 'https://sinergi.web.bps.go.id/#/auth/login?next=/',
            'icon' => 'link',
            'sort_order' => 2,
            'is_external' => true,
        ]);

        // 7. SE 2026 (Parent Group) - Menggunakan warna orange kustom untuk mempertahankan visual
        $se = SidebarLink::create([
            'name' => 'SE 2026',
            'url' => null,
            'icon' => 'trending-up',
            'sort_order' => 7,
            'is_external' => true,
            'color' => '#ea580c',
        ]);

        // Sub-links SE 2026 - Menggunakan warna orange kustom
        SidebarLink::create([
            'parent_id' => $se->id,
            'name' => 'DASHBOARD SE2026',
            'url' => 'https://dashboard-se2026.apps.bps.go.id/se2026',
            'icon' => 'layout-dashboard',
            'sort_order' => 1,
            'is_external' => true,
            'color' => '#ea580c',
        ]);

        SidebarLink::create([
            'parent_id' => $se->id,
            'name' => 'MANGCEK SE2026',
            'url' => 'https://mangcek.bpsoganilir.com/admin',
            'icon' => 'check-square',
            'sort_order' => 2,
            'is_external' => true,
            'color' => '#ea580c',
        ]);

        SidebarLink::create([
            'parent_id' => $se->id,
            'name' => 'SEMPATI SE2026',
            'url' => 'https://se2026.bpssumsel.com/',
            'icon' => 'activity',
            'sort_order' => 3,
            'is_external' => true,
            'color' => '#ea580c',
        ]);

        SidebarLink::create([
            'parent_id' => $se->id,
            'name' => 'ASISTEN SE2026',
            'url' => 'https://asistense2026.bpsoganilir.com/',
            'icon' => 'bot',
            'sort_order' => 4,
            'is_external' => true,
            'color' => '#ea580c',
        ]);
    }
}
