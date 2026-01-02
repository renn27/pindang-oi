<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    public $incrementing = true;
    public $timestamps = true;


    protected $fillable = [
        'nama_role',
    ];

    public function pegawais() {
        return $this->belongsToMany(Pegawai::class, 'pegawai_role', 'role_id',
        'pegawai_id');
    }
}
