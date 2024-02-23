<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'Admin']);
        $employee = Role::create(['name' => 'Employee']);
        $coach = Role::create(['name' => 'Coach']);
        $client = Role::create(['name' => 'Client']);

        Permission::create(['name' => 'users.index'])->syncRoles([$admin]);
        Permission::create(['name' => 'users.create'])->syncRoles([$admin]);
        Permission::create(['name' => 'users.edit'])->syncRoles([$admin]);
        Permission::create(['name' => 'users.destroy'])->syncRoles([$admin]);

        Permission::create(['name' => 'memberships.index'])->syncRoles([$admin]);
        Permission::create(['name' => 'memberships.create'])->syncRoles([$admin]);
        Permission::create(['name' => 'memberships.edit'])->syncRoles([$admin]);
        Permission::create(['name' => 'memberships.destroy'])->syncRoles([$admin]);

        Permission::create(['name' => 'clients.index'])->syncRoles([$admin, $employee]);
        Permission::create(['name' => 'clients.create'])->syncRoles([$admin, $employee]);
        Permission::create(['name' => 'clients.edit'])->syncRoles([$admin, $employee]);
        Permission::create(['name' => 'clients.destroy'])->syncRoles([$admin]);

        Permission::create(['name' => 'concurrences.index'])->syncRoles([$admin, $employee, $coach]);
        Permission::create(['name' => 'concurrences.create'])->syncRoles([$admin, $employee, $coach]);
        Permission::create(['name' => 'concurrences.edit'])->syncRoles([$admin, $employee, $coach]);
        Permission::create(['name' => 'concurrences.destroy'])->syncRoles([$admin]);
        
        Permission::create(['name' => 'payments.index'])->syncRoles([$admin, $employee]);
        Permission::create(['name' => 'payments.create'])->syncRoles([$admin, $employee]);
        Permission::create(['name' => 'payments.edit'])->syncRoles([$admin, $employee]);
        Permission::create(['name' => 'payments.destroy'])->syncRoles([$admin, $employee]);
    }
}
