<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_booking_with_valid_data()
    {
        $customer = Customer::factory()->create();
        $service = Service::factory()->create();
        $user = User::factory()->create();
        $booking = Booking::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'user_id' => $user->id,
            'date' => Carbon::now()->addDay(),
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    }

    public function test_cannot_create_booking_with_past_date()
    {
        $customer = Customer::factory()->create();
        $service = Service::factory()->create();
        $user = User::factory()->create();
        $response = $this->post('/bookings', [
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'user_id' => $user->id,
            'date' => Carbon::now()->subDay(),
            'status' => 'pending',
        ]);
        $response->assertSessionHasErrors('date');
    }

    public function test_customer_booking_relationship()
    {
        $customer = Customer::factory()->create();
        $booking = Booking::factory()->create(['customer_id' => $customer->id]);
        $this->assertTrue($customer->bookings->contains($booking));
    }

    public function test_service_booking_relationship()
    {
        $service = Service::factory()->create();
        $booking = Booking::factory()->create(['service_id' => $service->id]);
        $this->assertTrue($service->bookings->contains($booking));
    }

    public function test_booking_status_change()
    {
        $booking = Booking::factory()->create(['status' => 'pending']);
        $booking->status = 'completed';
        $booking->save();
        $this->assertEquals('completed', $booking->fresh()->status);
        $booking->status = 'cancelled';
        $booking->save();
        $this->assertEquals('cancelled', $booking->fresh()->status);
    }

    public function test_only_owner_can_cancel_booking()
    {
        $customer = Customer::factory()->create();
        $booking = Booking::factory()->create(['customer_id' => $customer->id]);
        $otherCustomer = Customer::factory()->create();
        $this->actingAs($otherCustomer->user);
        $response = $this->delete("/bookings/{$booking->id}");
        $response->assertForbidden();
    }
}
