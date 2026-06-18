<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BidangSeeder::class,
            RencanaJPTSeeder::class,
            IndikatorJPTSeeder::class,
            JenisKegiatanSeeder::class,
            RoleSeeder::class,
            PegawaiSeeder::class,
            PegawaiRoleSeeder::class,
            KegiatanSeeder::class,
            SubKegiatanSeeder::class,
            SidebarLinkSeeder::class,
        ]);
    }
}
