<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'name' => fake()->name(),
            'ruc' => fake()->unique()->uuid(),
            'dv' => Str::random(2),
            'logo' => null,
            'description' => fake()->address(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->e164PhoneNumber(),
            'created_at' => now(),
            'updated_at' => now(),
        ]); 
    }
}
