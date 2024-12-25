@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">My Bookings</h1>

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

    @if($bookings->isEmpty())
        <div class="bg-gray-100 rounded-lg p-6 text-center">
            <p class="text-gray-600">You haven't made any bookings yet.</p>
            <a href="{{ route('rooms') }}" class="mt-4 inline-block bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Browse Rooms</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($bookings as $booking)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-2">{{ $booking->room->name }}</h2>
                        <div class="text-gray-600 space-y-2">
                            <p>
                                <span class="font-medium">Check-in:</span> 
                                {{ $booking->check_in->format('M d, Y') }}
                            </p>
                            <p>
                                <span class="font-medium">Check-out:</span> 
                                {{ $booking->check_out->format('M d, Y') }}
                            </p>
                            <p>
                                <span class="font-medium">Guests:</span> 
                                {{ $booking->guests }}
                            </p>
                            <p>
                                <span class="font-medium">Total Price:</span> 
                                ${{ number_format($booking->total_price, 2) }}
                            </p>
                            <p>
                                <span class="font-medium">Status:</span> 
                                <span class="@if($booking->status === 'cancelled') text-red-500 
                                           @elseif($booking->status === 'completed') text-green-500 
                                           @else text-blue-500 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="mt-4 space-x-2">
                            <a href="{{ route('bookings.show', $booking) }}" 
                               class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                View Details
                            </a>
                            @if($booking->status === 'pending' || $booking->status === 'confirmed')
                                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                                            onclick="return confirm('Are you sure you want to cancel this booking?')">
                                        Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $bookings->links() }}
    @endif
</div>
@endsection
