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
        $client = Role::create(['name' => 'Client']);

        Permission::create(['name' => 'users.index'])->syncRoles([$admin]);
        Permission::create(['name' => 'users.create'])->syncRoles([$admin]);
        Permission::create(['name' => 'users.edit'])->syncRoles([$admin]);
        Permission::create(['name' => 'users.destroy'])->syncRoles([$admin]);

        Permission::create(['name' => 'clients.index'])->syncRoles([$admin, $employee]);
        Permission::create(['name' => 'clients.create'])->syncRoles([$admin, $employee]);
        Permission::create(['name' => 'clients.edit'])->syncRoles([$admin, $employee]);
        Permission::create(['name' => 'clients.destroy'])->syncRoles([$admin, $employee]);
    }
}
