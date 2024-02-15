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
        $durations = ['Diario'=>'1', 'Semanal'=>'7', 'Mensual'=>'30', 'Anual'=>'365'];
        foreach ($durations as $key => $value) {
            Membership::create([
                'name' => $key,
                'duration' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]); 
        }
                 
    }
}
