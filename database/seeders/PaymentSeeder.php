<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::select('id')->role('Client')->get();

        $membership = Membership::all();
        $m = count($membership);

        $payment_type = PaymentType::all();
        $p = count($payment_type);

        foreach ($clients as $client) {
            $ship = rand(0, ($m - 1));
            $pago = rand(0, ($p - 1));
            
            $fechaInicio = now();
            $mesesAtras = rand(1, 12);
            $fechaInicio->subMonths($mesesAtras);
            $fechaFin = $fechaInicio->clone();
            $fechaFin->addDays($membership[$ship]->duration);
                   
            Payment::create([
                'amount' => $membership[$ship]->price, 
                'date_buys' => $fechaInicio,
                'date_buys_end' => $fechaFin, 
                'user_id' => $client->id, 
                'membership_id' => $membership[$ship]->id,
                'payment_type_id' => $payment_type[$pago]->id,
                'created_at' => $fechaInicio,
                'updated_at' => $fechaInicio,
            ]); 
        }
    }
}
