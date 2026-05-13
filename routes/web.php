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
Route::view('/index', 'index');
Route::view('/login', 'login');
Route::view('/register', 'register');
Route::view('/events', 'events');
Route::view('/booking', 'booking');
Route::view('/create_event', 'create_event');
Route::view('/manage_events', 'manage_events');
Route::view('/admin_dashboard', 'admin_dashboard');