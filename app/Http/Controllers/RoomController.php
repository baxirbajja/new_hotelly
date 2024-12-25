<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('reviews')->get();
        return view('rooms.index', compact('rooms'));
    }

    public function show(Room $room)
    {
        $room->load(['reviews.user']);
        return view('rooms.show', compact('room'));
    }

    public function search(Request $request)
    {
        $query = Room::query();

        if ($request->filled('check_in') && $request->filled('check_out')) {
            $query->whereDoesntHave('bookings', function ($q) use ($request) {
                $q->whereBetween('check_in', [$request->check_in, $request->check_out])
                  ->orWhereBetween('check_out', [$request->check_in, $request->check_out]);
            });
        }

        if ($request->filled('guests')) {
            $query->where('capacity', '>=', $request->guests);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $rooms = $query->get();
        return view('rooms.index', compact('rooms'));
    }
}
