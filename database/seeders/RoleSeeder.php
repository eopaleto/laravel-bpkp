<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Buat roles
        $superadminRole = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $userRole = Role::firstOrCreate(['name' => 'User']);

        // Buat atau update SuperAdmin
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@bpkp.go.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('superadmin123'),
            ]
        );
        $superadmin->syncRoles([$superadminRole]);

        // Buat atau update Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@bpkp.go.id'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
            ]
        );
        $admin->syncRoles([$adminRole]);

        // Buat atau update User
        $user = User::firstOrCreate(
            ['email' => 'user@bpkp.go.id'],
            [
                'name' => 'User',
                'password' => Hash::make('user123'),
            ]
        );
        $user->syncRoles([$userRole]);
    }
}