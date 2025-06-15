<?php

namespace Tests\Unit\Policies;

use App\Models\Division;
use App\Models\User;
use App\Policies\DivisionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DivisionPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider viewAnyPermissionProvider
     */
    public function it_checks_view_any_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $policy = new DivisionPolicy();
        $this->assertEquals($shouldBeAllowed, $policy->viewAny($user));
    }

    public static function viewAnyPermissionProvider()
    {
        return [
            'user with permission' => ['view-any-division', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     * @dataProvider createPermissionProvider
     */
    public function it_checks_create_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $policy = new DivisionPolicy();
        $this->assertEquals($shouldBeAllowed, $policy->create($user));
    }

    public static function createPermissionProvider()
    {
        return [
            'user with permission' => ['create-division', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     * @dataProvider viewPermissionProvider
     */
    public function it_checks_view_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $division = Division::factory()->create();
        $policy = new DivisionPolicy();
        $this->assertEquals($shouldBeAllowed, $policy->view($user, $division));
    }

    public static function viewPermissionProvider()
    {
        return [
            'user with permission' => ['view-division', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     * @dataProvider updatePermissionProvider
     */
    public function it_checks_update_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $division = Division::factory()->create();
        $policy = new DivisionPolicy();
        $this->assertEquals($shouldBeAllowed, $policy->update($user, $division));
    }

    public static function updatePermissionProvider()
    {
        return [
            'user with permission' => ['update-division', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     * @dataProvider deletePermissionProvider
     */
    public function it_checks_delete_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $division = Division::factory()->create();
        $policy = new DivisionPolicy();
        $this->assertEquals($shouldBeAllowed, $policy->delete($user, $division));
    }

    public static function deletePermissionProvider()
    {
        return [
            'user with permission' => ['delete-division', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     * @dataProvider restorePermissionProvider
     */
    public function it_checks_restore_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $division = Division::factory()->create();
        $policy = new DivisionPolicy();
        $this->assertEquals($shouldBeAllowed, $policy->restore($user, $division));
    }

    public static function restorePermissionProvider()
    {
        return [
            'user with permission' => ['restore-division', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     * @dataProvider forceDeletePermissionProvider
     */
    public function it_checks_force_delete_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $division = Division::factory()->create();
        $policy = new DivisionPolicy();
        $this->assertEquals($shouldBeAllowed, $policy->forceDelete($user, $division));
    }

    public static function forceDeletePermissionProvider()
    {
        return [
            'user with permission' => ['force-delete-division', true],
            'user without permission' => [null, false],
        ];
    }
}