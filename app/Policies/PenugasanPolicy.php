<?php

namespace App\Policies;

use App\Models\Pegawai;
use App\Models\Penugasan;
use Illuminate\Auth\Access\Response;

class PenugasanPolicy
{

    /**
     * Anggota yang DITUGASKAN saja
     */
    public function access(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return
            $pegawai->isAnggota() &&
            $penugasan->id_anggota === $pegawai->id_pegawai;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Pegawai $pegawai): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Pegawai $pegawai): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return false;
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
