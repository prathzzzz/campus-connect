<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $models = [
            'user',
            'role',
            'student',
            'department',
            'division',
        ];

        $actions = ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete'];

        $permissions = [];
        foreach ($models as $model) {
            foreach ($actions as $action) {
                $permissions[] = Permission::updateOrCreate(['name' => $action . '-' . $model, 'guard_name' => 'web']);
            }
        }

        // Create admin role and assign all permissions
        $adminRole = Role::updateOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // Create student role
        Role::updateOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // Clear the permission cache
        Artisan::call('permission:cache-reset');
    }
}
