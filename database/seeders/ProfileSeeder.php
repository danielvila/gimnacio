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
            $elrol = 'Client';
            if($i < 3){
                $elrol = 'Coach';
            }
            $username = '';
            do {
                $username = fake()->unique()->regexify('[a-zA-Z]{6}');
            } while (User::where('username', $username)->exists());
            $client = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'username' => $username,
                'email_verified_at' => now(),
                'password' =>  static::$password ??= Hash::make('enero2050'), 
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ])->assignRole($elrol);
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
