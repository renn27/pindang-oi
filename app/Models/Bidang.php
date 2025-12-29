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

    protected $dates = [
        'deleted_at' // Tambahkan ini untuk soft delete
    ];

    // Method untuk mendapatkan data bidang dalam format array navbar
    public static function getNavItems()
    {
        $bidangs = self::whereNull('deleted_at')->orderBy('nama_bidang')->get();

        $navItems = [];
        foreach ($bidangs as $bidang) {
            $navItems[] = [
                'name' => $bidang->nama_bidang,
                'path' => '/bidang-kerja/' . $bidang->slug, // atau slug jika ada
                'icon' => 'dashboard', // default icon
            ];
        }

        return $navItems;
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

