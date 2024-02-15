<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Profile;
use Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        
        DB::beginTransaction();
        try {
            $daniel = User::create([
                'name' => 'daniel',
                'email' => 'daniel@test.com',
                'email_verified_at' => now(),
                'password' => Hash::make('enero2050'), 
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $fechaActual = now();
            $fechaAleatoria = $fechaActual->subYears(rand(20, 30));
            $profile = new Profile([
                'cedula' => fake()->unique()->uuid(),
                'phone' => fake()->e164PhoneNumber(),
                'address' => fake()->address(),
                'birthday' => $fechaAleatoria,
                'date_of_purchase' => now(),
                'membership' => fake()->randomElement([1, 2, 3, 4]),
            ]);
            $daniel->profile()->save($profile);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();            
        }
       /* DB::table('personal_access_tokens')->insert([
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => '1',
            'name' => 'daniel',
            'token' => 'b55760f4620861191b89e9685bd026699a175ce9edda08d433358132edcf2e14',
            'abilities' => '["read","create","update","delete"]',
            'last_used_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);*/
            
        try {
           $jose = User::create([
                'name' => 'jose',
                'email' => 'jose@test.com',
                'email_verified_at' => now(),
                'password' => '$2y$10$wItjvHmUHEZt0HwuacDj5OP7w2rGIq8jioE0TppXL./GqP/7uKmrW', // enero2050
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $fechaActualj = now();
            $fechaAleatoriaj = $fechaActualj->subYears(rand(20, 30));
            $profilej = new Profile([
                'cedula' => fake()->unique()->uuid(),
                'phone' => fake()->e164PhoneNumber(),
                'address' => fake()->address(),
                'birthday' => $fechaAleatoriaj,
                'date_of_purchase' => now(),
                'membership' => fake()->randomElement([1, 2, 3, 4]),
            ]);
            $jose->profile()->save($profilej);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
}
