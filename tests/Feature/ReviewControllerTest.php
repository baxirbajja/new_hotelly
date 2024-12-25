<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $room;
    protected $booking;

    protected function setUp(): void
    {
        parent::setUp();
        
        Carbon::setTestNow('2024-12-25 21:23:33');

        $this->user = User::factory()->create();
        $this->room = Room::factory()->create();
        $this->booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
            'check_in' => now()->subDays(5),
            'check_out' => now()->subDays(2),
            'status' => 'completed'
        ]);
    }

    public function test_guest_cannot_create_review()
    {
        $response = $this->post(route('reviews.store', $this->booking), [
            'rating' => 5,
            'comment' => 'Great stay!'
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_review_before_checkout()
    {
        $this->booking->update([
            'check_out' => now()->addDays(2),
            'status' => 'active'
        ]);

        $response = $this->actingAs($this->user)
                        ->post(route('reviews.store', $this->booking), [
                            'rating' => 5,
                            'comment' => 'Great stay!'
                        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_review_same_booking_twice()
    {
        Review::create([
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
            'booking_id' => $this->booking->id,
            'rating' => 5,
            'comment' => 'First review'
        ]);

        $response = $this->actingAs($this->user)
                        ->post(route('reviews.store', $this->booking), [
                            'rating' => 4,
                            'comment' => 'Another review'
                        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_update_own_review()
    {
        $review = Review::create([
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
            'booking_id' => $this->booking->id,
            'rating' => 5,
            'comment' => 'Original review',
            'is_approved' => false
        ]);

        $response = $this->actingAs($this->user)
                        ->put(route('reviews.update', $review), [
                            'rating' => 4,
                            'comment' => 'Updated review comment'
                        ]);

        $response->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 4,
            'comment' => 'Updated review comment'
        ]);
    }

    public function test_user_cannot_update_approved_review()
    {
        $review = Review::create([
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
            'booking_id' => $this->booking->id,
            'rating' => 5,
            'comment' => 'Original review',
            'is_approved' => true
        ]);

        $response = $this->actingAs($this->user)
                        ->put(route('reviews.update', $review), [
                            'rating' => 4,
                            'comment' => 'Updated review comment'
                        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_update_others_review()
    {
        $otherUser = User::factory()->create();
        $review = Review::create([
            'user_id' => $otherUser->id,
            'room_id' => $this->room->id,
            'booking_id' => $this->booking->id,
            'rating' => 5,
            'comment' => 'Original review'
        ]);

        $response = $this->actingAs($this->user)
                        ->put(route('reviews.update', $review), [
                            'rating' => 4,
                            'comment' => 'Updated review comment'
                        ]);

        $response->assertStatus(403);
    }

    public function test_validation_rules_for_review()
    {
        $response = $this->actingAs($this->user)
                        ->post(route('reviews.store', $this->booking), [
                            'rating' => 6,
                            'comment' => 'Short'
                        ]);

        $response->assertSessionHasErrors(['rating', 'comment']);
    }
}
