<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membership;
use Inertia\Inertia;

class MembershipController extends Controller
{
    public function index()
    {        
        return Inertia::render('Memberships/Index', ['memberships'=> Membership::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',            
            'duration' => 'required|string',
        ]);
        Membership::create([
            'name' => $request->input('name'),
            'duration' => $request->input('duration'),
        ]);

        return Inertia::render('Memberships/Index', [
            'memberships' => Membership::all()
        ]);
    }

    public function update(Request $request, Membership $membership)
    {
        $request->validate([
            'name' => 'required|string|max:255',            
            'duration' => 'required|string',
        ]);
        //$membership = Membership::findOrFail($id);
        $membership->update([
            'name' => $request->input('name'),
            'duration' => $request->input('duration'),
        ]);

        return Inertia::render('Memberships/Index', [
            'memberships' => Membership::all()
        ]);
    }

    public function destroy(Membership $membership)
    {
        //$membership = Membership::findOrFail($id);
        $membership->delete();
        return Inertia::render('Memberships/Index', [
            'memberships' => Membership::all()
        ]);
    }
}
