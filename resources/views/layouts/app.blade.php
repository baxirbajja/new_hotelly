<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Hotelly') }} - @yield('title', 'Luxury Hotel')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="nav-container">
        <a href="{{ url('/') }}" class="logo">HOTELLY</a>
        <div class="nav-right">
            <div class="nav-links">
                <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
                <a href="{{ url('/rooms') }}" class="nav-link {{ request()->is('rooms*') ? 'active' : '' }}">Rooms</a>
                <a href="{{ url('/dining') }}" class="nav-link {{ request()->is('dining') ? 'active' : '' }}">Dining</a>
                <a href="{{ url('/events') }}" class="nav-link {{ request()->is('events') ? 'active' : '' }}">Events</a>
                <a href="{{ url('/about') }}" class="nav-link {{ request()->is('about') ? 'active' : '' }}">About</a>
            </div>
            @auth
                <div class="user-menu">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <div class="user-dropdown">
                        <a href="{{ url('/dashboard') }}">Dashboard</a>
                        <a href="{{ url('/bookings') }}">My Bookings</a>
                        @if(Auth::user()->is_admin)
                            <a href="{{ url('/admin') }}">Admin Panel</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="auth-buttons">
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-solid">Register</a>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="serif">HOTELLY</h3>
                    <p>Experience luxury and comfort in the heart of Copenhagen. Our hotel offers the perfect blend of modern amenities and timeless elegance.</p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="{{ url('/rooms') }}">Rooms & Suites</a></li>
                        <li><a href="{{ url('/dining') }}">Dining</a></li>
                        <li><a href="{{ url('/spa') }}">Spa & Wellness</a></li>
                        <li><a href="{{ url('/events') }}">Events</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contact</h4>
                    <ul>
                        <li>123 Hotel Street</li>
                        <li>Copenhagen, Denmark</li>
                        <li>Phone: +45 1234 5678</li>
                        <li>Email: info@hotelly.com</li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Newsletter</h4>
                    <p>Subscribe to our newsletter for special offers and updates.</p>
                    <form class="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <input type="email" name="email" placeholder="Your email address" required>
                        <button type="submit" class="btn btn-solid">Subscribe</button>
                    </form>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Hotelly. All rights reserved.</p>
                <div class="footer-links">
                    <a href="{{ url('/privacy') }}">Privacy Policy</a>
                    <a href="{{ url('/terms') }}">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
    @stack('scripts')
</body>
</html>
