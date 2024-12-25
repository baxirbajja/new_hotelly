@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">{{ $room->name }}</h1>
            <a href="{{ route('rooms') }}" class="text-blue-500 hover:text-blue-600">← Back to Rooms</a>
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
            @if($room->image)
                <img src="{{ asset('storage/' . $room->image) }}" 
                     alt="{{ $room->name }}" 
                     class="w-full h-96 object-cover">
            @endif
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <h2 class="text-2xl font-semibold mb-4">Room Details</h2>
                        <p class="text-gray-600 mb-6">{{ $room->description }}</p>
                        
                        <h3 class="text-xl font-semibold mb-3">Amenities</h3>
                        <ul class="grid grid-cols-2 gap-2 mb-6">
                            @foreach($room->amenities as $amenity)
                                <li class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ $amenity }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="md:border-l md:pl-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="text-center mb-4">
                                <span class="text-3xl font-bold">${{ number_format($room->price, 2) }}</span>
                                <span class="text-gray-600">/ night</span>
                            </div>
                            <div class="mb-4">
                                <div class="flex justify-between text-gray-600 mb-2">
                                    <span>Capacity:</span>
                                    <span>{{ $room->capacity }} {{ Str::plural('Guest', $room->capacity) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Status:</span>
                                    <span class="{{ $room->is_available ? 'text-green-500' : 'text-red-500' }}">
                                        {{ $room->is_available ? 'Available' : 'Not Available' }}
                                    </span>
                                </div>
                            </div>

                            @if($room->is_available)
                                <form action="{{ route('bookings.store', $room) }}" method="POST">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label for="check_in" class="block text-sm font-medium text-gray-700 mb-1">Check-in Date</label>
                                            <input type="date" name="check_in" id="check_in" 
                                                   class="w-full rounded-md border-gray-300"
                                                   min="{{ now()->format('Y-m-d') }}"
                                                   required>
                                        </div>
                                        <div>
                                            <label for="check_out" class="block text-sm font-medium text-gray-700 mb-1">Check-out Date</label>
                                            <input type="date" name="check_out" id="check_out" 
                                                   class="w-full rounded-md border-gray-300"
                                                   min="{{ now()->addDay()->format('Y-m-d') }}"
                                                   required>
                                        </div>
                                        <div>
                                            <label for="guests" class="block text-sm font-medium text-gray-700 mb-1">Number of Guests</label>
                                            <select name="guests" id="guests" class="w-full rounded-md border-gray-300" required>
                                                @for($i = 1; $i <= $room->capacity; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div>
                                            <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-1">Special Requests</label>
                                            <textarea name="special_requests" id="special_requests" 
                                                      class="w-full rounded-md border-gray-300" 
                                                      rows="3"></textarea>
                                        </div>
                                        <button type="submit" 
                                                class="w-full bg-blue-500 text-white px-6 py-3 rounded-md hover:bg-blue-600">
                                            Book Now
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                @if($room->reviews->isNotEmpty())
                    <div class="mt-8 pt-8 border-t">
                        <h2 class="text-2xl font-semibold mb-6">Reviews</h2>
                        <div class="space-y-6">
                            @foreach($room->reviews as $review)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                                         fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <p class="text-gray-600 mt-1">{{ $review->user->name }}</p>
                                        </div>
                                        <span class="text-gray-500 text-sm">
                                            {{ $review->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
