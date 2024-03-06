<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Concurrence;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ConcurrenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $concurrences = Concurrence::select('*',)->with('user')->orderByDesc('entry_time');
        $date_concurrence = "local";
        if (request()->has("date_concurrence")) {            
            $date_concurrence = request("date_concurrence");
        }
        if ($date_concurrence === "salieron") {
            $concurrences->whereNotNull('departure_time');
        }elseif ($date_concurrence === "local") {
            $concurrences->whereNull('departure_time');
        }  
       
        $concurrences = $concurrences->paginate(10)->appends(request()->except(['page']));
                
        return Inertia::render('Admin/Concurrences/Index', [ 'concurrences'=> $concurrences,
                'date_concurrence'=> $date_concurrence,
                'autorized' => auth()->user()->roles()->first()->name
            ]);
    }

    public function store(Request $request)
    {
        $request->validate([                        
            'user_id' => 'required',
        ]);
        $datos = ['message' => ''];
        $ruta = 'concurrences.index';
        if ($request->input('to_redirect') !== null && !empty($request->input('to_redirect'))) {
            $ruta = $request->input('to_redirect');
        }

        $pago_client = Payment::where('user_id', $request->input('user_id'))->with('membership')->orderByDesc('date_buys', )->first();
        if ($pago_client) {
            $duration = $pago_client->membership->duration;
            $date_buys = $pago_client->date_buys;        
            $fechaActual = now();
            $diferenciaEnDias = $fechaActual->diffInDays($date_buys);
            
            if(($duration - $diferenciaEnDias) > 0){
                Concurrence::create([
                    'entry_time' => now(),            
                    'user_id' => $request->input('user_id'),
                ]);
            }else{
                return redirect()->route($ruta)
                    ->withErrors([
                        'revisa' => '¡No tiene membresia activa!',
                    ]);                           
            }
        } else {
            return redirect()->route($ruta)
                    ->withErrors([
                        'revisa' => '¡Debe comprar una membresia!',
                    ]);
        } 

        return to_route($ruta);
    }
}
