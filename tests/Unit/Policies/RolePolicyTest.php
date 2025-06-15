<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\RolePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @dataProvider viewAnyPermissionProvider
     */
    public function it_checks_view_any_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $policy = new RolePolicy;
        $this->assertEquals($shouldBeAllowed, $policy->viewAny($user));
    }

    public static function viewAnyPermissionProvider()
    {
        return [
            'user with permission' => ['view-any-role', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider createPermissionProvider
     */
    public function it_checks_create_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $policy = new RolePolicy;
        $this->assertEquals($shouldBeAllowed, $policy->create($user));
    }

    public static function createPermissionProvider()
    {
        return [
            'user with permission' => ['create-role', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider viewPermissionProvider
     */
    public function it_checks_view_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $role = Role::create(['name' => 'some-role']);
        $policy = new RolePolicy;
        $this->assertEquals($shouldBeAllowed, $policy->view($user, $role));
    }

    public static function viewPermissionProvider()
    {
        return [
            'user with permission' => ['view-role', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider updatePermissionProvider
     */
    public function it_checks_update_permission($permission, $roleName, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $role = Role::create(['name' => $roleName]);
        $policy = new RolePolicy;
        $this->assertEquals($shouldBeAllowed, $policy->update($user, $role));
    }

    public static function updatePermissionProvider()
    {
        return [
            'user with permission can update non-admin role' => ['update-role', 'some-role', true],
            'user with permission cannot update admin role' => ['update-role', 'admin', false],
            'user without permission cannot update non-admin role' => [null, 'some-role', false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider deletePermissionProvider
     */
    public function it_checks_delete_permission($permission, $roleName, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $role = Role::create(['name' => $roleName]);
        $policy = new RolePolicy;
        $this->assertEquals($shouldBeAllowed, $policy->delete($user, $role));
    }

    public static function deletePermissionProvider()
    {
        return [
            'user with permission can delete non-admin role' => ['delete-role', 'some-role', true],
            'user with permission cannot delete admin role' => ['delete-role', 'admin', false],
            'user without permission cannot delete non-admin role' => [null, 'some-role', false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider restorePermissionProvider
     */
    public function it_checks_restore_permission($permission, $roleName, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $role = Role::create(['name' => $roleName]);
        $policy = new RolePolicy;
        $this->assertEquals($shouldBeAllowed, $policy->restore($user, $role));
    }

    public static function restorePermissionProvider()
    {
        return [
            'user with permission can restore non-admin role' => ['restore-role', 'some-role', true],
            'user with permission cannot restore admin role' => ['restore-role', 'admin', false],
            'user without permission cannot restore non-admin role' => [null, 'some-role', false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider forceDeletePermissionProvider
     */
    public function it_checks_force_delete_permission($permission, $roleName, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $role = Role::create(['name' => $roleName]);
        $policy = new RolePolicy;
        $this->assertEquals($shouldBeAllowed, $policy->forceDelete($user, $role));
    }

    public static function forceDeletePermissionProvider()
    {
        return [
            'user with permission can force delete non-admin role' => ['force-delete-role', 'some-role', true],
            'user with permission cannot force delete admin role' => ['force-delete-role', 'admin', false],
            'user without permission cannot force delete non-admin role' => [null, 'some-role', false],
        ];
    }
}
