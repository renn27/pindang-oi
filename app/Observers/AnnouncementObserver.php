<?php

namespace App\Observers;

use App\Models\Announcement;
use Illuminate\Support\Facades\File;

class AnnouncementObserver
{
    /**
     * Handle the Announcement "created" event.
     */
    public function created(Announcement $announcement)
    {
        // kalau tidak ada file, skip
        if (!$announcement->image_path) {
            return;
        }

        $source = storage_path('app/public/' . $announcement->image_path);
        $dest   = public_path('storage/' . $announcement->image_path);

        if (file_exists($source)) {

            // pastikan folder tujuan ada
            File::ensureDirectoryExists(dirname($dest));

            // copy ke public
            File::copy($source, $dest);
        }
    }

    /**
     * Handle the Announcement "updated" event.
     */
    public function updated(Announcement $announcement)
    {
        // jalan hanya kalau image berubah
        if (!$announcement->wasChanged('image_path')) {
            return;
        }

        // hapus file lama
        $oldImage = $announcement->getOriginal('image_path');

        if ($oldImage) {

            $oldStorage = storage_path('app/public/' . $oldImage);
            $oldPublic  = public_path('storage/' . $oldImage);

            if (file_exists($oldStorage)) {
                File::delete($oldStorage);
            }

            if (file_exists($oldPublic)) {
                File::delete($oldPublic);
            }
        }

        // copy file baru
        if (!$announcement->image_path) return;

        $source = storage_path('app/public/' . $announcement->image_path);
        $dest   = public_path('storage/' . $announcement->image_path);

        if (file_exists($source)) {
            File::ensureDirectoryExists(dirname($dest));
            File::copy($source, $dest);
        }
    }

    public function deleted(Announcement $announcement)
    {
        if (!$announcement->image_path) {
            return;
        }

        $path = $announcement->image_path;

        $storagePath = storage_path('app/public/' . $path);
        $publicPath  = public_path('storage/' . $path);

        // hapus dari storage
        if (file_exists($storagePath)) {
            File::delete($storagePath);
        }

        // hapus dari public (kalau kamu pakai copy manual)
        if (file_exists($publicPath)) {
            File::delete($publicPath);
        }
    }

    // /**
    //  * Handle the Announcement "deleted" event.
    //  */
    // public function deleted(Announcement $announcement)
    // {
    //     // Hapus gambar saat announcement dihapus
    //     if ($announcement->image_path) {
    //         $this->deleteImage($announcement->image_path);
    //     }
    // }

    // /**
    //  * Copy gambar dari storage ke public
    //  */
    // private function copyImageToPublic(Announcement $announcement)
    // {
    //     if (!$announcement->image_path) {
    //         return;
    //     }

    //     $source = storage_path('app/public/' . $announcement->image_path);
    //     $dest = public_path('storage/' . $announcement->image_path);

    //     if (file_exists($source)) {
    //         // Pastikan direktori tujuan ada
    //         File::ensureDirectoryExists(dirname($dest));
            
    //         // Copy file
    //         File::copy($source, $dest);
    //     }
    // }

    // /**
    //  * Hapus gambar dari storage dan public
    //  */
    // private function deleteImage($imagePath)
    // {
    //     if (!$imagePath) {
    //         return;
    //     }

    //     $storagePath = storage_path('app/public/' . $imagePath);
    //     $publicPath = public_path('storage/' . $imagePath);

    //     // Hapus dari storage
    //     if (file_exists($storagePath)) {
    //         File::delete($storagePath);
    //     }

    //     // Hapus dari public
    //     if (file_exists($publicPath)) {
    //         File::delete($publicPath);
    //     }

    //     // Hapus juga direktori jika kosong (opsional)
    //     $this->cleanupEmptyDirectories(dirname($storagePath));
    //     $this->cleanupEmptyDirectories(dirname($publicPath));
    // }

    // /**
    //  * Bersihkan direktori kosong
    //  */
    // private function cleanupEmptyDirectories($directory)
    // {
    //     // Jangan hapus direktori root storage
    //     $rootStorage = storage_path('app/public/announcements');
    //     $rootPublic = public_path('storage/announcements');
        
    //     if ($directory === $rootStorage || $directory === $rootPublic) {
    //         return;
    //     }

    //     if (is_dir($directory) && count(File::files($directory)) === 0 && count(File::directories($directory)) === 0) {
    //         File::deleteDirectory($directory);
    //     }
    // }
}