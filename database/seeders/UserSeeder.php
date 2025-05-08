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
        // Get existing roles
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $operatorRole = Role::where('name', 'Operator')->first();

        if (!$superAdminRole || !$adminRole || !$operatorRole) {
            throw new \Exception('Roles not found. Make sure RolesSeeder has run first.');
        }

        // Assign permissions to roles - Roles and permissions already created by RolesSeeder
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
     * Assign appropriate permissions to each role
     */
    private function assignPermissionsToRoles($superAdminRole, $adminRole, $operatorRole): void
    {
        // Buat terlebih dahulu daftar permission dari DB - lebih aman daripada hardcode
        $existingPermissions = Permission::pluck('name')->toArray();

        // Debug - tampilkan semua permission yang ada
        \Illuminate\Support\Facades\Log::info('Available permissions: ' . implode(', ', $existingPermissions));

        // Super Admin gets all permissions
        $superAdminRole->syncPermissions($existingPermissions);

        // Admin gets most permissions, but not role/permission management
        $adminPermissions = Permission::where(function ($query) {
            $query->where('name', 'not like', '%Roles Create%')
                ->where('name', 'not like', '%Roles Update%')
                ->where('name', 'not like', '%Roles Delete%')
                ->where('name', 'not like', '%Permissions Create%')
                ->where('name', 'not like', '%Permissions Update%')
                ->where('name', 'not like', '%Permissions Delete%');
        })->pluck('name')->toArray();
        $adminRole->syncPermissions($adminPermissions);

        // Get selected permissions for operator by querying DB with 'like'
        $operatorViewPermissions = Permission::where(function ($query) {
            // Only necessary View permissions
            $query->where('name', 'like', '%Users View%')
                ->orWhere('name', 'like', '%Activity View%')
                ->orWhere('name', 'like', '%Activity List%')
                ->orWhere('name', 'like', '%Sales List%')
                ->orWhere('name', 'like', '%Sales Create%')
                ->orWhere('name', 'like', '%Sales Update%')
                ->orWhere('name', 'like', '%Sales View%')
                ->orWhere('name', 'like', '%Suppliers List%')
                ->orWhere('name', 'like', '%Suppliers View%')
                ->orWhere('name', 'like', '%Category Suppliers List%')
                ->orWhere('name', 'like', '%Category Suppliers View%')
                ->orWhere('name', 'like', '%Category Products List%')
                ->orWhere('name', 'like', '%Category Products View%');
        })->pluck('name')->toArray();

        // Debug - log operator permissions
        \Illuminate\Support\Facades\Log::info('Operator permissions: ' . implode(', ', $operatorViewPermissions));

        $operatorRole->syncPermissions($operatorViewPermissions);
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
