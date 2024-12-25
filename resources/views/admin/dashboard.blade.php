@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Stats Overview -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Rooms</h3>
            <div class="value">{{ $stats['total_rooms'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Available Rooms</h3>
            <div class="value">{{ $stats['available_rooms'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Total Bookings</h3>
            <div class="value">{{ $stats['total_bookings'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Revenue</h3>
            <div class="value">${{ number_format($stats['revenue'], 2) }}</div>
        </div>
    </div>

    <!-- Latest Bookings -->
    <div class="content-section">
        <div class="section-header">
            <h2>Latest Bookings</h2>
            <a href="{{ route('admin.bookings') }}" class="btn btn-outline">View All</a>
        </div>
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latest_bookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>{{ $booking->user->name }}</td>
                            <td>{{ $booking->room->name }}</td>
                            <td>{{ $booking->check_in->format('M d, Y') }}</td>
                            <td>{{ $booking->check_out->format('M d, Y') }}</td>
                            <td>
                                <span class="status-badge status-{{ $booking->status }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>${{ number_format($booking->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="content-section">
        <div class="section-header">
            <h2>Quick Actions</h2>
        </div>
        <div class="quick-actions">
            <a href="{{ route('admin.rooms.create') }}" class="action-card">
                <span class="icon">🏨</span>
                <h3>Add New Room</h3>
                <p>Create a new room listing</p>
            </a>
            <a href="{{ route('admin.bookings.create') }}" class="action-card">
                <span class="icon">📅</span>
                <h3>New Booking</h3>
                <p>Create a booking for a guest</p>
            </a>
            <a href="{{ route('admin.users.create') }}" class="action-card">
                <span class="icon">👤</span>
                <h3>Add User</h3>
                <p>Create a new user account</p>
            </a>
            <a href="{{ route('admin.reports') }}" class="action-card">
                <span class="icon">📊</span>
                <h3>Generate Report</h3>
                <p>Create custom reports</p>
            </a>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .content-section {
        background: white;
        padding: 1.5rem;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-header h2 {
        font-size: 1.25rem;
        color: #333;
        font-family: 'Playfair Display', serif;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-confirmed {
        background: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .action-card {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 4px;
        text-align: center;
        text-decoration: none;
        color: #333;
        transition: all 0.3s ease;
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .action-card .icon {
        font-size: 2rem;
        margin-bottom: 1rem;
        display: block;
    }

    .action-card h3 {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .action-card p {
        color: #666;
        font-size: 0.9rem;
    }
</style>
@endpush
