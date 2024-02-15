<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\Membership;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $durations = ['Mensual'=>['30', 25.99], 'Trimestral'=>['90', 75.99], 'Anual'=>['365', 200.99]];
        foreach ($durations as $key => $value) {
            Membership::create([
                'name' => $key,
                'duration' => $value[0],
                'price' => $value[1],
                'created_at' => now(),
                'updated_at' => now(),
            ]); 
        }
                 
    }
}
