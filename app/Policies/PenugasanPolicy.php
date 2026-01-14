<?php

namespace App\Policies;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use App\Models\Penugasan;
use Illuminate\Auth\Access\Response;

class PenugasanPolicy
{

    protected function isKetuaOwner(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return
            $pegawai->hasRole('Ketua Tim') &&
            $pegawai->id_pegawai === $penugasan->subKegiatan->kegiatan->id_penanggung_jawab;
    }

    protected function isAssignedAnggota(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return $pegawai->id_pegawai === $penugasan->id_anggota;
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
    public function view(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return
            $this->isKetuaOwner($pegawai, $penugasan) ||
            $this->isAssignedAnggota($pegawai, $penugasan);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return
            $pegawai->hasRole('Ketua Tim') &&
            $pegawai->id_pegawai === $subKegiatan->kegiatan->id_penanggung_jawab;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return
            $pegawai->hasRole('Ketua Tim') &&
            $pegawai->id_pegawai === $penugasan->subKegiatan->kegiatan->id_penanggung_jawab;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return $this->isKetuaOwner($pegawai, $penugasan);
    }

    // === PENGIRIMAN ===
    public function send(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return $this->isAssignedAnggota($pegawai, $penugasan);
    }

    // === PENERIMAAN ===
    public function receive(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return $this->isKetuaOwner($pegawai, $penugasan);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return false;
    }
}
