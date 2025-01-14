<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_id',
        'scheduled_at',
        'status',
        'notes',
        'professional_id'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function professional()
    {
        return $this->belongsTo(user::class);
    }

    public function customer()
    {
        return $this->belongsTo(customer::class, 'customer_id');
    }
}
