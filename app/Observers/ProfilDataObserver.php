<?php

namespace App\Observers;

use App\Models\Pegawai;
use Illuminate\Support\Facades\File;

class ProfilDataObserver
{
    public function saved(Pegawai $pegawai)
    {
        if (!$pegawai->photo) return;

        // SOURCE: storage/app/public/...
        $source = storage_path('app/public/' . $pegawai->photo);

        // DESTINATION: public/storage/...
        $publicRoot = public_path('storage');
        $dest = $publicRoot . '/' . $pegawai->photo;

        if (file_exists($source) && !file_exists($dest)) {
            File::ensureDirectoryExists(dirname($dest));
            File::copy($source, $dest);
        }
    }
}
