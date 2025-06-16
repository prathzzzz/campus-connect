<?php

namespace Tests\Unit\Filament;

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\RoleResource\Pages\ListRoles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('app:sync-permissions');
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->user = User::factory()->create();
    }

    #[Test]
    public function users_with_permission_can_list_roles()
    {
        $this->actingAs($this->admin);
        Livewire::test(ListRoles::class)
            ->assertCanSeeTableRecords(Role::all());
    }

    #[Test]
    public function users_without_permission_cannot_list_roles()
    {
        $this->actingAs($this->user);
        Livewire::test(ListRoles::class)
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_create_roles()
    {
        $this->actingAs($this->admin);
        $this->assertTrue(RoleResource::canCreate());
    }

    #[Test]
    public function non_admin_cannot_create_roles()
    {
        $this->actingAs($this->user);
        $this->assertFalse(RoleResource::canCreate());
    }

    #[Test]
    public function admin_can_edit_roles()
    {
        $role = Role::create(['name' => 'new-role']);
        $this->actingAs($this->admin);
        $this->assertTrue(RoleResource::canEdit($role));
    }

    #[Test]
    public function non_admin_cannot_edit_roles()
    {
        $role = Role::create(['name' => 'new-role']);
        $this->actingAs($this->user);
        $this->assertFalse(RoleResource::canEdit($role));
    }

    #[Test]
    public function admin_can_delete_roles()
    {
        $role = Role::create(['name' => 'new-role']);
        $this->actingAs($this->admin);
        $this->assertTrue(RoleResource::canDelete($role));
    }

    #[Test]
    public function admin_cannot_delete_the_admin_role()
    {
        $adminRole = Role::findByName('admin');
        $this->actingAs($this->admin);
        $this->assertFalse(RoleResource::canDelete($adminRole));
    }

    #[Test]
    public function non_admin_cannot_delete_roles()
    {
        $role = Role::create(['name' => 'new-role']);
        $this->actingAs($this->user);
        $this->assertFalse(RoleResource::canDelete($role));
    }
}
