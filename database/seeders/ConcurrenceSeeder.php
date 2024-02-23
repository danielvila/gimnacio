<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Concurrence;
use App\Models\User;

class ConcurrenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = User::select('id')->role('Client')->get();
        foreach ($clients as $client) {
            for ($i=0; $i < 3; $i++) { 
                $fechaInicio = now();
                $mesesAtras = rand(1, 12);
                $fechaInicio->subMonths($mesesAtras);
                $fechaFin = $fechaInicio->clone();
                $fechaFin->addHours(2);
                Concurrence::create([
                    'entry_time' => $fechaInicio, 
                    'departure_time' => $fechaFin, 
                    'user_id' => $client->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }                 
        }
    }
}
