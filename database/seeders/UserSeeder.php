<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Backend\Activity;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First ensure we have all required permissions
        $this->createPermissions();

        // Create roles if they don't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $operatorRole = Role::firstOrCreate(['name' => 'Operator']);

        // Assign permissions to roles
        $this->assignPermissionsToRoles($superAdminRole, $adminRole, $operatorRole);

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

        // Create some Operator users (Employees)
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

        // Log the creation of these users
        $this->logActivity($superAdmin, 'Super Admin account created/updated by system');
        $this->logActivity($admin, 'Admin account created/updated by system');
        $this->logActivity($employee1, 'Operator account created/updated by system');
        $this->logActivity($employee2, 'Operator account created/updated by system');
    }

    /**
     * Create all needed permissions for the system
     */
    private function createPermissions(): void
    {
        // User management permissions
        $userPermissions = [
            'Users List',
            'Users Create',
            'Users Update',
            'Users Delete',
            'Users View',
        ];

        // Role management permissions
        $rolePermissions = [
            'Roles List',
            'Roles Create',
            'Roles Update',
            'Roles Delete',
            'Roles View',
        ];

        // Permission management permissions
        $permissionPermissions = [
            'Permissions List',
            'Permissions Create',
            'Permissions Update',
            'Permissions Delete',
            'Permissions View',
        ];

        // Menu management permissions
        $menuPermissions = [
            'Menus List',
            'Menus Create',
            'Menus Update',
            'Menus Delete',
            'Menus View',
        ];

        // Activity Log permissions
        $activityPermissions = [
            'Activity View',
            'Activity List',
        ];

        // Supplier management permissions
        $supplierPermissions = [
            'Suppliers List',
            'Suppliers Create',
            'Suppliers Update',
            'Suppliers Delete',
            'Suppliers View',
        ];

        // Category Supplier permissions
        $categorySupplierPermissions = [
            'Category Suppliers List',
            'Category Suppliers Create',
            'Category Suppliers Update',
            'Category Suppliers Delete',
            'Category Suppliers View',
        ];

        // Category Product permissions
        $categoryProductPermissions = [
            'Category Products List',
            'Category Products Create',
            'Category Products Update',
            'Category Products Delete',
            'Category Products View',
        ];

        // Sales permissions
        $salesPermissions = [
            'Sales List',
            'Sales Create',
            'Sales Update',
            'Sales Delete',
            'Sales View',
            'Sales Report',
        ];

        // Combine all permissions
        $allPermissions = array_merge(
            $userPermissions,
            $rolePermissions,
            $permissionPermissions,
            $menuPermissions,
            $activityPermissions,
            $supplierPermissions,
            $categorySupplierPermissions,
            $categoryProductPermissions,
            $salesPermissions
        );

        // Create each permission if it doesn't exist
        foreach ($allPermissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);
        }
    }

    /**
     * Assign appropriate permissions to each role
     */
    private function assignPermissionsToRoles($superAdminRole, $adminRole, $operatorRole): void
    {
        // Super Admin gets all permissions
        $allPermissions = Permission::all()->pluck('name')->toArray();
        $superAdminRole->syncPermissions($allPermissions);

        // Admin gets most permissions, but not all role and permission management
        $adminPermissions = Permission::whereNotIn('name', [
            'Roles Create',
            'Roles Update',
            'Roles Delete',
            'Permissions Create',
            'Permissions Update',
            'Permissions Delete',
        ])->pluck('name')->toArray();
        $adminRole->syncPermissions($adminPermissions);

        // Operator gets limited permissions
        $operatorPermissions = [
            // User management (only view own profile)
            'Users View',

            // Activity
            'Activity View',
            'Activity List',

            // Sales operations
            'Sales List',
            'Sales Create',
            'Sales Update',
            'Sales View',

            // Supplier operations (only view)
            'Suppliers List',
            'Suppliers View',

            // Category Supplier operations (only view)
            'Category Suppliers List',
            'Category Suppliers View',

            // Category Product operations (only view)
            'Category Products List',
            'Category Products View',
        ];
        $operatorRole->syncPermissions($operatorPermissions);
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
