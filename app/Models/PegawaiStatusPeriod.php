<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiStatusPeriod extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pegawai_status_periods';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id_pegawai',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}
