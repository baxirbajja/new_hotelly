@extends('layouts.admin')

@section('title', 'Booking Details')

@section('content')
    <div class="content-section">
        <div class="section-header">
            <h2>Booking Details #{{ $booking->id }}</h2>
            <div class="header-actions">
                <a href="{{ route('admin.bookings') }}" class="btn btn-outline">Back to Bookings</a>
                @if($booking->status === 'pending')
                    <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">Confirm Booking</button>
                    </form>
                @endif
                @if($booking->status !== 'cancelled')
                    <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to cancel this booking?')">
                            Cancel Booking
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="booking-details">
            <!-- Status -->
            <div class="status-section">
                <span class="status-badge status-{{ $booking->status }}">
                    {{ ucfirst($booking->status) }}
                </span>
                <span class="booking-date">
                    Created on {{ $booking->created_at->format('M d, Y H:i') }}
                </span>
            </div>

            <div class="details-grid">
                <!-- Guest Information -->
                <div class="detail-card">
                    <h3>Guest Information</h3>
                    <div class="detail-content">
                        <p><strong>Name:</strong> {{ $booking->user->name }}</p>
                        <p><strong>Email:</strong> {{ $booking->user->email }}</p>
                        <p><strong>Phone:</strong> {{ $booking->user->phone ?? 'Not provided' }}</p>
                        <p><strong>Number of Guests:</strong> {{ $booking->guests }}</p>
                    </div>
                </div>

                <!-- Room Information -->
                <div class="detail-card">
                    <h3>Room Information</h3>
                    <div class="detail-content">
                        <div class="room-preview">
                            @if($booking->room->image)
                                <img src="{{ asset('storage/' . $booking->room->image) }}" 
                                     alt="{{ $booking->room->name }}">
                            @endif
                            <div>
                                <h4>{{ $booking->room->name }}</h4>
                                <p>{{ ucfirst($booking->room->type) }} Room</p>
                            </div>
                        </div>
                        <p><strong>Room Size:</strong> {{ $booking->room->size }} m²</p>
                        <p><strong>Capacity:</strong> {{ $booking->room->capacity }} guests</p>
                        <p><strong>View:</strong> {{ ucfirst($booking->room->view_type) }}</p>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="detail-card">
                    <h3>Stay Details</h3>
                    <div class="detail-content">
                        <div class="stay-dates">
                            <div>
                                <strong>Check-in</strong>
                                <p>{{ $booking->check_in->format('M d, Y') }}</p>
                                <small>After 3:00 PM</small>
                            </div>
                            <div>
                                <strong>Check-out</strong>
                                <p>{{ $booking->check_out->format('M d, Y') }}</p>
                                <small>Before 11:00 AM</small>
                            </div>
                        </div>
                        <p><strong>Length of Stay:</strong> 
                            {{ $booking->check_in->diffInDays($booking->check_out) }} nights</p>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="detail-card">
                    <h3>Payment Details</h3>
                    <div class="detail-content">
                        <div class="payment-breakdown">
                            <div class="payment-row">
                                <span>Room Rate</span>
                                <span>${{ number_format($booking->room->price, 2) }} / night</span>
                            </div>
                            <div class="payment-row">
                                <span>Number of Nights</span>
                                <span>{{ $booking->check_in->diffInDays($booking->check_out) }}</span>
                            </div>
                            <div class="payment-row total">
                                <span>Total Amount</span>
                                <span>${{ number_format($booking->total_price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($booking->special_requests)
                <div class="special-requests">
                    <h3>Special Requests</h3>
                    <p>{{ $booking->special_requests }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
    .header-actions {
        display: flex;
        gap: 1rem;
    }

    .status-section {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .booking-date {
        color: #666;
        font-size: 0.9rem;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .detail-card {
        background: white;
        border-radius: 4px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .detail-card h3 {
        font-family: 'Playfair Display', serif;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eee;
    }

    .detail-content {
        color: #333;
    }

    .detail-content p {
        margin-bottom: 0.5rem;
    }

    .room-preview {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .room-preview img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
    }

    .stay-dates {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .stay-dates small {
        color: #666;
    }

    .payment-breakdown {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .payment-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
    }

    .payment-row.total {
        border-top: 1px solid #eee;
        margin-top: 0.5rem;
        padding-top: 1rem;
        font-weight: bold;
    }

    .special-requests {
        background: white;
        border-radius: 4px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .special-requests h3 {
        font-family: 'Playfair Display', serif;
        margin-bottom: 1rem;
    }
</style>
@endpush
