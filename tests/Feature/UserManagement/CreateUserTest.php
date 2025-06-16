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

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $permissions = [
            Permission::create(['name' => 'view-any-user', 'guard_name' => 'web']),
            Permission::create(['name' => 'create-user', 'guard_name' => 'web']),
        ];
        $role->syncPermissions($permissions);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($role);

        $this->actingAs($this->adminUser);
    }

    public function test_it_can_create_a_user(): void
    {
        $newUser = User::factory()->make();
        $adminRole = Role::where('name', 'Admin')->first();

        Livewire::test(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => $newUser->name,
                'email' => $newUser->email,
                'password' => 'password',
                'roles' => [$adminRole->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $createdUser = User::where('email', $newUser->email)->first();
        $this->assertDatabaseHas('users', [
            'name' => $newUser->name,
            'email' => $newUser->email,
        ]);
        $this->assertTrue($createdUser->hasRole('Admin'));
    }

    public function test_it_validates_user_input(): void
    {
        Livewire::test(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => null,
                'email' => 'not-an-email',
                'password' => 'short',
            ])
            ->call('create')
            ->assertHasFormErrors([
                'name' => 'required',
                'email' => 'email',
                'password' => 'min',
            ]);
    }

    public function test_it_validates_password_minimum_length(): void
    {
        Livewire::test(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => '1234',
            ])
            ->call('create')
            ->assertHasFormErrors(['password' => 'min']);
    }

    public function test_it_validates_unique_email(): void
    {
        $existingUser = User::factory()->create();

        Livewire::test(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'New User',
                'email' => $existingUser->email,
                'password' => 'password',
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'unique']);
    }
}
