<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'user_view', 'user_create', 'user_update', 'user_delete',
            'role_view', 'role_create', 'role_update', 'role_delete',
            'permission_view', 'permission_create', 'permission_update', 'permission_delete',
            'student_view', 'student_create', 'student_update', 'student_delete',
            'department_view', 'department_create', 'department_update', 'department_delete',
            'division_view', 'division_create', 'division_update', 'division_delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $spocRole = Role::firstOrCreate(['name' => 'spoc']);
        $coordinatorRole = Role::firstOrCreate(['name' => 'co-ordinator']);
        Role::firstOrCreate(['name' => 'student']);

        // Assign permissions to roles
        $spocRole->givePermissionTo([
            'student_view', 'student_create', 'student_update', 'student_delete',
            'department_view', 'division_view',
        ]);

        $coordinatorRole->givePermissionTo(['student_view']);

        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email' => 'admin@gmail.com',
            ]
        );

        $user->assignRole($adminRole);
    }
}
