<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientgymController;
use App\Http\Controllers\ConcurrenceController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\UserController;
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
    Route::resource('clients', ClientController::class)->except( ['edit', 'create'])->middleware('can:clients.index');
    Route::resource('concurrences', ConcurrenceController::class)->only(['index','store'])->middleware('can:concurrences.index');   
    Route::resource('payments', PaymentController::class)->except($not_route)->middleware('can:payments.index');
    Route::resource('paymentypes', PaymentTypeController::class)->except($not_route)->middleware('can:paymentypes.index');
    Route::resource('routines', RoutineController::class)->except($not_route);

    Route::resource('users', UserController::class)->except($not_route)->middleware('can:users.index');    
    Route::resource('memberships', MembershipController::class)->except($not_route)->middleware('can:memberships.index');
 
});

require __DIR__.'/auth.php';
