<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function store(Request $request, Booking $booking)
    {
        abort(403, 'Reviews are not allowed at this time.');
    }

    public function update(Request $request, Review $review)
    {
        try {
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|min:10|max:500'
            ]);

            if (!Gate::allows('update-review', $review)) {
                abort(403, 'Unauthorized action.');
            }

            if ($review->is_approved) {
                abort(403, 'Cannot update an approved review.');
            }

            $review->update($validated);

            return back()->with('success', 'Review updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())
                ->withInput();
        }
    }
}
