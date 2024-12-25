<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - {{ config('app.name', 'Hotelly') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="{{ url('/admin') }}" class="logo">HOTELLY</a>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="icon">📊</span>
                    Dashboard
                </a>
                <a href="{{ route('admin.rooms') }}" class="sidebar-link {{ request()->routeIs('admin.rooms*') ? 'active' : '' }}">
                    <span class="icon">🏨</span>
                    Rooms
                </a>
                <a href="{{ route('admin.bookings') }}" class="sidebar-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
                    <span class="icon">📅</span>
                    Bookings
                </a>
                <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <span class="icon">👥</span>
                    Users
                </a>
                <a href="{{ route('admin.reviews') }}" class="sidebar-link {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
                    <span class="icon">⭐</span>
                    Reviews
                </a>
                <a href="{{ route('admin.settings') }}" class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                    <span class="icon">⚙️</span>
                    Settings
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Bar -->
            <header class="admin-header">
                <div class="header-title">
                    <h1>@yield('title', 'Dashboard')</h1>
                </div>
                <div class="header-actions">
                    <div class="user-menu">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <div class="user-dropdown">
                            <a href="{{ url('/') }}">View Site</a>
                            <a href="{{ route('profile.edit') }}">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="admin-content">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
