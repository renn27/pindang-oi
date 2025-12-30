<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bidang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bidangs';
    protected $primaryKey = 'id_bidang';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama_bidang',
        'slug',
        'detail_bidang',
    ];

    public static function getNavItems() {
        $currentBidang = request()->route('bidang');

        return self::whereNull('deleted_at')
            ->orderBy('nama_bidang')
            ->get()
            ->map(function ($bidang) use ($currentBidang) {
                $isActive = $currentBidang
                    && $currentBidang->slug === $bidang->slug;

                return [
                    'name'      => $bidang->nama_bidang,
                    'path'      => route('kegiatan.index', $bidang->slug),
                    'icon'      => 'dashboard',
                    'is_active' => $isActive, // 🔥 JANGAN DIUBAH LAGI
                ];
            })
            ->toArray();
    }

    // Generate slug from nama_bidang
    protected static function sluggable()
    {
        static::creating(function ($bidang) {
            if (empty($bidang->slug)) {
                $bidang->slug = Str::slug($bidang->nama_bidang);
            }
        });
    }

    // Relationships
    public function kegiatans() {
        return $this->hasMany(Kegiatan::class, 'id_bidang', 'id_bidang');
    }
}

