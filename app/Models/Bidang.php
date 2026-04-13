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
        'urutan',
    ];

    public static function getNavItems(): array
    {
        $user = Auth::user();
        $currentBidang = request()->route('bidang');

        // 🔥 TAMBAHAN LOGIC: Jika $currentBidang kosong (berada di route nested tanpa param bidang)
        // Lakukan perunutan ke model Kegiatan atau SubKegiatan
        if (!$currentBidang) {
            $subKegiatan = request()->route('subKegiatan');
            $kegiatan = request()->route('kegiatan');
            
            if ($subKegiatan) {
                // Bergantung pada tipe parameter binding
                if ($subKegiatan instanceof \App\Models\SubKegiatan) {
                    $currentBidang = $subKegiatan->kegiatan->bidang ?? null;
                } elseif (is_numeric($subKegiatan)) {
                    $currentBidang = \App\Models\SubKegiatan::with('kegiatan.bidang')->find($subKegiatan)?->kegiatan->bidang;
                }
            } elseif ($kegiatan) {
                if ($kegiatan instanceof \App\Models\Kegiatan) {
                    $currentBidang = $kegiatan->bidang ?? null;
                } elseif (is_numeric($kegiatan)) {
                    $currentBidang = \App\Models\Kegiatan::with('bidang')->find($kegiatan)?->bidang;
                }
            }
        }

        $query = self::query()->whereNull('deleted_at');

        if ($user) {
            // MODE KETUA TIM
            if ($user->active_role === 'Ketua Tim') {
                $query->whereHas('kegiatans', function ($q) use ($user) {
                    $q->where('id_penanggung_jawab', $user->id_pegawai);
                });
            }

            // MODE ANGGOTA TIM
            if ($user->active_role === 'Anggota Tim') {
                $query->whereHas('kegiatans.subKegiatans.penugasans', function ($q) use ($user) {
                    $q->where('id_anggota', $user->id_pegawai);
                });
            }
        }

        $bidangs = $query->orderBy('urutan')->get();

        // 🔥 TAMBAHAN LOGIC UNTUK KETUA TIM TANPA KEGIATAN
        if ($user && $user->active_role === 'Ketua Tim' && $bidangs->isEmpty()) {
            return [
                [
                    'name' => 'Belum ada Bidang Kerja',
                    'path' => route('master-kegiatan.index'), // arahkan ke master kegiatan
                    'is_active' => false,
                    'is_placeholder' => true, // flag tambahan'
                ]
            ];
        }

        return $bidangs
        ->map(function ($bidang) use ($currentBidang) {
            return [
                'name'      => $bidang->nama_bidang,
                'path'      => route('kegiatan.index', $bidang->slug),
                'icon'      => 'dashboard',
                'is_active' => $currentBidang && $currentBidang->slug === $bidang->slug,
            ];
        })
        ->toArray();

        // return $query
        //     ->orderBy('urutan')
        //     ->get()
        //     ->map(function ($bidang) use ($currentBidang) {
        //         return [
        //             'name'      => $bidang->nama_bidang,
        //             'path'      => route('kegiatan.index', $bidang->slug),
        //             'icon'      => 'dashboard',
        //             'is_active' => $currentBidang && $currentBidang->slug === $bidang->slug,
        //         ];
        //     })
        //     ->toArray();
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

