<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CoachController;
use App\Http\Controllers\Admin\ConcurrenceController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentTypeController;
use App\Http\Controllers\Admin\RoutineController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\UserController;


$not_route = ['edit', 'create', 'show'];
Route::resource('schedules', ScheduleController::class)->except($not_route);
Route::get('students', [ScheduleController::class, 'clients'])->name('students');

Route::resource('clients', ClientController::class)->except( ['edit', 'create'])->middleware('can:clients.index');
Route::resource('concurrences', ConcurrenceController::class)->only(['index','store'])->middleware('can:concurrences.index');   
Route::resource('payments', PaymentController::class)->except($not_route)->middleware('can:payments.index');
Route::resource('paymentypes', PaymentTypeController::class)->except($not_route)->middleware('can:paymentypes.index');
Route::resource('routines', RoutineController::class)->except($not_route);
Route::resource('coachs', CoachController::class)->only(['index', 'show'])->names('coachs');

Route::resource('users', UserController::class)->except($not_route)->middleware('can:users.index');    
Route::resource('memberships', MembershipController::class)->except($not_route)->middleware('can:memberships.index');