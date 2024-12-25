<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    protected $appends = [
        'is_admin',
    ];

    // Relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Accessors & Mutators
    public function getIsAdminAttribute()
    {
        return $this->role === 'admin';
    }

    // Helper Methods
    public function canManageBooking(Booking $booking)
    {
        return $this->is_admin || $this->id === $booking->user_id;
    }

    public function canManageReview(Review $review)
    {
        return $this->is_admin || $this->id === $review->user_id;
    }

    public function hasActiveBooking(Room $room, $checkIn, $checkOut)
    {
        return $this->bookings()
            ->where('room_id', $room->id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function($query) use ($checkIn, $checkOut) {
                        $query->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();
    }

    public function getBookingStats()
    {
        $stats = [
            'total' => $this->bookings()->count(),
            'completed' => $this->bookings()->where('status', 'completed')->count(),
            'upcoming' => $this->bookings()->where('status', 'confirmed')
                ->where('check_in', '>', now())
                ->count(),
            'cancelled' => $this->bookings()->where('status', 'cancelled')->count(),
        ];

        $stats['completion_rate'] = $stats['total'] > 0 
            ? round(($stats['completed'] / $stats['total']) * 100, 1) 
            : 0;

        return $stats;
    }

    public function getReviewStats()
    {
        $stats = [
            'total' => $this->reviews()->count(),
            'average_rating' => $this->reviews()->avg('rating') ?? 0,
        ];

        $stats['average_rating'] = round($stats['average_rating'], 1);

        return $stats;
    }
}
