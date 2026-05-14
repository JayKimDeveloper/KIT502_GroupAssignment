<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EventController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Route::view('/', 'index');
Route::view('/index', 'index');
Route::view('/login', 'login');
Route::view('/register', 'register');
Route::view('/events', 'events');
Route::view('/booking', 'booking');
Route::view('/create_event', 'create_event');
Route::view('/manage_events', 'manage_events');
Route::view('/admin_dashboard', 'admin_dashboard');
Route::view('/test_api', 'Test_api');



/* ------------------------------------------------------------------ */
/* Auth actions                                                        */
/* ------------------------------------------------------------------ */
 
Route::middleware('guest')->group(function () {
    Route::post('/login',    [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
});
 
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
 
Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
 
/* ------------------------------------------------------------------ */
/* API — Events                                                        */
/* ------------------------------------------------------------------ */
 
// Public reads (visitor can see published events)
Route::get('/api/events',          [EventController::class, 'index'])->name('api.events.index');
Route::get('/api/events/recent',   [EventController::class, 'recent'])->name('api.events.recent');
 
// Authenticated reads — must come BEFORE /api/events/{id} so 'mine' isn't
// mistaken for an event ID.
Route::middleware('auth')->group(function () {
    Route::get('/api/events/mine', [EventController::class, 'mine'])->name('api.events.mine');
});
 
// Public single-event read
Route::get('/api/events/{id}',     [EventController::class, 'show'])
    ->whereNumber('id')
    ->name('api.events.show');
 
// Writes — must be logged in. (Role check is done inside the controller
// since admin and organiser have different ownership rules.)
Route::middleware('auth')->group(function () {
    Route::post('/api/events',        [EventController::class, 'store'])->name('api.events.store');
    Route::put('/api/events/{id}',    [EventController::class, 'update'])
        ->whereNumber('id')
        ->name('api.events.update');
    Route::delete('/api/events/{id}', [EventController::class, 'destroy'])
        ->whereNumber('id')
        ->name('api.events.destroy');
});
 
/* ------------------------------------------------------------------ */
/* API — Categories                                                    */
/* ------------------------------------------------------------------ */
 
Route::get('/api/categories', [CategoryController::class, 'index'])->name('api.categories.index');
 
 
/* ------------------------------------------------------------------ */
/* API — Bookings                                                      */
/* ------------------------------------------------------------------ */
 
Route::middleware('auth')->group(function () {
    Route::post('/api/bookings',        [BookingController::class, 'store'])->name('api.bookings.store');
    Route::get('/api/bookings/mine',    [BookingController::class, 'mine'])->name('api.bookings.mine');
    Route::delete('/api/bookings/{id}', [BookingController::class, 'destroy'])
        ->whereNumber('id')
        ->name('api.bookings.destroy');
});
 
