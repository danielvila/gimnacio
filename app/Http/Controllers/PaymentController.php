<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {   
        $payments = Payment::with(['user', 'membership','payment_type'])->orderByDesc('date_buys')->paginate(10)->appends(request()->except(['page']));
        $memberships = Membership::all();
        $users = User::select('id','name')->role('Client')->get();
        $payment_types = PaymentType::select('id','name')->get();
        
        return Inertia::render('Payments/Index', [
                'payments'=> $payments, 'memberships'=> $memberships, 'payment_types' => $payment_types,
                'users'=> $users, 'autorized' => auth()->user()->roles()->first()->name
            ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'date_buys' => 'required',            
            'user_id' => 'required',
            'membership_id' => 'required',
            'payment_type_id' => 'required',
        ]);
        Payment::create([
            'amount' => $request->input('amount'),
            'date_buys' => $request->input('date_buys'),
            'user_id' => $request->input('user_id'),
            'membership_id' => $request->input('membership_id'),
            'payment_type_id' => $request->input('payment_type_id'),
        ]);

        $payments = Payment::with(['user', 'membership','payment_type'])->orderByDesc('date_buys')->paginate(10)->appends(request()->except(['page']));
        $memberships = Membership::select('id','name', 'price')->get();
        $users = User::select('id','name')->with('profile')->role('Client')->get();
        $payment_types = PaymentType::select('id','name')->get();
        
        return Inertia::render('Payments/Index', [
                'payments'=> $payments, 'memberships'=> $memberships, 'payment_types' => $payment_types,
                'users'=> $users, 'autorized' => auth()->user()->roles()->first()->name
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
            'payment_type_id' => $request->input('payment_type_id'),
        ]);     
 
        $payments = Payment::with(['user', 'membership','payment_type'])->orderByDesc('date_buys')->paginate(10)->appends(request()->except(['page']));
        $memberships = Membership::select('id','name', 'price')->get();
        $users = User::select('id','name')->with('profile')->role('Client')->get();
        $payment_types = PaymentType::select('id','name')->get();
        
        return Inertia::render('Payments/Index', [
                'payments'=> $payments, 'memberships'=> $memberships, 'payment_types' => $payment_types,
                'users'=> $users, 'autorized' => auth()->user()->roles()->first()->name
            ]);
    }
}
