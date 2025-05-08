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
        // Call seeders in the right order - create permissions and roles first
        $this->call([
            RolesSeeder::class,        // Create basic roles and permissions first
            UserSeeder::class,         // Then create users with their roles
            CategorySupplierSeeder::class,
            SupplierSeeder::class,
            CategoryProductSeeder::class, // Tambahkan seeder untuk kategori produk
        ]);
    }
}
