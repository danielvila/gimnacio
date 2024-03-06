<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Membership;
use Inertia\Inertia;

class MembershipController extends Controller
{
    public function index()
    {        
        return Inertia::render('Admin/Memberships/Index', ['memberships'=> Membership::all(), 'autorized' => auth()->user()->roles()->first()->name]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',            
            'duration' => 'required|string',
            'price' => 'required|numeric',
        ]);
        Membership::create([
            'name' => $request->input('name'),
            'duration' => $request->input('duration'),
            'price' => $request->input('price'),
        ]);

        return to_route('memberships.index');
    }

    public function update(Request $request, Membership $membership)
    {
        $request->validate([
            'name' => 'required|string|max:255',            
            'duration' => 'required|string',
            'price' => 'required|numeric',
        ]);
        //$membership = Membership::findOrFail($id);
        $membership->update([
            'name' => $request->input('name'),
            'duration' => $request->input('duration'),
            'price' => $request->input('price'),
        ]);

        return to_route('memberships.index');
    }

    public function destroy(Membership $membership)
    {
        //$membership = Membership::findOrFail($id);
        $membership->delete();
        return to_route('memberships.index');
    }
}
