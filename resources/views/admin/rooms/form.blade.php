@extends('layouts.admin')

@section('title', isset($room) ? 'Edit Room' : 'Add New Room')

@section('content')
    <div class="content-section">
        <div class="section-header">
            <h2>{{ isset($room) ? 'Edit Room' : 'Add New Room' }}</h2>
            <a href="{{ route('admin.rooms') }}" class="btn btn-outline">Back to Rooms</a>
        </div>

        <form action="{{ isset($room) ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}" 
              method="POST" enctype="multipart/form-data" class="admin-form">
            @csrf
            @if(isset($room))
                @method('PUT')
            @endif

            <div class="form-grid">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3>Basic Information</h3>
                    
                    <div class="form-group">
                        <label for="name">Room Name</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $room->name ?? '') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="type">Room Type</label>
                        <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            <option value="standard" {{ old('type', $room->type ?? '') == 'standard' ? 'selected' : '' }}>Standard</option>
                            <option value="deluxe" {{ old('type', $room->type ?? '') == 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                            <option value="suite" {{ old('type', $room->type ?? '') == 'suite' ? 'selected' : '' }}>Suite</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="capacity">Capacity</label>
                            <input type="number" id="capacity" name="capacity" min="1" 
                                   class="form-control @error('capacity') is-invalid @enderror"
                                   value="{{ old('capacity', $room->capacity ?? '') }}" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="size">Size (m²)</label>
                            <input type="number" id="size" name="size" min="1" 
                                   class="form-control @error('size') is-invalid @enderror"
                                   value="{{ old('size', $room->size ?? '') }}" required>
                            @error('size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing and Availability -->
                <div class="form-section">
                    <h3>Pricing & Availability</h3>
                    
                    <div class="form-group">
                        <label for="price">Price per Night ($)</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" 
                               class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', $room->price ?? '') }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="is_available">Availability Status</label>
                        <select id="is_available" name="is_available" class="form-control">
                            <option value="1" {{ old('is_available', $room->is_available ?? true) ? 'selected' : '' }}>Available</option>
                            <option value="0" {{ old('is_available', $room->is_available ?? true) ? '' : 'selected' }}>Unavailable</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="form-section">
                <h3>Description & Details</h3>
                
                <div class="form-group">
                    <label for="description">Room Description</label>
                    <textarea id="description" name="description" rows="4" 
                              class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $room->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="view_type">View Type</label>
                    <select id="view_type" name="view_type" class="form-control">
                        <option value="city" {{ old('view_type', $room->view_type ?? '') == 'city' ? 'selected' : '' }}>City View</option>
                        <option value="garden" {{ old('view_type', $room->view_type ?? '') == 'garden' ? 'selected' : '' }}>Garden View</option>
                        <option value="pool" {{ old('view_type', $room->view_type ?? '') == 'pool' ? 'selected' : '' }}>Pool View</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Amenities</label>
                    <div class="amenities-grid">
                        @php
                            $amenities = old('amenities', $room->amenities ?? []);
                            if (!is_array($amenities)) {
                                $amenities = json_decode($amenities, true) ?? [];
                            }
                        @endphp
                        
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="wifi" 
                                   {{ in_array('wifi', $amenities) ? 'checked' : '' }}>
                            WiFi
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="tv" 
                                   {{ in_array('tv', $amenities) ? 'checked' : '' }}>
                            TV
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="ac" 
                                   {{ in_array('ac', $amenities) ? 'checked' : '' }}>
                            Air Conditioning
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="minibar" 
                                   {{ in_array('minibar', $amenities) ? 'checked' : '' }}>
                            Minibar
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="safe" 
                                   {{ in_array('safe', $amenities) ? 'checked' : '' }}>
                            Safe
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="desk" 
                                   {{ in_array('desk', $amenities) ? 'checked' : '' }}>
                            Work Desk
                        </label>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="form-section">
                <h3>Room Images</h3>
                
                <div class="form-group">
                    <label for="image">Room Image</label>
                    <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror"
                           accept="image/*" {{ isset($room) ? '' : 'required' }}>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if(isset($room) && $room->image)
                    <div class="current-image">
                        <img src="{{ asset('storage/' . $room->image) }}" alt="{{ $room->name }}">
                        <p>Current Image</p>
                    </div>
                @endif
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-solid">{{ isset($room) ? 'Update Room' : 'Create Room' }}</button>
                <a href="{{ route('admin.rooms') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section h3 {
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 1rem;
        font-family: 'Playfair Display', serif;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .amenities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .current-image {
        margin-top: 1rem;
    }

    .current-image img {
        max-width: 200px;
        border-radius: 4px;
    }

    .current-image p {
        margin-top: 0.5rem;
        color: #666;
        font-size: 0.9rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
@endpush
