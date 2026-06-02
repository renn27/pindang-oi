<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KegiatanTransfer extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kegiatan_transfers';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kegiatan_id',
        'from_ketua_id',
        'to_ketua_id',
        'transferred_at',
    ];

    protected $casts = [
        'transferred_at' => 'date',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id', 'id_kegiatan');
    }

    public function fromKetua()
    {
        return $this->belongsTo(Pegawai::class, 'from_ketua_id', 'id_pegawai');
    }

    public function toKetua()
    {
        return $this->belongsTo(Pegawai::class, 'to_ketua_id', 'id_pegawai');
    }
}
