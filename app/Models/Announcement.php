<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image_path',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    // Scope untuk pengumuman aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('start_date', '<=', Carbon::now())
                     ->where('end_date', '>=', Carbon::now());
    }

    // Get image URL dengan fallback jika file tidak ada di public
    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        $publicPath = public_path('storage/' . $this->image_path);
        
        // Jika file tidak ada di public, coba copy dari storage
        if (!file_exists($publicPath)) {
            $storagePath = storage_path('app/public/' . $this->image_path);
            
            if (file_exists($storagePath)) {
                File::ensureDirectoryExists(dirname($publicPath));
                File::copy($storagePath, $publicPath);
            } else {
                return null;
            }
        }

        return asset('storage/' . $this->image_path);
    }

    // Format untuk ditampilkan di modal
    public function toModalFormat()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image_url,
            'content' => $this->content
        ];
    }
}