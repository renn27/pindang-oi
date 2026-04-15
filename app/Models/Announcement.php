<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

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

    // Get image URL
    public function getImageUrlAttribute()
    {
        return $this->image_path 
            ? asset('storage/' . $this->image_path) 
            : null;
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