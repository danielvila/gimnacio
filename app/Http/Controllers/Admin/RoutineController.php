<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Routine;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoutineController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Routines/Index', ['routines'=> Routine::all(), 'autorized' => auth()->user()->roles()->first()->name]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',            
            'description' => 'required|string|max:255',
        ]);
        Routine::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return to_route('routines.index');
    }

    public function update(Request $request, Routine $routine)
    {
        $request->validate([
            'name' => 'required|string|max:100',            
            'description' => 'required|string|max:255',
        ]);
        $routine->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return to_route('routines.index');
    }

    public function destroy(Routine $routine)
    {
        $routine->delete();
        return to_route('routines.index');
    }
}
