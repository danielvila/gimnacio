<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            MembershipSeeder::class,
            ProfileSeeder::class,
            PaymentTypeSeeder::class,
            PaymentSeeder::class,
            ConcurrenceSeeder::class,
            RoutineSeeder::class,
            ScheduleSeeder::class,
            SettingSeeder::class
        ]);        
    }
}
