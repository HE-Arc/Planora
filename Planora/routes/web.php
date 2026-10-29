<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return view('home');
});

Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/event/{id}', [EventController::class, 'show'])->name('events.show');
Route::get('/event/add', [EventController::class, 'add'])->name('events.add');

Route::resource('events', EventController::class);

// Route::post('/logout', [AuthController::class, 'logout'])
//     ->name('logout');
