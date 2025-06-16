<?php

namespace Tests\Feature\UserManagement;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BulkDeleteUserTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Collection $usersToDelete;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $permissions = [
            Permission::create(['name' => 'view-any-user', 'guard_name' => 'web']),
            Permission::create(['name' => 'delete-user', 'guard_name' => 'web']),
        ];
        $role->syncPermissions($permissions);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($role);
        $this->actingAs($this->adminUser);

        $this->usersToDelete = User::factory()->count(3)->create();
    }

    public function test_it_can_bulk_delete_users(): void
    {
        Livewire::test(UserResource\Pages\ListUsers::class)
            ->callTableBulkAction(DeleteAction::class, $this->usersToDelete->pluck('id')->all())
            ->assertHasNoTableActionErrors();

        $this->usersToDelete->each(function (User $user) {
            $this->assertModelMissing($user);
        });
    }

    public function test_it_does_not_bulk_delete_logged_in_user(): void
    {
        $allUsers = $this->usersToDelete->push($this->adminUser);

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->callTableBulkAction(DeleteAction::class, $allUsers->pluck('id')->all())
            ->assertHasNoTableActionErrors();

        $this->assertModelExists($this->adminUser);

        $this->usersToDelete->each(function (User $user) {
            if ($user->id !== $this->adminUser->id) {
                $this->assertModelMissing($user);
            }
        });
    }
}
