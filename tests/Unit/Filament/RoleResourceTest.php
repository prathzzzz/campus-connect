<?php

namespace Tests\Unit\Filament;

use App\Filament\Resources\RoleResource;
use App\Filament\Resources\RoleResource\Pages\ListRoles;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RoleResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_form_fields()
    {
        $form = RoleResource::form(new Form($this->createMock(\Filament\Forms\Contracts\HasForms::class)));
        $fields = $form->getComponents();

        $this->assertCount(2, $fields);
        $this->assertInstanceOf(Section::class, $fields[0]);
        $this->assertInstanceOf(Section::class, $fields[1]);

        $firstSectionFields = $fields[0]->getChildComponents();
        $this->assertCount(1, $firstSectionFields);
        $this->assertEquals('name', $firstSectionFields[0]->getName());

        $secondSectionFields = $fields[1]->getChildComponents();
        $this->assertCount(1, $secondSectionFields);
        $this->assertEquals('permissions', $secondSectionFields[0]->getName());
    }

    /** @test */
    public function it_has_correct_table_columns()
    {
        $adminRole = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'view-any-role']);
        $adminRole->givePermissionTo($permission);
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $this->actingAs($admin);

        Livewire::test(ListRoles::class)
            ->assertTableColumnExists('name')
            ->assertTableColumnExists('created_at');
    }

    /**
     * @test
     *
     * @dataProvider canViewAnyPermissionProvider
     */
    public function it_checks_can_view_any_permission($permission, $shouldBeAllowed)
    {
        /** @var User $user */
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $this->actingAs($user);
        $this->assertEquals($shouldBeAllowed, \App\Filament\Resources\RoleResource::canViewAny());
    }

    public static function canViewAnyPermissionProvider()
    {
        return [
            'user with permission' => ['view-any-role', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider canCreatePermissionProvider
     */
    public function it_checks_can_create_permission($permission, $shouldBeAllowed)
    {
        /** @var User $user */
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $this->actingAs($user);
        $this->assertEquals($shouldBeAllowed, \App\Filament\Resources\RoleResource::canCreate());
    }

    public static function canCreatePermissionProvider()
    {
        return [
            'user with permission' => ['create-role', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider canEditPermissionProvider
     */
    public function it_checks_can_edit_permission($permission, $shouldBeAllowed)
    {
        /** @var User $user */
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $this->actingAs($user);
        $role = \Spatie\Permission\Models\Role::create(['name' => 'some-role']);
        $this->assertEquals($shouldBeAllowed, \App\Filament\Resources\RoleResource::canEdit($role));
    }

    public static function canEditPermissionProvider()
    {
        return [
            'user with permission' => ['update-role', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider canDeletePermissionProvider
     */
    public function it_checks_can_delete_permission($permission, $roleName, $shouldBeAllowed)
    {
        /** @var User $user */
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $this->actingAs($user);
        $role = \Spatie\Permission\Models\Role::create(['name' => $roleName]);
        $this->assertEquals($shouldBeAllowed, \App\Filament\Resources\RoleResource::canDelete($role));
    }

    public static function canDeletePermissionProvider()
    {
        return [
            'user with permission can delete non-admin role' => ['delete-role', 'some-role', true],
            'user with permission cannot delete admin role' => ['delete-role', 'admin', false],
            'user without permission cannot delete non-admin role' => [null, 'some-role', false],
        ];
    }
}
