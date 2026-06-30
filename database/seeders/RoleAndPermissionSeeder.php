<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $cashierRole = Role::firstOrCreate(['name' => 'Kasir']);

        // Create Users & Assign Roles

        // 1. Super Admin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // 2. Manager User
        $manager = User::updateOrCreate(
            ['email' => 'manager@demo.com'],
            [
                'name' => 'Manager Toko',
                'password' => Hash::make('password'),
            ]
        );
        $manager->assignRole($managerRole);

        // 3. Cashier User
        $cashier = User::updateOrCreate(
            ['email' => 'kasir@demo.com'],
            [
                'name' => 'Budi Kasir',
                'password' => Hash::make('password'),
            ]
        );
        $cashier->assignRole($cashierRole);
    }
}
