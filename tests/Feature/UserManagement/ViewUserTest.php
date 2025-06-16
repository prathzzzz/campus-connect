<?php

namespace Tests\Feature\UserManagement;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions\ViewAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ViewUserTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $testUser;

    protected Role $testRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $permissions = [
            Permission::create(['name' => 'view-any-user', 'guard_name' => 'web']),
            Permission::create(['name' => 'view-user', 'guard_name' => 'web']),
        ];
        $role->syncPermissions($permissions);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($role);
        $this->actingAs($this->adminUser);

        $this->testUser = User::factory()->create();
        $this->testRole = Role::create(['name' => 'Test Role']);
        $this->testUser->assignRole($this->testRole);
    }

    public function test_it_can_view_a_user(): void
    {
        Livewire::test(UserResource\Pages\ListUsers::class)
            ->mountTableAction(ViewAction::class, $this->testUser)
            ->assertSee($this->testUser->name)
            ->assertSee($this->testUser->email);
    }

    public function test_it_can_display_user_roles_in_view(): void
    {
        Livewire::test(UserResource\Pages\ListUsers::class)
            ->mountTableAction(ViewAction::class, $this->testUser)
            ->assertSee($this->testRole->name);
    }
}
