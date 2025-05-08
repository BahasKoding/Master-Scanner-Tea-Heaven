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
        try {
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

            \Illuminate\Support\Facades\Log::info('Database seeding completed successfully');
        } catch (\Exception $e) {
            // Log error with full details
            \Illuminate\Support\Facades\Log::error('Database seeding failed: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Error details: ' . $e->getTraceAsString());

            // Re-throw for CLI visibility
            throw $e;
        }
    }

    /**
     * Create all system permissions
     */
    private function createPermissions(): void
    {
        try {
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

            // Counter for created permissions
            $created = 0;

            // Create permissions for each group
            foreach ($permissionGroups as $group => $actions) {
                foreach ($actions as $action) {
                    $permission = Permission::firstOrCreate([
                        'name' => "$group $action",
                        'guard_name' => 'web'
                    ]);
                    if ($permission->wasRecentlyCreated) {
                        $created++;
                    }
                }
            }

            \Illuminate\Support\Facades\Log::info("Created $created new permissions");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create permissions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create roles and assign permissions
     */
    private function createRolesWithPermissions(): void
    {
        try {
            // Create basic roles
            $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
            $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $operatorRole = Role::firstOrCreate(['name' => 'Operator', 'guard_name' => 'web']);

            \Illuminate\Support\Facades\Log::info('Roles created: Super Admin, Admin, Operator');

            // Super Admin gets all permissions
            $allPermissions = Permission::all()->pluck('name')->toArray();
            \Illuminate\Support\Facades\Log::info('Total permissions found: ' . count($allPermissions));
            \Illuminate\Support\Facades\Log::info('Permissions found: ' . implode(', ', $allPermissions));

            $superAdminRole->syncPermissions($allPermissions);
            \Illuminate\Support\Facades\Log::info('Super Admin role permissions assigned: ' . count($allPermissions));

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
            \Illuminate\Support\Facades\Log::info('Admin role permissions assigned: ' . count($adminPermissions));

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
            \Illuminate\Support\Facades\Log::info('Operator permissions to assign: ' . implode(', ', $existingOperatorPermissions));

            $operatorRole->syncPermissions($existingOperatorPermissions);
            \Illuminate\Support\Facades\Log::info('Operator role permissions assigned: ' . count($existingOperatorPermissions));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create roles with permissions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create all system users
     */
    private function createUsers(): void
    {
        try {
            // Get roles
            $superAdminRole = Role::where('name', 'Super Admin')->first();
            $adminRole = Role::where('name', 'Admin')->first();
            $operatorRole = Role::where('name', 'Operator')->first();

            if (!$superAdminRole || !$adminRole || !$operatorRole) {
                \Illuminate\Support\Facades\Log::error('One or more roles not found');
                throw new \Exception('Roles not found, cannot create users');
            }

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
            \Illuminate\Support\Facades\Log::info('Super Admin user created and role assigned');

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
            \Illuminate\Support\Facades\Log::info('Admin user created and role assigned');

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
            \Illuminate\Support\Facades\Log::info('Cashier user created and role assigned');

            $employee2 = User::firstOrCreate(
                ['email' => 'inventory@gmail.com'],
                [
                    'name' => 'Inventory Staff',
                    'password' => bcrypt('inventory123'),
                    'email_verified_at' => now(),
                ]
            );
            $employee2->assignRole($operatorRole);
            \Illuminate\Support\Facades\Log::info('Inventory user created and role assigned');

            // Log user creation
            $this->logActivity($superAdmin, 'Super Admin account created/updated by system');
            $this->logActivity($admin, 'Admin account created/updated by system');
            $this->logActivity($employee1, 'Operator account created/updated by system');
            $this->logActivity($employee2, 'Operator account created/updated by system');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create users: ' . $e->getMessage());
            throw $e;
        }
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
