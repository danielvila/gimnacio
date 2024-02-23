<?php

namespace App\Http\Controllers;

use App\Models\Concurrence;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConcurrenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $concurrences = Concurrence::where('departure_time', null)->with('user')->orderByDesc('entry_time')->paginate(10)->appends(request()->except(['page']));
        return Inertia::render('Concurrences/Index', [ 'concurrences'=> $concurrences,
                'autorized' => auth()->user()->roles()->first()->name
            ]);
    }

    public function store(Request $request)
    {
        $request->validate([                        
            'user_id' => 'required',
        ]);
        Concurrence::create([
            'entry_time' => now(),            
            'user_id' => $request->input('user_id'),
        ]);

        $concurrences = Concurrence::with('user')->orderByDesc('entry_time')->paginate(10)->appends(request()->except(['page']));
        return Inertia::render('Concurrences/Index', [ 'concurrences'=> $concurrences,
                'autorized' => auth()->user()->roles()->first()->name
            ]);
    }

    public function update(Request $request, Concurrence $concurrence)
    {
        $concurrence->update([
            'departure_time' => now(),
        ]); 

        $concurrences = Concurrence::with('user')->orderByDesc('entry_time')->paginate(10)->appends(request()->except(['page']));
        return Inertia::render('Concurrences/Index', [ 'concurrences'=> $concurrences,
                'autorized' => auth()->user()->roles()->first()->name
            ]);
    }
}
