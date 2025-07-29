<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Rating;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Service;

class ModelRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_ratings()
    {
        $user = User::factory()->create();
        $ratings = Rating::factory()->count(3)->create(['user_id' => $user->id]);
        $this->assertCount(3, $user->ratings);
    }

    public function test_customer_has_many_bookings()
    {
        $customer = Customer::factory()->create();
        $bookings = Booking::factory()->count(2)->create(['customer_id' => $customer->id]);
        $this->assertCount(2, $customer->bookings);
    }

    public function test_service_has_many_bookings()
    {
        $service = Service::factory()->create();
        $bookings = Booking::factory()->count(4)->create(['service_id' => $service->id]);
        $this->assertCount(4, $service->bookings);
    }

    public function test_booking_belongs_to_customer_service_user()
    {
        $customer = Customer::factory()->create();
        $service = Service::factory()->create();
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'user_id' => $user->id,
        ]);
        $this->assertEquals($customer->id, $booking->customer->id);
        $this->assertEquals($service->id, $booking->service->id);
        $this->assertEquals($user->id, $booking->user->id);
    }
}
