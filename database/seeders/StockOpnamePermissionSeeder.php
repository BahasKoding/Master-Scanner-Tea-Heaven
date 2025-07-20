<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class StockOpnamePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Stock Opname permissions
        $permissions = [
            'Stock Opname List',
            'Stock Opname Create',
            'Stock Opname Edit',
            'Stock Opname Delete',
            'Stock Opname Process',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to existing roles
        $adminRole = Role::where('name', 'admin')->first();
        $superAdminRole = Role::where('name', 'super-admin')->first();

        if ($adminRole) {
            foreach ($permissions as $permission) {
                $adminRole->givePermissionTo($permission);
            }
        }

        if ($superAdminRole) {
            // Super admin already gets all permissions via Gate::before rule
            // But we can explicitly assign them if needed
            foreach ($permissions as $permission) {
                $superAdminRole->givePermissionTo($permission);
            }
        }

        $this->command->info('Stock Opname permissions created and assigned successfully!');
    }
}
