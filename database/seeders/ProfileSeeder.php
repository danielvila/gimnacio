<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Profile;
use Hash;


class ProfileSeeder extends Seeder
{
    protected static ?string $password;

    public function run(): void
    {
        for ($i=0; $i < 25; $i++) { 
        
            $client = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' =>  static::$password ??= Hash::make('enero2050'), 
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ])->assignRole('Client');
            $fechaActual = now();
            $fechaAleatoria = $fechaActual->subYears(rand(20, 30));
            Profile::create([
                'cedula' => fake()->unique()->uuid(),
                'phone' => fake()->e164PhoneNumber(),
                'address' => fake()->address(),
                'birthday' => $fechaAleatoria,            
                'user_id' =>  $client->id,
            ]);
        }
    }
}
