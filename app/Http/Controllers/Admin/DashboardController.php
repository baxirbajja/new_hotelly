<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $stats = [
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('is_available', true)->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'total_users' => User::count(),
            'revenue' => Booking::where('status', 'confirmed')->sum('total_price'),
        ];

        $latest_bookings = Booking::with(['user', 'room'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'latest_bookings'));
    }

    public function rooms()
    {
        $rooms = Room::withCount('bookings')->get();
        return view('admin.rooms.index', compact('rooms'));
    }

    public function bookings()
    {
        $bookings = Booking::with(['user', 'room'])->latest()->paginate(10);
        return view('admin.bookings.index', compact('bookings'));
    }

    public function users()
    {
        $users = User::withCount('bookings')->paginate(10);
        return view('admin.users.index', compact('users'));
    }
}
