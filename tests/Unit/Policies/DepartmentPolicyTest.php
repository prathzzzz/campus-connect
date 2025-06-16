<?php

namespace Tests\Unit\Policies;

use App\Models\Department;
use App\Models\User;
use App\Policies\DepartmentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DepartmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    #[DataProvider('viewAnyPermissionProvider')]
    public function it_checks_view_any_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }

        $policy = new DepartmentPolicy;

        $this->assertEquals($shouldBeAllowed, $policy->viewAny($user));
    }

    #[Test]
    #[DataProvider('createPermissionProvider')]
    public function it_checks_create_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }

        $policy = new DepartmentPolicy;

        $this->assertEquals($shouldBeAllowed, $policy->create($user));
    }

    public static function viewAnyPermissionProvider()
    {
        return [
            'user with permission' => ['view-any-department', true],
            'user without permission' => [null, false],
        ];
    }

    public static function createPermissionProvider()
    {
        return [
            'user with permission' => ['create-department', true],
            'user without permission' => [null, false],
        ];
    }

    #[Test]
    #[DataProvider('viewPermissionProvider')]
    public function it_checks_view_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $department = Department::factory()->create();
        $policy = new DepartmentPolicy;
        $this->assertEquals($shouldBeAllowed, $policy->view($user, $department));
    }

    public static function viewPermissionProvider()
    {
        return [
            'user with permission' => ['view-department', true],
            'user without permission' => [null, false],
        ];
    }

    #[Test]
    #[DataProvider('updatePermissionProvider')]
    public function it_checks_update_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $department = Department::factory()->create();
        $policy = new DepartmentPolicy;
        $this->assertEquals($shouldBeAllowed, $policy->update($user, $department));
    }

    public static function updatePermissionProvider()
    {
        return [
            'user with permission' => ['update-department', true],
            'user without permission' => [null, false],
        ];
    }

    #[Test]
    #[DataProvider('deletePermissionProvider')]
    public function it_checks_delete_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $department = Department::factory()->create();
        $policy = new DepartmentPolicy;
        $this->assertEquals($shouldBeAllowed, $policy->delete($user, $department));
    }

    public static function deletePermissionProvider()
    {
        return [
            'user with permission' => ['delete-department', true],
            'user without permission' => [null, false],
        ];
    }

    #[Test]
    #[DataProvider('restorePermissionProvider')]
    public function it_checks_restore_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $department = Department::factory()->create();
        $policy = new DepartmentPolicy;
        $this->assertEquals($shouldBeAllowed, $policy->restore($user, $department));
    }

    public static function restorePermissionProvider()
    {
        return [
            'user with permission' => ['restore-department', true],
            'user without permission' => [null, false],
        ];
    }

    #[Test]
    #[DataProvider('forceDeletePermissionProvider')]
    public function it_checks_force_delete_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $department = Department::factory()->create();
        $policy = new DepartmentPolicy;
        $this->assertEquals($shouldBeAllowed, $policy->forceDelete($user, $department));
    }

    public static function forceDeletePermissionProvider()
    {
        return [
            'user with permission' => ['force-delete-department', true],
            'user without permission' => [null, false],
        ];
    }
}
