<?php

namespace Tests\Feature\UserManagement;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class EditUserTest extends TestCase
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
            Permission::create(['name' => 'edit-user', 'guard_name' => 'web']),
            Permission::create(['name' => 'update-user', 'guard_name' => 'web']),
        ];
        $role->syncPermissions($permissions);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($role);
        $this->actingAs($this->adminUser);

        $this->testUser = User::factory()->unverified()->create();
    }

    public function test_it_can_retrieve_user_data(): void
    {
        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $this->testUser->getRouteKey()])
            ->assertFormSet([
                'name' => $this->testUser->name,
                'email' => $this->testUser->email,
            ]);
    }

    public function test_it_can_update_a_user(): void
    {
        $newData = User::factory()->make();
        $adminRole = Role::where('name', 'Admin')->first();

        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $this->testUser->getRouteKey()])
            ->fillForm([
                'name' => $newData->name,
                'email' => $newData->email,
                'roles' => [$adminRole->id],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $this->testUser->id,
            'name' => $newData->name,
            'email' => $newData->email,
        ]);
        $this->assertTrue($this->testUser->refresh()->hasRole('Admin'));
    }

    public function test_it_validates_user_input(): void
    {
        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $this->testUser->getRouteKey()])
            ->fillForm([
                'name' => null,
                'email' => 'not-an-email',
            ])
            ->call('save')
            ->assertHasFormErrors([
                'name' => 'required',
                'email' => 'email',
            ]);
    }

    public function test_it_validates_password_minimum_length_when_updating(): void
    {
        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $this->testUser->getRouteKey()])
            ->fillForm([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => '1234',
            ])
            ->call('save')
            ->assertHasFormErrors(['password' => 'min']);
    }

    public function test_it_validates_unique_email_when_updating(): void
    {
        $existingUser = User::factory()->create();

        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $this->testUser->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Name',
                'email' => $existingUser->email,
            ])
            ->call('save')
            ->assertHasFormErrors(['email' => 'unique']);
    }

    public function test_it_does_not_update_password_if_field_is_blank(): void
    {
        $oldPassword = $this->testUser->password;

        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $this->testUser->getRouteKey()])
            ->fillForm([
                'name' => 'New Name',
                'password' => '',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals($oldPassword, $this->testUser->refresh()->password);
    }

    public function test_it_updates_password_if_field_is_filled(): void
    {
        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $this->testUser->getRouteKey()])
            ->fillForm([
                'name' => 'New Name',
                'password' => 'new-password',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('new-password', $this->testUser->refresh()->password));
    }

    public function test_admin_can_verify_user_email(): void
    {
        $this->assertNull($this->testUser->email_verified_at);

        Livewire::test(UserResource\Pages\EditUser::class, ['record' => $this->testUser->getRouteKey()])
            ->fillForm([
                'name' => $this->testUser->name,
                'email' => $this->testUser->email,
                'email_verified_at' => now()->toDateTimeString(),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNotNull($this->testUser->refresh()->email_verified_at);
    }
}
