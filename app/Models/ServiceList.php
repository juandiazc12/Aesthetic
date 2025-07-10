<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class ServiceList extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'name',
    ];

    public function servicesList()
    {
        return $this->belongsToMany(User::class, 'service_list_user', 'service_list_id', 'professional_id');
    }
}