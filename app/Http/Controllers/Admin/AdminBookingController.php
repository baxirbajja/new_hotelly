<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'room']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('room', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('check_in', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('check_out', '<=', $request->date_to);
        }

        $bookings = $query->latest()->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'room']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function create()
    {
        $rooms = Room::where('is_available', true)->get();
        $users = User::all();
        return view('admin.bookings.create', compact('rooms', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        // Check room availability
        $room = Room::findOrFail($validated['room_id']);
        $isAvailable = $this->checkRoomAvailability(
            $room,
            $validated['check_in'],
            $validated['check_out']
        );

        if (!$isAvailable) {
            return back()->withErrors(['room_id' => 'Room is not available for the selected dates.']);
        }

        if ($validated['guests'] > $room->capacity) {
            return back()->withErrors(['guests' => 'Number of guests exceeds room capacity.']);
        }

        // Calculate total price
        $nights = date_diff(date_create($validated['check_in']), date_create($validated['check_out']))->days;
        $totalPrice = $room->price * $nights;

        DB::transaction(function() use ($validated, $totalPrice) {
            Booking::create([
                'user_id' => $validated['user_id'],
                'room_id' => $validated['room_id'],
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'guests' => $validated['guests'],
                'total_price' => $totalPrice,
                'special_requests' => $validated['special_requests'],
                'status' => 'confirmed'
            ]);
        });

        return redirect()->route('admin.bookings')->with('success', 'Booking created successfully.');
    }

    public function confirm(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->withErrors(['status' => 'Booking cannot be confirmed.']);
        }

        $booking->update(['status' => 'confirmed']);

        // Send confirmation email to guest
        // TODO: Implement email notification

        return back()->with('success', 'Booking confirmed successfully.');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->status === 'cancelled') {
            return back()->withErrors(['status' => 'Booking is already cancelled.']);
        }

        $booking->update(['status' => 'cancelled']);

        // Send cancellation email to guest
        // TODO: Implement email notification

        return back()->with('success', 'Booking cancelled successfully.');
    }

    private function checkRoomAvailability(Room $room, $checkIn, $checkOut)
    {
        $conflictingBookings = Booking::where('room_id', $room->id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function($query) use ($checkIn, $checkOut) {
                        $query->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->count();

        return $conflictingBookings === 0;
    }
}
