<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Hotelly') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/" class="text-2xl font-serif">HOTELLY</a>
                    </div>
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="/" class="inline-flex items-center px-1 pt-1 text-gray-900">Home</a>
                        <a href="/rooms" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-900">Rooms</a>
                        <a href="/dining" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-900">Dining</a>
                        <a href="/events" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-900">Events</a>
                        <a href="/about" class="inline-flex items-center px-1 pt-1 text-gray-500 hover:text-gray-900">About</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="/login" class="text-gray-500 hover:text-gray-900 px-3 py-2">Sign In</a>
                    <a href="/book-now" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Book Now</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider">About</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="#" class="text-gray-300 hover:text-white">Our Story</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Press</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Careers</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider">Support</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="#" class="text-gray-300 hover:text-white">Contact</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">FAQ</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-700 pt-8">
                <p class="text-gray-400 text-sm">
                    © 2024 Hotelly. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
