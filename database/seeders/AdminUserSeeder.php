<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('12345678'),
                'email_verified_at' => now(),
            ]
        );

        // Ensure the Super Admin role exists
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // Assign the Super Admin role to the admin user
        $adminUser->assignRole($superAdminRole);
    }
}