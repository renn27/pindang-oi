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
        $this->copyImageToPublic($announcement);
    }

    /**
     * Handle the Announcement "updated" event.
     */
    public function updated(Announcement $announcement)
    {
        // Jalankan hanya jika image_path berubah
        if (!$announcement->wasChanged('image_path')) {
            return;
        }

        // Hapus gambar lama
        $oldImage = $announcement->getOriginal('image_path');
        if ($oldImage) {
            $this->deleteImage($oldImage);
        }

        // Copy gambar baru ke public
        $this->copyImageToPublic($announcement);
    }

    /**
     * Handle the Announcement "deleted" event.
     */
    public function deleted(Announcement $announcement)
    {
        // Hapus gambar saat announcement dihapus
        if ($announcement->image_path) {
            $this->deleteImage($announcement->image_path);
        }
    }

    /**
     * Copy gambar dari storage ke public
     */
    private function copyImageToPublic(Announcement $announcement)
    {
        if (!$announcement->image_path) {
            return;
        }

        $source = storage_path('app/public/' . $announcement->image_path);
        $dest = public_path('storage/' . $announcement->image_path);

        if (file_exists($source)) {
            // Pastikan direktori tujuan ada
            File::ensureDirectoryExists(dirname($dest));
            
            // Copy file
            File::copy($source, $dest);
        }
    }

    /**
     * Hapus gambar dari storage dan public
     */
    private function deleteImage($imagePath)
    {
        if (!$imagePath) {
            return;
        }

        $storagePath = storage_path('app/public/' . $imagePath);
        $publicPath = public_path('storage/' . $imagePath);

        // Hapus dari storage
        if (file_exists($storagePath)) {
            File::delete($storagePath);
        }

        // Hapus dari public
        if (file_exists($publicPath)) {
            File::delete($publicPath);
        }

        // Hapus juga direktori jika kosong (opsional)
        $this->cleanupEmptyDirectories(dirname($storagePath));
        $this->cleanupEmptyDirectories(dirname($publicPath));
    }

    /**
     * Bersihkan direktori kosong
     */
    private function cleanupEmptyDirectories($directory)
    {
        // Jangan hapus direktori root storage
        $rootStorage = storage_path('app/public/announcements');
        $rootPublic = public_path('storage/announcements');
        
        if ($directory === $rootStorage || $directory === $rootPublic) {
            return;
        }

        if (is_dir($directory) && count(File::files($directory)) === 0 && count(File::directories($directory)) === 0) {
            File::deleteDirectory($directory);
        }
    }
}