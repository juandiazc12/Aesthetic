<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinLookup extends Model
{
    protected $fillable = [
        'bin',
        'bank_name',
        'bank_url',
        'brand',
        'type',
        'country_name',
        'country_emoji',
        'is_valid'
    ];

    protected $casts = [
        'is_valid' => 'boolean',
    ];
} 