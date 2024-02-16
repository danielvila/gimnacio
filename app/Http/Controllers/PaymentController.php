<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Membership;
use App\Models\User;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {   
        $payments = Payment::with(['user', 'membership'])->paginate(10)->appends(request()->except(['page']));
        $memberships = Membership::select('id','name')->get();
        $users = User::select('id','name')->get();

        return Inertia::render('Payments/Index', ['payments'=> $payments, 'memberships'=> $memberships, 'users'=> $users]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'date_buys' => 'required',            
            'user_id' => 'required',
            'membership_id' => 'required',
        ]);
        Payment::create([
            'amount' => $request->input('amount'),
            'date_buys' => $request->input('date_buys'),
            'user_id' => $request->input('user_id'),
            'membership_id' => $request->input('membership_id'),
        ]);

        return Inertia::render('Payments/Index', [
            'payments' => Payment::with(['user', 'membership'])->paginate(10)->appends(request()->except(['page']))
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'date_buys' => 'required',            
            'user_id' => 'required',
            'membership_id' => 'required',
        ]);       
        
        $payment->update([
            'amount' => $request->input('amount'),
            'date_buys' => $request->input('date_buys'),
            'user_id' => $request->input('user_id'),
            'membership_id' => $request->input('membership_id'),
        ]);     
 
        return Inertia::render('Payments/Index', [
            'payments' => Payment::with(['user', 'membership'])->paginate(10)->appends(request()->except(['page']))
        ]);
    }
}
