<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\User;
use App\Models\Profile;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = User::select('*')->with('profile');
        $q = "";
        if (request()->has("q")) {
            $q = request("q");
            $clients->where('name', 'like', '%'.$q.'%');
        }
        $clients = $clients->paginate(5)->appends(request()->except(['page', 'client']));
        $memberships = Membership::select('id','name')->get();
        
        return Inertia::render('Clients/Index', ['clients'=>$clients, 'memberships' => $memberships, 'q' => $q]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cedula' => 'required|string|max:15',
            'phone' => 'required|max:15',
            'address' => 'string',
            'birthday' => 'nullable|date',
            'date_of_purchase' => 'nullable|date',
            'membership' => 'string',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            $profile = new Profile([
                'cedula' => $request->input('cedula'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'birthday' => $request->input('birthday'),
                'date_of_purchase' => $request->input('date_of_purchase'),
                'membership' => $request->input('membership'),
            ]);
            $user->profile()->save($profile);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error en la inserción'], 500);
        }
 
        return Inertia::render('Clients/Index', [
            'clients' => User::with('profile')->paginate(5)->appends(request()->except(['page', 'client']))
        ]);
    }

    public function update(Request $request, $client)
    {
        $request->validate([
            'name' => 'required|string|max:60',
            'email' => 'required|email|max:60',            
            'cedula' => 'required|string|max:15',
            'phone' => 'required|max:15',
            'address' => 'string',
            'birthday' => 'nullable|date',
            'date_of_purchase' => 'nullable|date',
            'membership' => 'string',
        ]);
        DB::beginTransaction();

        try {
            $user = User::with('profile')->findOrFail($client);
            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ]);
            $user->profile->update([            
                'cedula' => $request->input('cedula'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'birthday' => $request->input('birthday'),
                'date_of_purchase' => $request->input('date_of_purchase'),
                'membership' => $request->input('membership'),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error en la inserción'], 500);
        }            
 
        return Inertia::render('Clients/Index', [
            'clients' => User::with('profile')->paginate(5)->appends(request()->except(['page', 'client']))
        ]);
    }

    public function destroy($client)
    {
        $user = User::with('profile')->findOrFail($client);
        $user->delete();
        return Inertia::render('Clients/Index', [
            'clients' => User::with('profile')->paginate(5)->appends(request()->except(['page', 'client']))
        ]);
    }
}
