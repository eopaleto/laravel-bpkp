<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $userRole  = Role::firstOrCreate(['name' => 'User']);

        // Assign role ke user ID 1 sebagai admin
        $admin = User::find(1);
        if ($admin) {
            $admin->assignRole($adminRole);
        }

        // Assign role ke user ID 2 sebagai user biasa
        $user = User::find(2);
        if ($user) {
            $user->assignRole($userRole);
        }
    }
}
