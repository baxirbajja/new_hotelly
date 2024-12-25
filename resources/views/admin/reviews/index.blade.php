@extends('layouts.admin')

@section('title', 'Reviews Management')

@section('content')
    <div class="content-section">
        <div class="section-header">
            <h2>Reviews Management</h2>
        </div>

        <!-- Filters -->
        <div class="filters">
            <form action="{{ route('admin.reviews') }}" method="GET" class="filter-form">
                <div class="form-group">
                    <input type="text" name="search" placeholder="Search by guest or room..." 
                           value="{{ request('search') }}" class="form-control">
                </div>
                <div class="form-group">
                    <select name="rating" class="form-control">
                        <option value="">All Ratings</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} Stars
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-outline">Apply Filters</button>
            </form>
        </div>

        <!-- Reviews Table -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reviews as $review)
                        <tr>
                            <td>
                                <div class="guest-info">
                                    <strong>{{ $review->user->name }}</strong>
                                    <small>{{ $review->user->email }}</small>
                                </div>
                            </td>
                            <td>{{ $review->room->name }}</td>
                            <td>
                                <div class="rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                                    @endfor
                                </div>
                            </td>
                            <td>
                                <div class="review-content">
                                    {{ Str::limit($review->comment, 100) }}
                                    @if(strlen($review->comment) > 100)
                                        <button class="btn-link" onclick="showFullReview({{ $review->id }})">
                                            Read More
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $review->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="status-badge status-{{ $review->status }}">
                                    {{ ucfirst($review->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    @if($review->status === 'pending')
                                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this review?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            {{ $reviews->links() }}
        </div>
    </div>

    <!-- Full Review Modal -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Review Details</h3>
            <div class="modal-body">
                <div class="review-details">
                    <div class="review-meta">
                        <div class="guest-info"></div>
                        <div class="rating"></div>
                        <div class="date"></div>
                    </div>
                    <div class="review-text"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .rating {
        display: flex;
        gap: 2px;
    }

    .star {
        color: #ddd;
        font-size: 1.2rem;
    }

    .star.filled {
        color: #ffd700;
    }

    .review-content {
        max-width: 300px;
    }

    .btn-link {
        background: none;
        border: none;
        color: #D4BEA3;
        padding: 0;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .btn-link:hover {
        text-decoration: underline;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-approved {
        background: #d4edda;
        color: #155724;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .modal-content {
        position: relative;
        background: white;
        margin: 10% auto;
        padding: 2rem;
        width: 90%;
        max-width: 600px;
        border-radius: 8px;
    }

    .close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .review-details {
        margin-top: 1.5rem;
    }

    .review-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .review-text {
        line-height: 1.6;
    }
</style>
@endpush

@push('scripts')
<script>
    // Modal functionality
    const modal = document.getElementById('reviewModal');
    const closeBtn = document.getElementsByClassName('close')[0];

    function showFullReview(reviewId) {
        // Fetch review details via AJAX
        fetch(`/admin/reviews/${reviewId}`)
            .then(response => response.json())
            .then(data => {
                const modalContent = modal.querySelector('.modal-content');
                modalContent.querySelector('.guest-info').innerHTML = `
                    <strong>${data.user.name}</strong>
                    <small>${data.user.email}</small>
                `;
                
                modalContent.querySelector('.rating').innerHTML = Array(5)
                    .fill()
                    .map((_, i) => `<span class="star ${i < data.rating ? 'filled' : ''}">★</span>`)
                    .join('');
                
                modalContent.querySelector('.date').textContent = new Date(data.created_at).toLocaleDateString();
                modalContent.querySelector('.review-text').textContent = data.comment;
                
                modal.style.display = 'block';
            });
    }

    closeBtn.onclick = function() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>
@endpush
