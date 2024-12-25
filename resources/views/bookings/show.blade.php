@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Booking Details</h1>
            <a href="{{ route('bookings') }}" class="text-blue-500 hover:text-blue-600">← Back to Bookings</a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-2xl font-semibold mb-4">{{ $booking->room->name }}</h2>
                        <div class="space-y-3">
                            <p class="flex justify-between">
                                <span class="font-medium">Check-in:</span>
                                <span>{{ $booking->check_in->format('M d, Y') }}</span>
                            </p>
                            <p class="flex justify-between">
                                <span class="font-medium">Check-out:</span>
                                <span>{{ $booking->check_out->format('M d, Y') }}</span>
                            </p>
                            <p class="flex justify-between">
                                <span class="font-medium">Guests:</span>
                                <span>{{ $booking->guests }}</span>
                            </p>
                            <p class="flex justify-between">
                                <span class="font-medium">Total Price:</span>
                                <span>${{ number_format($booking->total_price, 2) }}</span>
                            </p>
                            <p class="flex justify-between">
                                <span class="font-medium">Status:</span>
                                <span class="@if($booking->status === 'cancelled') text-red-500 
                                           @elseif($booking->status === 'completed') text-green-500 
                                           @else text-blue-500 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold mb-3">Special Requests</h3>
                        <p class="text-gray-600">
                            {{ $booking->special_requests ?? 'No special requests' }}
                        </p>
                    </div>
                </div>

                @if($booking->status === 'pending' || $booking->status === 'confirmed')
                    <div class="mt-6 pt-6 border-t">
                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600"
                                    onclick="return confirm('Are you sure you want to cancel this booking?')">
                                Cancel Booking
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
