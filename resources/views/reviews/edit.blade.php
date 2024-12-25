@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Edit Review</h1>
            <a href="{{ route('bookings') }}" class="text-blue-500 hover:text-blue-600">← Back to Bookings</a>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <form action="{{ route('reviews.update', $review) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Room</label>
                        <p class="text-gray-600">{{ $review->room->name }}</p>
                    </div>

                    <div class="mb-6">
                        <label for="rating" class="block text-gray-700 text-sm font-bold mb-2">Rating</label>
                        <div class="flex items-center space-x-2">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $i }}" 
                                           class="hidden" 
                                           {{ old('rating', $review->rating) == $i ? 'checked' : '' }}>
                                    <svg class="w-8 h-8 {{ old('rating', $review->rating) >= $i ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </label>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="comment" class="block text-gray-700 text-sm font-bold mb-2">Comment</label>
                        <textarea name="comment" id="comment" rows="4" 
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                  required>{{ old('comment', $review->comment) }}</textarea>
                        @error('comment')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('input[name="rating"]');
    const starIcons = document.querySelectorAll('svg');

    stars.forEach((star, index) => {
        star.addEventListener('change', function() {
            const rating = this.value;
            starIcons.forEach((icon, i) => {
                icon.classList.toggle('text-yellow-400', i < rating);
                icon.classList.toggle('text-gray-300', i >= rating);
            });
        });
    });
});
</script>
@endpush

@endsection
