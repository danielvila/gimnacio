<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Coach;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CoachController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coachs = User::select('*')->role('Coach')->with('profile')->orderBy('name')->get();
        return Inertia::render('Admin/Coachs/Index', [ 'coachs'=>$coachs, 'autorized' => auth()->user()->roles()->first()->name
            ]);
    }

    public function show(User $coach)
    {
        $schedules = Schedule::where('user_id', $coach->id)->with('routine')->orderBy('day_of_week')->orderBy('hour')->get();        
        return Inertia::render('Admin/Coachs/Show', [ 'schedules'=>$schedules, 'coach'=>$coach, 'autorized' => auth()->user()->roles()->first()->name
            ]);
    }
}
