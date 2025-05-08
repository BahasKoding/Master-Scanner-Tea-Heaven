<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in the right order
        $this->call([
            RolesSeeder::class,        // Create basic roles first
            UserSeeder::class,         // Create users with their roles
            CategorySupplierSeeder::class,
            SupplierSeeder::class,
        ]);
    }
}
