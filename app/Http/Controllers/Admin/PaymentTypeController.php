<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\PaymentType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentTypeController extends Controller
{
    public function index()
    {        
        return Inertia::render('Admin/Paymentypes/Index', ['paymentypes'=> PaymentType::all(), 'autorized' => auth()->user()->roles()->first()->name]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        PaymentType::create([
            'name' => $request->input('name'),
        ]);

        return to_route('paymentypes.index');
    }

    public function update(Request $request, PaymentType $paymentype)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $paymentype->update([
            'name' => $request->input('name'),
        ]);

        return to_route('paymentypes.index');
    }

    public function destroy(PaymentType $paymentype)
    {
        $paymentype->delete();
        return to_route('paymentypes.index');
    }
}
