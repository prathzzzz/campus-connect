<?php

namespace Tests\Feature\UserManagement;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $testUser;

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

        $this->testUser = User::factory()->create();
    }

    public function test_it_can_delete_a_user(): void
    {
        Livewire::test(UserResource\Pages\ListUsers::class)
            ->callTableAction(DeleteAction::class, $this->testUser);

        $this->assertModelMissing($this->testUser);
    }

    public function test_it_disables_delete_for_self(): void
    {
        Livewire::test(UserResource\Pages\ListUsers::class)
            ->assertTableActionDisabled(DeleteAction::class, $this->adminUser);
    }
}
