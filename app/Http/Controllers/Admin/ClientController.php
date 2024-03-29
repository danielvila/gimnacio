<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Concurrence;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Profile;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $clients = User::select('*')->with('profile')->role('Client')->orderBy('name');
        $q = "";
        if (request()->has("q")) {
            $q = request("q");
            $clients->where('name', 'like', '%'.$q.'%');
        }
        $clients = $clients->paginate(10)->appends(request()->except(['page', 'client']));
        $memberships = Membership::all();
        $payment_types = PaymentType::select('id','name')->get();

        return Inertia::render('Admin/Clients/Index', [ 'clients'=>$clients, 
                'memberships' => $memberships, 'payment_types' => $payment_types, 
                'q' => $q, 'autorized' => auth()->user()->roles()->first()->name
            ]);
    }

    public function show(User $client)
    {
        $full_client = User::with(['profile', 'concurrences'])->findOrFail($client->id);
        $payments = Payment::with(['membership','payment_type'])->where('user_id', $client->id)->orderByDesc('date_buys')->paginate(10)->appends(request()->except(['page']));
        $concurrences = Concurrence::where('user_id', $client->id)->orderByDesc('entry_time')->paginate(10)->appends(request()->except(['page']));
 
        return Inertia::render('Admin/Clients/Show', [ 'full_client'=>$full_client, 
                'payments'=>$payments, 'concurrences'=>$concurrences,
                'autorized' => auth()->user()->roles()->first()->name
            ]);
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
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ])->assignRole('Client');

            $profile = new Profile([
                'cedula' => $request->input('cedula'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'birthday' => $request->input('birthday'),
            ]);
            $user->profile()->save($profile);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error en la inserción'], 500);
        }
 
        return to_route('clients.index');
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
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error en la inserción'], 500);
        }            
 
       return to_route('clients.index');
    }

    public function destroy($client)
    {
        $user = User::with('profile')->findOrFail($client);
        $user->delete();
        return to_route('clients.index');
    }
}
