<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanduanFitur extends Model
{
    use HasFactory;

    protected $table = 'panduan_fiturs';

    protected $fillable = [
        'type',
        'role_tab',
        'menu_name',
        'slug',
        'title',
        'explanation',
        'route_target',
        'tutorial',
        'roles_allowed',
        'output',
        'form_details',
        'controller_path',
        'model_path',
        'view_path',
        'route_definition',
        'policy_gate',
        'middleware',
        'sort_order',
    ];

    protected $casts = [
        'roles_allowed' => 'array',
        'form_details' => 'array',
        'sort_order' => 'integer',
    ];
}
