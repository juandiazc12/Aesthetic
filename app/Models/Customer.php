<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;


class Customer extends User
{

    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'birth_date',
        'profile_picture',
        'status',
        'preferences',
        'is_vip',
        'notes',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relación con las reservas
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Método para obtener el nombre completo
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomerResetPasswordNotification($token));
    }

    /**
     * Get the email address that should be used for password reset.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    // Método para verificar si tiene reservas activas
    public function hasActiveBookings()
    {
        return $this->bookings()->where('status', 'active')->exists();
    }

    // Método para obtener reservas pagadas
    public function getPaidBookings()
    {
        return $this->bookings()->where('payment_status', 'paid')->get();
    }

    // Método para obtener reservas pendientes de pago
    public function getPendingPaymentBookings()
    {
        return $this->bookings()->where('payment_status', 'pending')->get();
    }
    public function getContent(string $field)
    {
        return $this->getAttribute($field);
    }

    public function ratings()
{
    return $this->hasMany(Rating::class);
}

}