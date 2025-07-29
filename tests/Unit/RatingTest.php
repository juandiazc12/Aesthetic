<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Rating;
use App\Models\User;
use App\Models\Booking;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_rating_with_score_between_1_and_5()
    {
        $professional = User::factory()->create();
        $booking = Booking::factory()->create();
        $rating = Rating::create([
            'user_id' => $professional->id,
            'booking_id' => $booking->id,
            'score' => 4,
            'comment' => 'Buen servicio',
        ]);
        $this->assertDatabaseHas('ratings', ['id' => $rating->id, 'score' => 4]);
    }

    public function test_cannot_create_rating_without_comment()
    {
        $professional = User::factory()->create();
        $booking = Booking::factory()->create();
        $response = $this->post('/ratings', [
            'user_id' => $professional->id,
            'booking_id' => $booking->id,
            'score' => 5,
            'comment' => '',
        ]);
        $response->assertSessionHasErrors('comment');
    }

    public function test_rating_user_relationship()
    {
        $professional = User::factory()->create();
        $rating = Rating::factory()->create(['user_id' => $professional->id]);
        $this->assertEquals($professional->id, $rating->user->id);
    }

    public function test_ratings_average_calculation()
    {
        $professional = User::factory()->create();
        Rating::factory()->create(['user_id' => $professional->id, 'score' => 5]);
        Rating::factory()->create(['user_id' => $professional->id, 'score' => 3]);
        $average = $professional->ratings()->avg('score');
        $this->assertEquals(4, $average);
    }

    public function test_customer_cannot_rate_same_booking_twice()
    {
        $professional = User::factory()->create();
        $booking = Booking::factory()->create();
        Rating::factory()->create([
            'user_id' => $professional->id,
            'booking_id' => $booking->id,
        ]);
        $response = $this->post('/ratings', [
            'user_id' => $professional->id,
            'booking_id' => $booking->id,
            'score' => 5,
            'comment' => 'Repetida',
        ]);
        $response->assertSessionHasErrors('booking_id');
    }
}
