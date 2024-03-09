<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientgymController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::resource('/', ClientgymController::class)->only(['index','store'])->names('home');
Route::put('/{concurrence}', [ClientgymController::class, 'update'])->name('home.update');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', ['autorized' => auth()->user()->roles()->first()->name]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    $not_route = ['edit', 'create', 'show'];
    Route::resource('horary', ClientController::class)->only(['index', 'store', 'destroy']);
    Route::get('/payments', [ClientController::class, 'payments'])->name('payments');
    Route::get('/concurrences', [ClientController::class, 'concurrences'])->name('concurrences');
});

require __DIR__.'/auth.php';
