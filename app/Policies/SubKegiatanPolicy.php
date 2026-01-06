<?php

namespace App\Policies;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use Illuminate\Auth\Access\Response;

class SubKegiatanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Pegawai $pegawai): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Pegawai $pegawai, SubKegiatan $SubKegiatan): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return false;
    }
}
