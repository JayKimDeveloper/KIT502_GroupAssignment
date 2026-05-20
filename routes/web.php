<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index');
Route::view('/index', 'index');
Route::view('/login', 'login')->name('login');
Route::view('/register', 'register');
Route::view('/events', 'events');
// Route::view('/events/{id}', 'event_detail');
Route::view('/booking', 'booking');
Route::view('/my_bookings', 'booking');
Route::view('/create_event', 'create_event');
Route::view('/manage_events', 'manage_events');
Route::view('/admin_dashboard', 'admin_dashboard');

// Auth                                                        
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Route::get('/me', [AuthController::class, 'me'])->name('auth.me');



// Events
Route::get('/api/events', [EventController::class, 'index'])->name('api.events.index');
Route::get('/api/events/recent', [EventController::class, 'recent'])->name('api.events.recent');

Route::middleware('auth')->group(function () {
    Route::get('/api/events/mine', [EventController::class, 'mine'])->name('api.events.mine');
});

Route::get('/events/{id}', [EventController::class, 'show'])
    ->whereNumber('id')
    ->name('events.detail');


Route::middleware('auth')->group(function () {
    Route::post('/api/events', [EventController::class, 'store'])->name('api.events.store');

    Route::put('/api/events/{id}', [EventController::class, 'update'])
        ->whereNumber('id')
        ->name('api.events.update');

    Route::delete('/api/events/{id}', [EventController::class, 'destroy'])
        ->whereNumber('id')
        ->name('api.events.destroy');

    Route::get('/api/events/{id}/bookings', [BookingController::class, 'forEvent'])
        ->whereNumber('id')
        ->name('api.events.bookings');
});


//Categories
Route::get('/api/categories', [CategoryController::class, 'index'])->name('api.categories.index');

//Bookings
Route::middleware('auth')->group(function () {
    Route::post('/api/bookings', [BookingController::class, 'store'])->name('api.bookings.store');

    Route::get('/api/bookings/mine', [BookingController::class, 'mine'])->name('api.bookings.mine');

    Route::delete('/api/bookings/{id}', [BookingController::class, 'destroy'])
        ->whereNumber('id')
        ->name('api.bookings.destroy');
});

//Admin
Route::middleware('auth')->group(function () {
    Route::get('/api/admin/stats', [AdminController::class, 'stats'])->name('api.admin.stats');

    Route::get('/api/admin/users', [AdminController::class, 'users'])->name('api.admin.users');

    Route::post('/api/admin/users', [AdminController::class, 'storeUser'])->name('api.admin.users.store');

    Route::put('/api/admin/users/{id}', [AdminController::class, 'updateUser'])
        ->whereNumber('id')
        ->name('api.admin.users.update');

    Route::patch('/api/admin/users/{id}/role', [AdminController::class, 'updateUserRole'])
        ->whereNumber('id')
        ->name('api.admin.users.role');

    Route::delete('/api/admin/users/{id}', [AdminController::class, 'destroyUser'])
        ->whereNumber('id')
        ->name('api.admin.users.destroy');

    Route::get('/api/admin/events', [AdminController::class, 'events'])->name('api.admin.events');
});