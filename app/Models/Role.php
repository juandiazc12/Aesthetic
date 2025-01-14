<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array', // Si 'permissions' es un campo JSON
    ];

    // Relación con usuarios a través de la tabla role_users
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_users');
    }
}
