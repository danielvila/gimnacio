<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Routine;

class RoutineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rutinas = ['Piernas', 'Brasos', 'Pecho', 'Espalda', 'Pantorrillas', 'Abdomen'];
        foreach ($rutinas as $key => $value) {
            Routine::create([
                'name' => $value,
                'description' => 'No hay nadie que ame el dolor mismo, que lo busque, lo encuentre y lo quiera, simplemente porque es el dolor.',
                'created_at' => now(),
                'updated_at' => now(),
            ]); 
        }
    }
}
