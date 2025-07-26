<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        $user = User::first() ?: User::factory()->create();
        $service = Service::first() ?: Service::factory()->create();
        return [
            'user_id' => $user->id,
            'service_id' => $service->id,
            'date' => $this->faker->date(),
            'time' => $this->faker->time('H:i'),
            // Agrega otros campos necesarios aquí según tu modelo Booking
        ];
    }
}
