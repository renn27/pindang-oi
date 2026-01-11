<?php

namespace App\Policies;

use App\Models\Kegiatan;
use App\Models\Pegawai;
use Illuminate\Auth\Access\Response;

class KegiatanPolicy
{
    /**
     * Hanya ketua tim PEMILIK kegiatan
     */
    protected function isOwner(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        return $pegawai->hasRole('Ketua Tim')
            && $pegawai->id_pegawai === $kegiatan->id_penanggung_jawab;
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
    public function view(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Pegawai $pegawai): bool
    {
        return $pegawai->hasRole('Ketua Tim', 'Pimpinan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        return $this->isOwner($pegawai, $kegiatan);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        return $this->isOwner($pegawai, $kegiatan);
    }

    /**
     * Determine whether the user can create sub kegiatan data.
     */
    public function createSubKegiatan(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        return $this->isOwner($pegawai, $kegiatan);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Pegawai $pegawai, Kegiatan $kegiatan): bool
    {
        return false;
    }
}
