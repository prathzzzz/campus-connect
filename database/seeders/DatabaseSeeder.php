<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders that create roles, permissions, and other non-user data first.
        $this->call([
            PermissionSeeder::class, // This now just syncs permissions and creates the admin role
            DepartmentSeeder::class,
            DivisionSeeder::class,
        ]);

        // Create a default admin user
        $adminRole = Role::where('name', 'admin')->first();
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        if ($adminRole) {
            $adminUser->assignRole($adminRole);
        }
    }
}
