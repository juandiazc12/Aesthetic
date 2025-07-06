<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Service extends Model
{
    use HasFactory, AsSource, Attachable, Filterable;

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

    // Relación con reservas (bookings)
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Relación con profesionales (muchos a muchos)
    public function professionals()
    {
        return $this->belongsToMany(User::class, 'service_professional', 'service_id', 'professional_id');
    }

    // Método para obtener profesionales activos para este servicio
    public function getActiveProfessionals()
    {
        return $this->professionals()
            ->whereHas('roles', function ($query) {
                $query->where('slug', 'profesional');
            })
            ->select('users.id', 'users.name', 'users.email', 'users.photo')
            ->get();
    }
}