@extends('layouts.app')

@section('content')
<div class="relative h-screen">
    <!-- Hero Section -->
    <div class="absolute inset-0">
        <img src="{{ asset('images/hero.jpg') }}" alt="Hotel exterior" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black opacity-40"></div>
    </div>
    
    <!-- Hero Content -->
    <div class="relative z-10 flex flex-col items-center justify-center h-full text-white px-4">
        <h1 class="text-6xl font-serif mb-4">A NEW HOTEL IN<br>COPENHAGEN</h1>
        <p class="text-xl mb-8 text-center max-w-2xl">
            Experience the perfect blend of modern comfort and timeless elegance in the heart of the city.
        </p>
        <div class="flex space-x-4">
            <a href="/rooms" class="bg-white text-gray-900 px-8 py-3 rounded-md hover:bg-gray-100">View Rooms</a>
            <a href="/about" class="border border-white px-8 py-3 rounded-md hover:bg-white hover:text-gray-900">Learn More</a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="absolute bottom-0 left-0 right-0 bg-white bg-opacity-90 py-6">
        <div class="max-w-7xl mx-auto px-4">
            <form class="flex flex-wrap md:flex-nowrap gap-4">
                <div class="w-full md:w-1/4">
                    <label class="block text-sm font-medium text-gray-700">Check-in</label>
                    <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="w-full md:w-1/4">
                    <label class="block text-sm font-medium text-gray-700">Check-out</label>
                    <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="w-full md:w-1/4">
                    <label class="block text-sm font-medium text-gray-700">Guests</label>
                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option>1 Guest</option>
                        <option>2 Guests</option>
                        <option>3 Guests</option>
                        <option>4+ Guests</option>
                    </select>
                </div>
                <div class="w-full md:w-1/4 flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Search Availability
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Featured Rooms -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-serif mb-8">Featured Rooms</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Room Card -->
            <div class="group">
                <div class="aspect-w-16 aspect-h-9 overflow-hidden rounded-lg">
                    <img src="{{ asset('images/room1.jpg') }}" alt="Luxury Room" class="object-cover transform group-hover:scale-105 transition-transform duration-300">
                </div>
                <h3 class="mt-4 text-xl font-serif">Deluxe Suite</h3>
                <p class="mt-2 text-gray-600">From $299 per night</p>
            </div>
            <!-- More room cards... -->
        </div>
    </div>
</section>

<!-- Hotel Features -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-serif mb-8">Why Choose Us</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4">
                    <!-- Icon -->
                </div>
                <h3 class="text-xl font-serif mb-2">Prime Location</h3>
                <p class="text-gray-600">Located in the heart of Copenhagen, walking distance to major attractions</p>
            </div>
            <!-- More features... -->
        </div>
    </div>
</section>
@endsection
