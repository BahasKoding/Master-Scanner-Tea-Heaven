<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Backend\Activity;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat permission dasar
        $this->createBasicPermissions();

        // 2. Buat roles
        $this->createBasicRoles();

        // 3. Buat users
        $this->createDefaultUsers();

        // 4. Panggil seeder lainnya
        $this->call([
            CategorySupplierSeeder::class,
            SupplierSeeder::class,
            LabelSeeder::class,
            CategoryProductSeeder::class,
            ProductListSeeder::class,
        ]);
    }

    /**
     * Buat permission dasar
     */
    private function createBasicPermissions()
    {
        $permissions = [
            // User, Role, Permission, Menu
            'Users List',
            'Users Create',
            'Users Update',
            'Users Delete',
            'Users View',
            'Roles List',
            'Roles Create',
            'Roles Update',
            'Roles Delete',
            'Roles View',
            'Permissions List',
            'Permissions Create',
            'Permissions Update',
            'Permissions Delete',
            'Permissions View',
            'Menus List',
            'Menus Create',
            'Menus Update',
            'Menus Delete',
            'Menus View',

            // Activity
            'Activity View',
            'Activity List',

            // Suppliers
            'Suppliers List',
            'Suppliers Create',
            'Suppliers Update',
            'Suppliers Delete',
            'Suppliers View',

            // Category Suppliers
            'Category Suppliers List',
            'Category Suppliers Create',
            'Category Suppliers Update',
            'Category Suppliers Delete',
            'Category Suppliers View',

            // Category Products
            'Category Products List',
            'Category Products Create',
            'Category Products Update',
            'Category Products Delete',
            'Category Products View',

            // Sales
            'Sales List',
            'Sales Create',
            'Sales Update',
            'Sales Delete',
            'Sales View',
            'Sales Report',

              // Label permissions
              'Labels List',
              'Labels Create',
              'Labels Update',
              'Labels Delete',
              'Labels View',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }

    /**
     * Buat roles dasar
     */
    private function createBasicRoles()
    {
        // Buat roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $operator = Role::firstOrCreate(['name' => 'Operator', 'guard_name' => 'web']);

        // Berikan semua permission ke Super Admin
        $allPermissions = Permission::all();
        $superAdmin->syncPermissions($allPermissions);

        // Berikan sebagian permission ke Admin (kecuali role management)
        $adminPermissions = Permission::whereNotIn('name', [
            'Roles Create',
            'Roles Update',
            'Roles Delete',
            'Permissions Create',
            'Permissions Update',
            'Permissions Delete'
        ])->get();
        $admin->syncPermissions($adminPermissions);

        // Berikan permission terbatas ke Operator
        $operatorPermissions = Permission::whereIn('name', [
            'Users View',
            'Activity View',
            'Activity List',
            'Sales List',
            'Sales Create',
            'Sales Update',
            'Sales View',
            'Suppliers List',
            'Suppliers View',
            'Category Suppliers List',
            'Category Suppliers View',
            'Category Products List',
            'Category Products View'
        ])->get();
        $operator->syncPermissions($operatorPermissions);
    }

    /**
     * Buat user default
     */
    private function createDefaultUsers()
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'developer@gmail.com'],
            [
                'name' => 'System Developer',
                'password' => bcrypt('developer123'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'owner@gmail.com'],
            [
                'name' => 'Tea Heaven Owner',
                'password' => bcrypt('owner123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('Admin');

        // Operator - Cashier
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@gmail.com'],
            [
                'name' => 'Cashier Staff',
                'password' => bcrypt('cashier123'),
                'email_verified_at' => now(),
            ]
        );
        $cashier->assignRole('Operator');

        // Operator - Inventory
        $inventory = User::firstOrCreate(
            ['email' => 'inventory@gmail.com'],
            [
                'name' => 'Inventory Staff',
                'password' => bcrypt('inventory123'),
                'email_verified_at' => now(),
            ]
        );
        $inventory->assignRole('Operator');

        // Log aktivitas user
        try {
            Activity::create(['category' => 'user', 'action' => 'create', 'action_id' => $superAdmin->id, 'note' => 'Super Admin account created', 'user_id' => null]);
            Activity::create(['category' => 'user', 'action' => 'create', 'action_id' => $admin->id, 'note' => 'Admin account created', 'user_id' => null]);
            Activity::create(['category' => 'user', 'action' => 'create', 'action_id' => $cashier->id, 'note' => 'Cashier account created', 'user_id' => null]);
            Activity::create(['category' => 'user', 'action' => 'create', 'action_id' => $inventory->id, 'note' => 'Inventory account created', 'user_id' => null]);
        } catch (\Exception $e) {
            // Ignore activity logging errors
        }
    }
}
