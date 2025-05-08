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
        // 1. Create all permissions first
        $this->createPermissions();

        // 2. Create and configure roles
        $this->createRolesWithPermissions();

        // 3. Create users with roles
        $this->createUsers();

        // 4. Seed categories and other data
        $this->call([
            CategorySupplierSeeder::class,
            SupplierSeeder::class,
            CategoryProductSeeder::class,
        ]);
    }

    /**
     * Create all system permissions
     */
    private function createPermissions(): void
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

    /**
     * Create roles and assign permissions
     */
    private function createRolesWithPermissions(): void
    {
        // Create basic roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $operatorRole = Role::firstOrCreate(['name' => 'Operator', 'guard_name' => 'web']);

        // Super Admin gets all permissions
        $allPermissions = Permission::all()->pluck('name')->toArray();
        $superAdminRole->syncPermissions($allPermissions);

        // Admin gets most permissions, except role and permission management
        $restrictedPermissions = [
            'Roles Create',
            'Roles Update',
            'Roles Delete',
            'Permissions Create',
            'Permissions Update',
            'Permissions Delete'
        ];

        $adminPermissions = Permission::whereNotIn('name', $restrictedPermissions)
            ->pluck('name')->toArray();
        $adminRole->syncPermissions($adminPermissions);

        // Operator gets limited permissions
        $operatorPermissions = [
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
            'Category Products View',
        ];

        // Filter only permissions that actually exist
        $existingOperatorPermissions = Permission::whereIn('name', $operatorPermissions)->pluck('name')->toArray();
        $operatorRole->syncPermissions($existingOperatorPermissions);
    }

    /**
     * Create all system users
     */
    private function createUsers(): void
    {
        // Get roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $operatorRole = Role::where('name', 'Operator')->first();

        // Create Super Admin user (Developer)
        $superAdmin = User::firstOrCreate(
            ['email' => 'developer@gmail.com'],
            [
                'name' => 'System Developer',
                'password' => bcrypt('developer123'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Create Admin user (Owner)
        $admin = User::firstOrCreate(
            ['email' => 'owner@gmail.com'],
            [
                'name' => 'Tea Heaven Owner',
                'password' => bcrypt('owner123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // Create Operator users (Employees)
        $employee1 = User::firstOrCreate(
            ['email' => 'cashier@gmail.com'],
            [
                'name' => 'Cashier Staff',
                'password' => bcrypt('cashier123'),
                'email_verified_at' => now(),
            ]
        );
        $employee1->assignRole($operatorRole);

        $employee2 = User::firstOrCreate(
            ['email' => 'inventory@gmail.com'],
            [
                'name' => 'Inventory Staff',
                'password' => bcrypt('inventory123'),
                'email_verified_at' => now(),
            ]
        );
        $employee2->assignRole($operatorRole);

        // Log user creation
        $this->logActivity($superAdmin, 'Super Admin account created/updated by system');
        $this->logActivity($admin, 'Admin account created/updated by system');
        $this->logActivity($employee1, 'Operator account created/updated by system');
        $this->logActivity($employee2, 'Operator account created/updated by system');
    }

    /**
     * Log user creation activities
     */
    private function logActivity($user, $note): void
    {
        try {
            Activity::create([
                'category' => 'user',
                'action' => 'create',
                'action_id' => $user->id,
                'note' => $note,
                'user_id' => null // System generated
            ]);
        } catch (\Exception $e) {
            // Don't fail the seeder if logging fails
            \Illuminate\Support\Facades\Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }
}
