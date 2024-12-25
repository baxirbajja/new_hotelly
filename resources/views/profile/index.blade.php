@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <div class="profile-cover"></div>
        <div class="profile-info">
            <div class="profile-avatar">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                @else
                    <div class="avatar-placeholder">{{ substr($user->name, 0, 1) }}</div>
                @endif
            </div>
            <h1>{{ $user->name }}</h1>
            <p>Member since {{ $user->created_at->format('F Y') }}</p>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-sidebar">
            <nav class="profile-nav">
                <a href="#profile" class="active" data-tab="profile">Profile Information</a>
                <a href="#bookings" data-tab="bookings">My Bookings</a>
                <a href="#reviews" data-tab="reviews">My Reviews</a>
                <a href="#security" data-tab="security">Security</a>
            </nav>
        </div>

        <div class="profile-main">
            <!-- Profile Information -->
            <div class="tab-content active" id="profile">
                <div class="content-card">
                    <h2>Profile Information</h2>
                    <form action="{{ route('profile.update') }}" method="POST" class="profile-form">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                   class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-solid">Update Profile</button>
                    </form>
                </div>
            </div>

            <!-- Bookings -->
            <div class="tab-content" id="bookings">
                <div class="content-card">
                    <h2>My Bookings</h2>
                    @if($bookings->count() > 0)
                        <div class="bookings-list">
                            @foreach($bookings as $booking)
                                <div class="booking-card">
                                    <div class="booking-image">
                                        @if($booking->room->image)
                                            <img src="{{ asset('storage/' . $booking->room->image) }}" 
                                                 alt="{{ $booking->room->name }}">
                                        @endif
                                    </div>
                                    <div class="booking-details">
                                        <h3>{{ $booking->room->name }}</h3>
                                        <div class="booking-dates">
                                            <span>{{ $booking->check_in->format('M d, Y') }}</span>
                                            <span>to</span>
                                            <span>{{ $booking->check_out->format('M d, Y') }}</span>
                                        </div>
                                        <div class="booking-meta">
                                            <span class="status-badge status-{{ $booking->status }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                            <span class="price">${{ number_format($booking->total_price, 2) }}</span>
                                        </div>
                                        <div class="booking-actions">
                                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline btn-sm">
                                                View Details
                                            </a>
                                            @if($booking->status === 'pending')
                                                <form action="{{ route('bookings.cancel', $booking) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <p>You haven't made any bookings yet.</p>
                            <a href="{{ route('rooms') }}" class="btn btn-solid">Browse Rooms</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reviews -->
            <div class="tab-content" id="reviews">
                <div class="content-card">
                    <h2>My Reviews</h2>
                    @if($reviews->count() > 0)
                        <div class="reviews-list">
                            @foreach($reviews as $review)
                                <div class="review-card">
                                    <div class="review-header">
                                        <h3>{{ $review->room->name }}</h3>
                                        <div class="rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="review-date">{{ $review->created_at->format('M d, Y') }}</p>
                                    <p class="review-comment">{{ $review->comment }}</p>
                                    <div class="review-actions">
                                        <button class="btn btn-outline btn-sm" onclick="editReview({{ $review->id }})">
                                            Edit
                                        </button>
                                        <form action="{{ route('reviews.destroy', $review) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <p>You haven't written any reviews yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Security -->
            <div class="tab-content" id="security">
                <div class="content-card">
                    <h2>Security Settings</h2>
                    <form action="{{ route('profile.password') }}" method="POST" class="profile-form">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" 
                                   class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" 
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-solid">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .profile-header {
        position: relative;
        margin-bottom: 2rem;
    }

    .profile-cover {
        height: 200px;
        background: linear-gradient(45deg, #D4BEA3, #987654);
        border-radius: 8px;
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: -50px;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid white;
        margin-bottom: 1rem;
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        background: #D4BEA3;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
    }

    .profile-content {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 2rem;
    }

    .profile-nav {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .profile-nav a {
        padding: 1rem;
        color: #333;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .profile-nav a:hover {
        background: #f8f9fa;
    }

    .profile-nav a.active {
        background: #D4BEA3;
        color: white;
    }

    .content-card {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .content-card h2 {
        font-family: 'Playfair Display', serif;
        margin-bottom: 1.5rem;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .booking-card {
        display: flex;
        gap: 1.5rem;
        padding: 1.5rem;
        border: 1px solid #eee;
        border-radius: 4px;
        margin-bottom: 1rem;
    }

    .booking-image {
        width: 150px;
        height: 100px;
    }

    .booking-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
    }

    .booking-details {
        flex: 1;
    }

    .booking-dates {
        color: #666;
        margin: 0.5rem 0;
    }

    .booking-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 1rem 0;
    }

    .review-card {
        padding: 1.5rem;
        border: 1px solid #eee;
        border-radius: 4px;
        margin-bottom: 1rem;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .rating .star {
        color: #ddd;
    }

    .rating .star.filled {
        color: #ffd700;
    }

    .review-date {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #666;
    }

    @media (max-width: 768px) {
        .profile-content {
            grid-template-columns: 1fr;
        }

        .profile-nav {
            flex-direction: row;
            overflow-x: auto;
            padding-bottom: 1rem;
        }

        .booking-card {
            flex-direction: column;
        }

        .booking-image {
            width: 100%;
            height: 200px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Tab switching functionality
    document.querySelectorAll('.profile-nav a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const tab = e.target.getAttribute('data-tab');
            
            // Update active tab
            document.querySelectorAll('.profile-nav a').forEach(el => el.classList.remove('active'));
            e.target.classList.add('active');
            
            // Show selected content
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.getElementById(tab).classList.add('active');
        });
    });
</script>
@endpush
