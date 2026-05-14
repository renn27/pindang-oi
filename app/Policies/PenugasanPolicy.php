<?php

namespace App\Policies;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use App\Models\Penugasan;
class PenugasanPolicy
{

    protected function canManagePenugasan(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return
            in_array($pegawai->active_role, ['Admin', 'Pimpinan'], true)
            || (
                $pegawai->active_role === 'Ketua Tim'
                && $pegawai->id_pegawai === $penugasan->subKegiatan->kegiatan->id_penanggung_jawab
            );
    }

    protected function isAssignedAnggota(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return
            $pegawai->active_role === 'Anggota Tim'
            && $pegawai->id_pegawai === $penugasan->id_anggota;
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
            $this->canManagePenugasan($pegawai, $penugasan) ||
            $this->isAssignedAnggota($pegawai, $penugasan);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Pegawai $pegawai, SubKegiatan $subKegiatan): bool
    {
        return
            in_array($pegawai->active_role, ['Admin', 'Pimpinan'], true)
            || (
                $pegawai->active_role === 'Ketua Tim'
                && $pegawai->id_pegawai === $subKegiatan->kegiatan->id_penanggung_jawab
            );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateJenisKegiatan(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        // 1️⃣ Tidak berhak kelola
        if (! $this->canManagePenugasan($pegawai, $penugasan)) {
            return false;
        }

        // 2️⃣ Hanya boleh jika id_jenis_kegiatan kosong (null, 0, '')
        if (!empty($penugasan->id_jenis_kegiatan)) {
            return false;
        }

        // 3️⃣ Hanya boleh diakses jika tombol Edit Penugasan normal sudah hidden/terkunci
        // Tombol normal terkunci jika:
        // a) Sudah diterima
        // b) Sudah masuk kalender DL (ACC)
        $isLocked = ($penugasan->latestPenerimaan?->status === 'Diterima') || $penugasan->sudahMasukKalenderDL();

        return $isLocked;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        // 1️⃣ Tidak berhak kelola
        if (! $this->canManagePenugasan($pegawai, $penugasan)) {
            return false;
        }

        // 2️⃣ Sudah masuk kalender DL → TIDAK BOLEH EDIT
        if ($penugasan->sudahMasukKalenderDL()) {
            return false;
        }

        // 3️⃣ Sudah diterima
        if ($penugasan->latestPenerimaan?->status === 'Diterima') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return $this->canManagePenugasan($pegawai, $penugasan)
            && ! $penugasan->latestPengiriman
            && $penugasan->latestPenerimaan?->status !== 'Diterima'
            && ! $penugasan->kalenderDls()->exists();
    }

    // === PENGIRIMAN ===
    public function send(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        // Hanya cek identitas: apakah user adalah anggota yang ditugaskan?
        // Pengecekan boleh/tidaknya kirim (disabled state + tooltip) dilakukan di view
        // via bolehKirimPenugasan() agar button tetap muncul dengan pesan informatif.
        return $this->isAssignedAnggota($pegawai, $penugasan);
    }

    public function cancelSend(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        return $this->isAssignedAnggota($pegawai, $penugasan);
    }

    // === PENERIMAAN ===
    public function receive(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        // Hanya cek identitas: apakah user berhak mengelola penugasan ini?
        // Disabled state + tooltip "Buat Penerimaan" diurus di view via bolehTerimaPenugasan()
        // agar button tetap muncul dengan pesan informatif (misal: belum ada pengiriman).
        return $this->canManagePenugasan($pegawai, $penugasan);
    }

    public function cancelReceive(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        // Hanya Ketua Tim yang merupakan penanggung jawab kegiatan dari sub kegiatan penugasan ini
        return $pegawai->active_role === 'Ketua Tim'
            && $pegawai->id_pegawai === $penugasan->subKegiatan->kegiatan->id_penanggung_jawab;
    }

    public function acceptDL(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        // 1️⃣ Penugasan memang butuh DL
        if (! $penugasan->butuh_dl) {
            return false;
        }

        // 2️⃣ Pimpinan: boleh ACC / Ditolak
        if ($pegawai->active_role === 'Pimpinan') {
            return true;
        }

        // 3️⃣ Ketua Tim (penanggung jawab kegiatan): boleh Ajukan Kembali (set → Menunggu)
        if (
            $pegawai->active_role === 'Ketua Tim'
            && $pegawai->id_pegawai === $penugasan->subKegiatan->kegiatan->id_penanggung_jawab
        ) {
            return true;
        }

        return false;
    }

    public function acceptTranslok(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        // 1️⃣ Penugasan memang butuh Translok
        if (! $penugasan->butuh_translok) {
            return false;
        }

        // 2️⃣ Pimpinan: boleh ACC / Ditolak
        if ($pegawai->active_role === 'Pimpinan') {
            return true;
        }

        // 3️⃣ Ketua Tim (penanggung jawab kegiatan): boleh Ajukan Kembali (set → Menunggu)
        if (
            $pegawai->active_role === 'Ketua Tim'
            && $pegawai->id_pegawai === $penugasan->subKegiatan->kegiatan->id_penanggung_jawab
        ) {
            return true;
        }

        return false;
    }

    public function setAsCKP(Pegawai $pegawai, Penugasan $penugasan): bool
    {
        // Validasi dasar: harus Anggota Tim dan pemilik penugasan
        if (
            $pegawai->active_role !== 'Anggota Tim' ||
            $penugasan->id_anggota !== $pegawai->id_pegawai
        ) {
            return false;
        }

        // Validasi: masih ada bulan pengiriman Diterima yang belum dijadikan CKP
        return $penugasan->jumlahCkpBelumDibuat() > 0;
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
