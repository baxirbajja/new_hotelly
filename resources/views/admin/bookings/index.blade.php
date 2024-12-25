@extends('layouts.admin')

@section('title', 'Bookings Management')

@section('content')
    <div class="content-section">
        <div class="section-header">
            <h2>Bookings Management</h2>
            <a href="{{ route('admin.bookings.create') }}" class="btn btn-solid">Create New Booking</a>
        </div>

        <!-- Filters -->
        <div class="filters">
            <form action="{{ route('admin.bookings') }}" method="GET" class="filter-form">
                <div class="form-group">
                    <input type="text" name="search" placeholder="Search by guest name or room..." 
                           value="{{ request('search') }}" class="form-control">
                </div>
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="form-control" placeholder="From Date">
                </div>
                <div class="form-group">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="form-control" placeholder="To Date">
                </div>
                <button type="submit" class="btn btn-outline">Apply Filters</button>
            </form>
        </div>

        <!-- Bookings Table -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Dates</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>
                                <div class="guest-info">
                                    <strong>{{ $booking->user->name }}</strong>
                                    <small>{{ $booking->user->email }}</small>
                                </div>
                            </td>
                            <td>{{ $booking->room->name }}</td>
                            <td>
                                <div class="dates-info">
                                    <div>Check-in: {{ $booking->check_in->format('M d, Y') }}</div>
                                    <div>Check-out: {{ $booking->check_out->format('M d, Y') }}</div>
                                </div>
                            </td>
                            <td>${{ number_format($booking->total_price, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $booking->status }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" 
                                       class="btn btn-sm btn-outline">View</a>
                                    <a href="{{ route('admin.bookings.edit', $booking) }}" 
                                       class="btn btn-sm btn-outline">Edit</a>
                                    @if($booking->status === 'pending')
                                        <form action="{{ route('admin.bookings.confirm', $booking) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                        </form>
                                    @endif
                                    @if($booking->status !== 'cancelled')
                                        <form action="{{ route('admin.bookings.cancel', $booking) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            {{ $bookings->links() }}
        </div>
    </div>
@endsection

@push('styles')
<style>
    .guest-info {
        display: flex;
        flex-direction: column;
    }

    .guest-info small {
        color: #666;
    }

    .dates-info {
        font-size: 0.9rem;
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

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
    }
</style>
@endpush
