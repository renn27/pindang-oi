<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bidang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bidangs';
    protected $primaryKey = 'id_bidang';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama_bidang'
    ];

    protected $dates = [
        'deleted_at' // Tambahkan ini untuk soft delete
    ];

    // Method untuk mendapatkan data bidang aktif (tidak di-soft delete)
    public static function getAktif()
    {
        return self::whereNull('deleted_at')->orderBy('nama_bidang')->get();
    }

    // Method untuk mendapatkan data bidang dalam format array navbar
    public static function getNavItems()
    {
        $bidangs = self::getAktif();
        
        $navItems = [];
        foreach ($bidangs as $bidang) {
            $navItems[] = [
                'name' => $bidang->nama_bidang,
                'path' => '/bidang/' . $bidang->id_bidang, // atau slug jika ada
                'icon' => 'dashboard', // default icon
            ];
        }
        
        return $navItems;
    }
}

