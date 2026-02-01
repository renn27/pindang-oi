<?php

namespace App\Observers;

use App\Models\Pegawai;
use Illuminate\Support\Facades\File;

class ProfilDataObserver
{
    public function updated(Pegawai $pegawai)
    {
        /**
         * Jalankan hanya jika photo berubah
         */
        if (!$pegawai->wasChanged('photo')) {
            return;
        }

        /**
         * Ambil foto lama
         */
        $oldPhoto = $pegawai->getOriginal('photo');

        if ($oldPhoto) {

            $oldStorage = storage_path('app/public/' . $oldPhoto);
            $oldPublic  = public_path('storage/' . $oldPhoto);

            if (file_exists($oldStorage)) {
                File::delete($oldStorage);
            }

            if (file_exists($oldPublic)) {
                File::delete($oldPublic);
            }
        }

        /**
         * Copy foto baru ke public
         */
        if (!$pegawai->photo) return;

        $source = storage_path('app/public/' . $pegawai->photo);
        $dest   = public_path('storage/' . $pegawai->photo);

        if (file_exists($source)) {

            File::ensureDirectoryExists(dirname($dest));

            File::copy($source, $dest);
        }
    }
}
