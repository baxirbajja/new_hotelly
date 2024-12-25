<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {
        if (auth()->check()) {
            $bookings = auth()->user()->bookings()->with('room')->latest()->paginate(10);
            return view('bookings.index', compact('bookings'));
        }
        
        return redirect()->route('login');
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        return view('bookings.show', compact('booking'));
    }

    public function store(Request $request, Room $room)
    {
        if (!$room->is_available) {
            return back()->withErrors(['error' => 'This room is not available for booking.']);
        }

        $validated = $request->validate([
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1|max:' . $room->capacity,
            'special_requests' => 'nullable|string|max:500'
        ]);

        // Check if room is already booked for these dates
        $isBooked = $room->bookings()
            ->where(function ($query) use ($validated) {
                $query->whereBetween('check_in', [$validated['check_in'], $validated['check_out']])
                    ->orWhereBetween('check_out', [$validated['check_in'], $validated['check_out']])
                    ->orWhere(function ($query) use ($validated) {
                        $query->where('check_in', '<=', $validated['check_in'])
                            ->where('check_out', '>=', $validated['check_out']);
                    });
            })
            ->exists();

        if ($isBooked) {
            return back()->withErrors(['error' => 'Room is already booked for these dates.']);
        }

        $nights = Carbon::parse($validated['check_out'])->diffInDays(Carbon::parse($validated['check_in']));
        $total_price = $room->price * $nights;

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'room_id' => $room->id,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'guests' => $validated['guests'],
            'total_price' => $total_price,
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'pending'
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully!');
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);

        if ($booking->status !== 'pending' && $booking->status !== 'confirmed') {
            return back()->withErrors(['error' => 'This booking cannot be cancelled.']);
        }

        if (Carbon::parse($booking->check_in)->isPast()) {
            return back()->withErrors(['error' => 'Cannot cancel a past booking.']);
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
