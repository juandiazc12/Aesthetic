<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'customer_id',
        'professional_id',
        'rating',
        'comment',
    ];

    // Relación con Booking
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Cliente que califica (modelo Customer)
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Profesional calificado (modelo User)
    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
