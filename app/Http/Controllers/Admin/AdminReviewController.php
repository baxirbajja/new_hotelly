<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'room']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('room', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reviews = $query->latest()->paginate(10);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'room']);
        return response()->json($review);
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);
        
        // Recalculate room rating
        $room = $review->room;
        $avgRating = $room->reviews()->where('status', 'approved')->avg('rating');
        $room->update(['rating' => round($avgRating, 1)]);

        return back()->with('success', 'Review approved successfully.');
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);
        
        // Recalculate room rating
        $room = $review->room;
        $avgRating = $room->reviews()->where('status', 'approved')->avg('rating');
        $room->update(['rating' => round($avgRating, 1)]);

        return back()->with('success', 'Review rejected successfully.');
    }

    public function destroy(Review $review)
    {
        $room = $review->room;
        $review->delete();
        
        // Recalculate room rating
        $avgRating = $room->reviews()->where('status', 'approved')->avg('rating');
        $room->update(['rating' => round($avgRating, 1)]);

        return back()->with('success', 'Review deleted successfully.');
    }
}
