<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles');
        $q = "";
        if (request()->has("q")) {
            $q = request("q");
            $users->where('name', 'like', '%'.$q.'%');
        }
        $users = $users->paginate(10)->appends(request()->except(['page', 'client']));
        $roles = Role::select('id','name')->get();
       
        return Inertia::render('Admin/Users/Index', ['users'=>$users, 'roles' => $roles, 'q' => $q, 'autorized' => auth()->user()->roles()->first()->name]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ])->assignRole('Client');
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error en la inserción'], 500);
        }
        
        return to_route('users.index');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:60',
            'email' => 'required|email|max:60',
        ]);
        DB::beginTransaction();

        try {            
            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ]);
            $user->roles()->sync($request->input('roles'));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error en la inserción'], 500);
        }            
 
        return to_route('users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return to_route('users.index');
    }
}
