<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use ReflectionClass;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all application permissions to the database from Policy files.';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem)
    {
        $this->info('Synchronizing permissions...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $policyFiles = $filesystem->glob(app_path('Policies/*Policy.php'));

        foreach ($policyFiles as $file) {
            $className = 'App\\Policies\\' . $filesystem->name($file);
            if (! class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);
            if ($reflection->hasProperty('permissions')) {
                $permissions = $reflection->getStaticPropertyValue('permissions');
                foreach ($permissions as $permission) {
                    Permission::updateOrCreate(['name' => $permission, 'guard_name' => 'web']);
                }
            }
        }

        // Ensure the admin role exists and has all permissions
        $adminRole = Role::updateOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $this->info('Permissions synchronized successfully.');

        return Command::SUCCESS;
    }
}
