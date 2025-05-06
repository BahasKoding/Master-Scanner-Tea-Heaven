<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $operatorRole = Role::firstOrCreate(['name' => 'Operator', 'guard_name' => 'web']);

        // Define permissions
        $permissions = [
            'Roles Index',
            'Roles Create',
            'Users Create',
            'Users Update',
            'Users Delete',
            'Roles Update',
            'Roles Delete',
        ];

        // Create permissions
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);
        }

        // Assign all permissions to Super Admin
        $superAdminRole->givePermissionTo($permissions);

        // You can assign specific permissions to Admin and Operator roles here if needed
        // For example:
        // $adminRole->givePermissionTo(['Users Create', 'Users Update']);
        // $operatorRole->givePermissionTo(['Products Create', 'Products Update']);
    }
}