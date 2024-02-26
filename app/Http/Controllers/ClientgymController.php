<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use App\Models\Concurrence;
use App\Models\Payment;
use App\Models\User;

class ClientgymController extends Controller
{
    public function index()
    {
        $message = Session::get('flash.message');

        $concurrences = Concurrence::where('departure_time', null)->with('user')->orderByDesc('entry_time')->paginate(10)->appends(request()->except(['page']));
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'concurrences'=> $concurrences,
            'message' => $message,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(
            [                        
                'username' => 'required',
                'password' => 'required',
            ],
            [
                'username.required' => 'El campo Nombre de usuario es requerido.',
                'password.required' => 'El campo Contraseña es requerido.',
            ]
        );
        $datos = ['message' => ''];

        $pago_client = User::where('name', );
        
        $username = $request->input('username');
        $password = $request->input('password');
        
        $user = User::where('username', $username)->first();
        if ($user && \Hash::check($password, $user->password)){
           $pago_client = Payment::where('user_id', $user->id)->with('membership')->orderByDesc('date_buys', )->first();
            if ($pago_client) {
                $duration = $pago_client->membership->duration;
                $date_buys = $pago_client->date_buys;        
                $fechaActual = now();
                $diferenciaEnDias = $fechaActual->diffInDays($date_buys);
                
                if(($duration - $diferenciaEnDias) > 0){                   
                    Concurrence::create([
                        'entry_time' => now(),            
                        'user_id' => $user->id,
                    ]);
                }else{
                    return redirect()->route('home.index')
                    ->withErrors([
                        'revisa' => '¡No tiene membresia activa!',
                    ]);                        
                }
            } else {
                return redirect()->route('home.index')
                    ->withErrors([
                        'revisa' => '¡Debe comprar una membresia!',
                    ]); 
            } 
        }else{
            return redirect()->route('home.index')
                    ->withErrors([
                        'revisa' => 'Datos incorrectos revise por favor.',
                    ]);
        }

        return to_route('home.index');
    }

    public function update(Request $request, Concurrence $concurrence)
    {
        $concurrence->update([
            'departure_time' => now(),
        ]); 

        return to_route('home.index');
    }
}
