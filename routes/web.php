<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminRoomController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');

// Authentication Routes
require __DIR__.'/auth.php';

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/rooms/{room}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Reviews
    Route::post('/rooms/{room}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rooms Management
    Route::get('/rooms', [AdminRoomController::class, 'index'])->name('rooms');
    Route::get('/rooms/create', [AdminRoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [AdminRoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}/edit', [AdminRoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{room}', [AdminRoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [AdminRoomController::class, 'destroy'])->name('rooms.destroy');

    // Bookings Management
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/create', [AdminBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::put('/bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
    Route::post('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');

    // Reviews Management
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews');
    Route::get('/reviews/{review}', [AdminReviewController::class, 'show'])->name('reviews.show');
    Route::post('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Users Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Reports
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    Route::get('/reports/bookings', [DashboardController::class, 'bookingsReport'])->name('reports.bookings');
    Route::get('/reports/revenue', [DashboardController::class, 'revenueReport'])->name('reports.revenue');
    Route::get('/reports/occupancy', [DashboardController::class, 'occupancyReport'])->name('reports.occupancy');

    // Settings
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::put('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
});
