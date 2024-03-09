<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Concurrence;
use App\Models\Payment;
use App\Models\Schedule;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['routine', 'user'])->orderBy('day_of_week')->orderBy('hour')->get();
        $payment = Payment::with(['membership','payment_type'])->where('user_id', auth()->user()->id)->where('date_buys_end', '>', now())->get();
       
        return Inertia::render('Client/Index', [ 'schedules'=>$schedules, 
            'payment'=>$payment, 'horaries'=>auth()->user()->horaries, 
            'autorized' => auth()->user()->roles()->first()->name
            ]);
    }

    public function concurrences()
    {
        $concurrences = Concurrence::where('user_id', auth()->user()->id)->orderByDesc('entry_time')->paginate(10)->appends(request()->except(['page']));
 
        return Inertia::render('Client/Concurrences', ['concurrences'=>$concurrences,
                'autorized' => auth()->user()->roles()->first()->name
            ]);
    }

    public function payments()
    {
        $payments = Payment::with(['membership','payment_type'])->where('user_id', auth()->user()->id)->orderByDesc('date_buys')->paginate(10)->appends(request()->except(['page']));

        return Inertia::render('Client/Payments', ['payments'=>$payments, 
                'autorized' => auth()->user()->roles()->first()->name
            ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|numeric',
        ]);       

        DB::beginTransaction();

        try {
            DB::table('schedule_user')->insert([
                'schedule_id' => $request->input('schedule_id'),
                'user_id' => auth()->user()->id
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error en la inserción'], 500);
        }
        
        return to_route('horary.index');
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            DB::table('schedule_user')->where('schedule_id', $id)->where('user_id', auth()->user()->id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error en la inserción'], 500);
        }
        
        return to_route('horary.index');
    }
}
