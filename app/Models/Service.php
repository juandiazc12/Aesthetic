<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'image',
        'price',
        'is_vip',
        'preferences',
        'notes',
        'description',
        'duration',
        'status',
        'user_id',
    ];

    protected $casts = [
        'preferences' => 'array',
    ];
}
