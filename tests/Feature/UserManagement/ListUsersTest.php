<?php

namespace Tests\Feature\UserManagement;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ListUsersTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission = Permission::create(['name' => 'view-any-user', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $this->user = User::factory()->create();
        $this->user->assignRole($role);

        $this->actingAs($this->user);
    }

    public function test_it_can_list_users(): void
    {
        $users = User::factory()->count(5)->create();

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->assertCanSeeTableRecords($users->merge([$this->user]));
    }

    public function test_it_can_search_for_users(): void
    {
        $users = User::factory()->count(5)->create();
        $searchUser = $users->first();

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->searchTable($searchUser->name)
            ->assertCanSeeTableRecords([$searchUser])
            ->assertCanNotSeeTableRecords($users->where('id', '!=', $searchUser->id));

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->searchTable($searchUser->email)
            ->assertCanSeeTableRecords([$searchUser])
            ->assertCanNotSeeTableRecords($users->where('id', '!=', $searchUser->id));
    }

    public function test_it_can_search_by_role(): void
    {
        $role = Role::create(['name' => 'Super-Admin']);
        $userWithRole = User::factory()->create();
        $userWithRole->assignRole($role);

        $otherUsers = User::factory()->count(3)->create();

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->searchTable('Super-Admin')
            ->assertCanSeeTableRecords([$userWithRole])
            ->assertCanNotSeeTableRecords($otherUsers);
    }

    public function test_it_can_sort_users_by_creation_date(): void
    {
        $users = collect([
            $this->user,
            User::factory()->create(['created_at' => now()->subDays(2)]),
            User::factory()->create(['created_at' => now()->subDays(1)]),
            User::factory()->create(['created_at' => now()]),
        ]);

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->sortTable('created_at', 'asc')
            ->assertCanSeeTableRecords($users->sortBy('created_at')->values()->all(), inOrder: true);

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->sortTable('created_at', 'desc')
            ->assertCanSeeTableRecords($users->sortByDesc('created_at')->values()->all(), inOrder: true);
    }

    public function test_it_can_sort_users_by_email_verified_at_date(): void
    {
        $users = collect([
            User::factory()->create(['email_verified_at' => now()->subDays(2)]),
            User::factory()->create(['email_verified_at' => now()->subDays(1)]),
            User::factory()->create(['email_verified_at' => null]),
        ]);

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->sortTable('email_verified_at', 'asc')
            ->assertCanSeeTableRecords($users->sortBy('email_verified_at')->values()->all(), inOrder: true);

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->sortTable('email_verified_at', 'desc')
            ->assertCanSeeTableRecords($users->sortByDesc('email_verified_at')->values()->all(), inOrder: true);
    }

    public function test_it_can_sort_users_by_updated_at_date(): void
    {
        $users = collect([
            User::factory()->create(['updated_at' => now()->subDays(2)]),
            User::factory()->create(['updated_at' => now()->subDays(1)]),
            User::factory()->create(['updated_at' => now()]),
        ]);

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->sortTable('updated_at', 'asc')
            ->assertCanSeeTableRecords($users->sortBy('updated_at')->values()->all(), inOrder: true);

        Livewire::test(UserResource\Pages\ListUsers::class)
            ->sortTable('updated_at', 'desc')
            ->assertCanSeeTableRecords($users->sortByDesc('updated_at')->values()->all(), inOrder: true);
    }
}
