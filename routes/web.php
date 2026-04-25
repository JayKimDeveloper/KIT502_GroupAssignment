<?php

use Illuminate\Support\Facades\Route;

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
Route::view('/login', 'login');
Route::view('/register', 'register');
Route::view('/events', 'events');
Route::view('/booking', 'booking');
Route::view('/create-event', 'create_event');
Route::view('/manage-events', 'manage_events');
Route::view('/admin-dashboard', 'admin_dashboard');