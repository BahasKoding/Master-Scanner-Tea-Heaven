<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil AdminUserSeeder
        $this->call([
            RolesSeeder::class,       // Add the new RoleSeeder here
            AdminUserSeeder::class,  // Your existing seeders
        ]);

        // Dapatkan user admin yang baru dibuat
        $adminUser = User::where('email', 'admin@gmail.com')->first();

        if (!$adminUser) {
            throw new \Exception('Admin user not found. Make sure AdminUserSeeder is creating the user correctly.');
        }
    
    }
}