<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarLink extends Model
{
    use HasFactory;

    protected $table = 'sidebar_links';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'parent_id',
        'name',
        'url',
        'icon',
        'color',
        'background_color',
        'sort_order',
        'is_external',
        'is_special',
    ];

    protected $casts = [
        'is_external' => 'boolean',
        'is_special' => 'boolean',
    ];

    /**
     * Relasi ke parent link (jika berupa sub-link)
     */
    public function parent()
    {
        return $this->belongsTo(SidebarLink::class, 'parent_id');
    }

    /**
     * Relasi ke sub-link di bawahnya
     */
    public function children()
    {
        return $this->hasMany(SidebarLink::class, 'parent_id')->orderBy('sort_order', 'asc');
    }
}
