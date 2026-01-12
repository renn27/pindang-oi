<?php

namespace App\Policies;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use Illuminate\Auth\Access\Response;

class SubKegiatanPolicy
{
    /**
     * Hanya ketua tim PEMILIK kegiatan dan Sub Kegiatan di dalamnya
     */
    protected function isOwner(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return
            $pegawai->hasRole('Ketua Tim') &&
            $pegawai->id_pegawai === $subKegiatan->kegiatan->id_penanggung_jawab;
    }
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
     * Determine whether the user can create the model.
     */
    public function create(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return $this->isOwner($pegawai, $subKegiatan);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return $this->isOwner($pegawai, $subKegiatan);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return $this->isOwner($pegawai, $subKegiatan);
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
