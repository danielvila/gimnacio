<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {   
        $payments = Payment::select('*')->with(['user', 'membership','payment_type'])->orderByDesc('date_buys');
        $date_start = "";
        $date_end = "";
        if (request()->has("date_start") and request()->has("date_end")) {
            $date_start = request("date_start");
            $date_end = request("date_end");
            $payments->whereBetween(DB::raw('DATE(date_buys)'), [$date_start, $date_end]);
        }else{
            $date_start = now();
            $date_end = Payment::max('date_buys_end');            
            $payments->whereDate('date_buys_end', '>', $date_start);
        }
       
        $payments = $payments->paginate(10)->appends(request()->except('page'));

        $memberships = Membership::all();
        $users = User::select('id','name')->role('Client')->get();
        
        $payment_types = PaymentType::select('id','name')->get();
        
        return Inertia::render('Admin/Payments/Index', [ 'payments'=> $payments,
                'date_start'=> $date_start, 'date_end' => $date_end,
                'memberships'=> $memberships, 'payment_types' => $payment_types,
                'users'=> $users, 'autorized' => auth()->user()->roles()->first()->name
            ]);
    }

    public function facelect($id, $request){
        $amount = $request->input('amount');
        $taxe = number_format($request->input('amount') * 0.07, 2, '.', '');
        $data = [
            "header" => [
                "id" => 1,
                "environment" => "2"
            ],
            "document" => [
                "fd_number" => $id,
                "receptor" => [
                    "type" => str_pad($request->input('type_receptor'), 2, '0', STR_PAD_LEFT),
                    "name" => $request->input('name'),
                    "ruc_type" => $request->input('type_contrib'),
                    "address" => "",
                    "email" => "",
                    "ruc" => "",
                    "dv" => ""
                ],
                "items" => [
                    [
                        "line" => 1,
                        "price" => $amount,
                        "mu" => "und",
                        "quantity" => 1,
                        "description" => $request->input('membership'),
                        "taxes" => [
                            [
                                "type" => "01",
                                "amount" => $taxe,
                                "code" => "01"
                            ]
                        ],
                        "discount" => 0.0,
                        "internal_code" => $request->input('membership_id')
                    ]
                ],
                "payments" => [
                    [
                        "type" => str_pad($request->input('payment_type_id'), 2, '0', STR_PAD_LEFT),
                        "amount" => $amount + $taxe,
                        "description" => "Medio de pago ".$request->input('payment_name')
                    ]
                ],
                "type" => "01",
                "info" => "Pago por membresia, tiene derecho a usar las instalaciones del gimnasio."
            ]
        ];
        
        // Realizar una solicitud HTTP POST
        $response = Http::withHeaders([
            'X-FF-Company' => '2a45cee2-77a5-49ca-b270-fa70966592e8',
            'X-FF-API-Key' => '3rU19SJfOWaWkydid4jmbedln-f7oYvkSd0nw-6vZT8xRfdTymd2KK2DPu-DQCjEeSaZ_bHRugSiKc95zOJq1tpSBaKA1k1CX-idv2oeH3sgMiXlzRo2h_4HzeFPpFWh',
            'X-FF-Branch' => 'fe175fcf-9af4-4aad-8f7b-dedb4e0952c4',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->post('https://backend-qa-api.facturafacil.com.pa/api/pac/reception_fe/detailed/', $data);

        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            return $response->body();
        } else {
            $errorCode = $response->status();
            return 'Unexpected HTTP status: ' . $response->status() . ' ' . $response->body();
        }
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
        
        $payment = Payment::create($this->arrayData($request));
        $cafe = $this->facelect($payment->id, $request);
        //dd($cafe);
        return to_route('payments.index');
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'date_buys' => 'required',            
            'user_id' => 'required',
            'membership_id' => 'required',
        ]);

        $payment->update($this->arrayData($request));
        
        return to_route('payments.index');
    }

    public function arrayData($request){
        $membership = Membership::findOrFail($request->input('membership_id'));
        $fechaInicio = Carbon::parse($request->input('date_buys'));
        $fechaFin = $fechaInicio->clone();
        $fechaFin->addDays($membership->duration);

        return [
            'amount' => $request->input('amount'),
            'date_buys' => $fechaInicio,
            'date_buys_end' => $fechaFin,
            'user_id' => $request->input('user_id'),
            'membership_id' => $request->input('membership_id'),
            'payment_type_id' => $request->input('payment_type_id'),
        ];
    }
}
