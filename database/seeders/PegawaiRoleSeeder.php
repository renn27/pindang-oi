<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class PegawaiRoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin     = Role::find(2); // Admin
        $pimpinan  = Role::find(1); // Pimpinan

        $sukendro = Pegawai::where('username', 'sukendrosuryowiguno')->first();
        $ifone    = Pegawai::where('username', 'ifonearma')->first();

        if ($sukendro) {
            DB::table('pegawai_role')->insertOrIgnore([
                [
                    'pegawai_id' => $sukendro->id_pegawai,
                    'role_id' => $pimpinan->id,
                ],
                [
                    'pegawai_id' => $sukendro->id_pegawai,
                    'role_id' => $admin->id,
                ],
            ]);

            $sukendro->update(['active_role' => 'Pimpinan']);
        }

        if ($ifone) {
            DB::table('pegawai_role')->insertOrIgnore([
                'pegawai_id' => $ifone->id_pegawai,
                'role_id' => $admin->id,
            ]);

            $ifone->update(['active_role' => 'Admin']);
        }
    }
}
