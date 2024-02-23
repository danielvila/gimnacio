<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConcurrenceController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', ['autorized' => auth()->user()->roles()->first()->name]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    $not_route = ['edit', 'create', 'show'];
    Route::resource('clients', ClientController::class)->except($not_route)->middleware('can:clients.index');
    Route::resource('concurrences', ConcurrenceController::class)->only(['index','store','update']);   
    Route::resource('payments', PaymentController::class)->except($not_route)->middleware('can:payments.index');


    Route::resource('users', UserController::class)->only(['index','store','update','destroy'])->middleware('can:users.index');
    Route::resource('memberships', MembershipController::class)->except($not_route)->middleware('can:memberships.index');
 
});

require __DIR__.'/auth.php';
