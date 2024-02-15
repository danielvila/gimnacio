<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::all();
        $membership = Membership::all();
        $cantidad = count($membership);
        foreach ($clients as $client) {
            $ship = rand(0, 2);
            Payment::create([
                'amount' => $membership[$ship]->price, 
                'date_buys' => now(), 
                'user_id' => $client->id, 
                'membership_id' => $membership[$ship]->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]); 
        }
    }
}
