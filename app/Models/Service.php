<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Service extends Model
{
    use HasFactory,AsSource,Attachable,Filterable;
    protected $fillable = [
        'name',
        'image',
        'price',
        'preferences',
        'description',
        'duration',
        'status',
        'user_id',
    ];

    protected $casts = [
        'preferences' => 'array',
        'price' => 'decimal:2',
    ];
}
        