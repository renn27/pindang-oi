<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisKegiatan extends Model
{
    use HasFactory;

    protected $table = 'jenis_kegiatans';
    public $timestamps = true;

    protected $fillable = [
        'jenis_kegiatan',
        'kategori',
        'butuh_dl_atau_translok',
    ];

    public function penugasans() {
        return $this->hasMany(Penugasan::class, 'id_jenis_kegiatan');
    }
}
