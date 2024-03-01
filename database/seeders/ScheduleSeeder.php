<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Routine;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = [1, 2, 3, 4, 5, 6, 7];
        $coachs = User::role('Coach')->orderBy('name')->get();
        $routines = Routine::select('id')->get();

        foreach ($days as $day) {
            foreach ($routines as $routine) {
                foreach ($coachs as $coach) {
                    $horaAleatoria = Carbon::createFromTime(
                        rand(6, 20),
                        rand(0, 1) * 30, 
                        0  
                    );
                    $hora = $horaAleatoria->format('H:i:s');

                    Schedule::create([
                        'day_of_week' => $day,
                        'hour' => $hora,
                        'description' => 'No hay nadie que ame el dolor mismo, que lo busque, lo encuentre y lo quiera, simplemente porque es el dolor.',
                        'user_id'  => $coach->id, 
                        'routine_id'  => $routine->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]); 
                } 
            }
        }
    }
}
