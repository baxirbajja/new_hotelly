@extends('layouts.admin')

@section('title', 'Rooms Management')

@section('content')
    <div class="content-section">
        <div class="section-header">
            <h2>Rooms Management</h2>
            <a href="{{ route('admin.rooms.create') }}" class="btn btn-solid">Add New Room</a>
        </div>

        <!-- Filters -->
        <div class="filters">
            <form action="{{ route('admin.rooms') }}" method="GET" class="filter-form">
                <div class="form-group">
                    <input type="text" name="search" placeholder="Search rooms..." 
                           value="{{ request('search') }}" class="form-control">
                </div>
                <div class="form-group">
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="standard" {{ request('type') == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="deluxe" {{ request('type') == 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                        <option value="suite" {{ request('type') == 'suite' ? 'selected' : '' }}>Suite</option>
                    </select>
                </div>
                <div class="form-group">
                    <select name="availability" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('availability') == '1' ? 'selected' : '' }}>Available</option>
                        <option value="0" {{ request('availability') == '0' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-outline">Apply Filters</button>
            </form>
        </div>

        <!-- Rooms Table -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Type</th>
                        <th>Capacity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Bookings</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                        <tr>
                            <td>
                                <div class="room-info">
                                    @if($room->image)
                                        <img src="{{ asset('storage/' . $room->image) }}" alt="{{ $room->name }}" class="room-thumbnail">
                                    @endif
                                    <div>
                                        <strong>{{ $room->name }}</strong>
                                        <small>{{ $room->size }} m²</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ ucfirst($room->type) }}</td>
                            <td>{{ $room->capacity }} guests</td>
                            <td>${{ number_format($room->price, 2) }}/night</td>
                            <td>
                                <span class="status-badge status-{{ $room->is_available ? 'available' : 'unavailable' }}">
                                    {{ $room->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td>{{ $room->bookings_count }} bookings</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-outline">Edit</a>
                                    <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this room?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            {{ $rooms->links() }}
        </div>
    </div>
@endsection

@push('styles')
<style>
    .filters {
        margin-bottom: 2rem;
    }

    .filter-form {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-form .form-group {
        flex: 1;
        margin-bottom: 0;
    }

    .room-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .room-thumbnail {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
        border: none;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .status-available {
        background: #d4edda;
        color: #155724;
    }

    .status-unavailable {
        background: #f8d7da;
        color: #721c24;
    }

    .pagination-container {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }
</style>
@endpush
