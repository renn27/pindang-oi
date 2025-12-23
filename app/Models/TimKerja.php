<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimKerja extends Model
{
    use HasFactory;

    protected $table = 'tim_kerja';
    protected $primaryKey = 'id_tim_kerja';

    protected $fillable = [
        'nama_tim',
        'id_ketua',
    ];

    public $timestamps = true;
}
