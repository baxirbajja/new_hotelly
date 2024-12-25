@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Our Rooms</h1>

    <div class="mb-8">
        <form action="{{ route('rooms') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Guests</label>
                <select name="capacity" id="capacity" class="w-full rounded-md border-gray-300">
                    <option value="">Any</option>
                    @for($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}" {{ request('capacity') == $i ? 'selected' : '' }}>
                            {{ $i }} {{ Str::plural('Guest', $i) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="min_price" class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                <input type="number" name="min_price" id="min_price" 
                       value="{{ request('min_price') }}"
                       class="w-full rounded-md border-gray-300"
                       min="0" step="10">
            </div>
            <div>
                <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                <input type="number" name="max_price" id="max_price" 
                       value="{{ request('max_price') }}"
                       class="w-full rounded-md border-gray-300"
                       min="0" step="10">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Search
                </button>
            </div>
        </form>
    </div>

    @if($rooms->isEmpty())
        <div class="text-center py-8">
            <p class="text-gray-600">No rooms found matching your criteria.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($rooms as $room)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @if($room->image)
                        <img src="{{ asset('storage/' . $room->image) }}" 
                             alt="{{ $room->name }}" 
                             class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-2">{{ $room->name }}</h2>
                        <p class="text-gray-600 mb-4">{{ Str::limit($room->description, 100) }}</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-2xl font-bold">${{ number_format($room->price, 2) }}</span>
                            <span class="text-gray-600">{{ $room->capacity }} {{ Str::plural('Guest', $room->capacity) }}</span>
                        </div>
                        @if($room->reviews_count > 0)
                            <div class="flex items-center mb-4">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= round($room->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="ml-2 text-gray-600">
                                    {{ number_format($room->average_rating, 1) }} ({{ $room->reviews_count }})
                                </span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <a href="{{ route('rooms.show', $room) }}" 
                               class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                View Details
                            </a>
                            @if($room->is_available)
                                <span class="text-green-500">Available</span>
                            @else
                                <span class="text-red-500">Not Available</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $rooms->links() }}
        </div>
    @endif
</div>
@endsection
