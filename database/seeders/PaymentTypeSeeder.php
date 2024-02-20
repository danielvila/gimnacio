<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipo_pagos = ['Efectivo', 'Yappy', 'Tarjeta'];
        foreach ($tipo_pagos as $key => $value) {
            PaymentType::create([
                'name' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]); 
        }
    }
}
