<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Bookings
            'view bookings', 'create bookings', 'edit bookings',
            'cancel bookings', 'approve bookings', 'force delete bookings',
            // Venues
            'view venues', 'create venues', 'edit venues', 'delete venues',
            // Customers
            'view customers', 'create customers', 'edit customers', 'delete customers',
            // Reports
            'view reports', 'export reports',
            // Payments
            'view payments', 'create payments', 'delete payments',
            // Other
            'manage settings', 'manage slots', 'manage waitlist', 'manage credits',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Staff
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staff->syncPermissions([
            'view bookings', 'create bookings',
            'view customers', 'create customers',
            'view payments', 'create payments',
            'manage slots',
        ]);

        // Admin
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'view bookings', 'create bookings', 'edit bookings',
            'cancel bookings', 'approve bookings',
            'view venues', 'create venues', 'edit venues',
            'view customers', 'create customers', 'edit customers', 'delete customers',
            'view reports', 'export reports',
            'view payments', 'create payments', 'delete payments',
            'manage slots', 'manage waitlist', 'manage credits',
        ]);

        // Super Admin
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());
    }
}
