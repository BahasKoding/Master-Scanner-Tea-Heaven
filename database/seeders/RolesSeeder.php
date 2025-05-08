<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions first to ensure they exist
        $this->createBasicPermissions();

        // Create basic roles
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Operator', 'guard_name' => 'web']);
    }

    /**
     * Create basic permissions needed by the system
     */
    private function createBasicPermissions(): void
    {
        // Define all permission categories
        $permissionGroups = [
            // User management
            'Users' => ['List', 'Create', 'Update', 'Delete', 'View'],
            // Role management
            'Roles' => ['List', 'Create', 'Update', 'Delete', 'View'],
            // Permission management
            'Permissions' => ['List', 'Create', 'Update', 'Delete', 'View'],
            // Menu management
            'Menus' => ['List', 'Create', 'Update', 'Delete', 'View'],
            // Activity
            'Activity' => ['View', 'List'],
            // Suppliers
            'Suppliers' => ['List', 'Create', 'Update', 'Delete', 'View'],
            // Category Suppliers
            'Category Suppliers' => ['List', 'Create', 'Update', 'Delete', 'View'],
            // Category Products
            'Category Products' => ['List', 'Create', 'Update', 'Delete', 'View'],
            // Sales
            'Sales' => ['List', 'Create', 'Update', 'Delete', 'View', 'Report'],
        ];

        // Create permissions for each group
        foreach ($permissionGroups as $group => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "$group $action",
                    'guard_name' => 'web'
                ]);
            }
        }
    }
}
