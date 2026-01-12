<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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

    // public static function getNavItems() {
    //     $currentBidang = request()->route('bidang');

    //     return self::whereNull('deleted_at')
    //         ->orderBy('nama_bidang')
    //         ->get()
    //         ->map(function ($bidang) use ($currentBidang) {
    //             $isActive = $currentBidang
    //                 && $currentBidang->slug === $bidang->slug;

    //             return [
    //                 'name'      => $bidang->nama_bidang,
    //                 'path'      => route('kegiatan.index', $bidang->slug),
    //                 'icon'      => 'dashboard',
    //                 'is_active' => $isActive, 
    //             ];
    //         })
    //         ->toArray();
    // }

    public static function getNavItems(): array
    {
        $user = Auth::user();
        $currentBidang = request()->route('bidang');

        $query = self::query()->whereNull('deleted_at');

        if ($user && ! in_array($user->active_role, ['Admin', 'Pimpinan'])) {

            $query->whereHas('kegiatans', function ($kegiatan) use ($user) {

                $kegiatan->where(function ($q) use ($user) {

                    // ✅ 1. Ketua Tim
                    $q->where('id_penanggung_jawab', $user->id_pegawai)

                    // ✅ 2. Anggota Tim → lewat SubKegiatan → Penugasan
                    ->orWhereHas('subKegiatans.penugasans.anggota', function ($anggota) use ($user) {
                        $anggota->where('id_anggota', $user->id_pegawai);
                    });
                });
            });
        }

        return $query
            ->orderBy('nama_bidang')
            ->get()
            ->map(function ($bidang) use ($currentBidang) {
                return [
                    'name'      => $bidang->nama_bidang,
                    'path'      => route('kegiatan.index', $bidang->slug),
                    'icon'      => 'dashboard',
                    'is_active' => $currentBidang && $currentBidang->slug === $bidang->slug,
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

