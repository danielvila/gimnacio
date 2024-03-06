<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Routine;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::where('user_id', auth()->user()->id)->with('routine')->orderBy('day_of_week')->orderBy('hour')->get();
        $routines = Routine::select('id','name')->get();
        return Inertia::render('Admin/Schedules/Index', ['schedules'=> $schedules, 'routines'=> $routines, 'autorized' => auth()->user()->roles()->first()->name]);
    } 

    public function store(Request $request)
    {
        $request->validate([
            'day_of_week' => 'required|numeric', 
            'hour' => 'required|string|max:255', 
            'description' => 'required|string|max:255', 
            'routine_id' => 'required'
        ]);
        Schedule::create([
            'day_of_week' => $request->input('day_of_week'),
            'hour' => $request->input('hour'),
            'description' => $request->input('description'),
            'user_id' => auth()->user()->id, 
            'routine_id' => $request->input('routine_id'),
        ]);

        return to_route('schedules.index');
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'day_of_week' => 'required|numeric', 
            'hour' => 'required|string|max:255', 
            'description' => 'required|string|max:255', 
            'routine_id' => 'required'
        ]);
        
        $schedule->update([
            'day_of_week' => $request->input('day_of_week'),
            'hour' => $request->input('hour'),
            'description' => $request->input('description'),
            'user_id' => auth()->user()->id, 
            'routine_id' => $request->input('routine_id'),
        ]);

        return to_route('schedules.index');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return to_route('schedules.index');
    }
}
