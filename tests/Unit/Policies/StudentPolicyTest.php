<?php

namespace Tests\Unit\Policies;

use App\Models\Student;
use App\Models\User;
use App\Policies\StudentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'student']);
    }

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
        $policy = new StudentPolicy;
        $this->assertEquals($shouldBeAllowed, $policy->viewAny($user));
    }

    public static function viewAnyPermissionProvider()
    {
        return [
            'user with permission' => ['view-any-student', true],
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
        $policy = new StudentPolicy;
        $this->assertEquals($shouldBeAllowed, $policy->create($user));
    }

    public static function createPermissionProvider()
    {
        return [
            'user with permission' => ['create-student', true],
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
        $student = Student::factory()->create();
        $policy = new StudentPolicy;
        $this->assertEquals($shouldBeAllowed, $policy->view($user, $student));
    }

    public static function viewPermissionProvider()
    {
        return [
            'user with permission' => ['view-student', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider updatePermissionProvider
     */
    public function it_checks_update_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $student = Student::factory()->create();
        $policy = new StudentPolicy;
        $this->assertEquals($shouldBeAllowed, $policy->update($user, $student));
    }

    public static function updatePermissionProvider()
    {
        return [
            'user with permission' => ['update-student', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider deletePermissionProvider
     */
    public function it_checks_delete_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $student = Student::factory()->create();
        $policy = new StudentPolicy;
        $this->assertEquals($shouldBeAllowed, $policy->delete($user, $student));
    }

    public static function deletePermissionProvider()
    {
        return [
            'user with permission' => ['delete-student', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider restorePermissionProvider
     */
    public function it_checks_restore_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $student = Student::factory()->create();
        $policy = new StudentPolicy;
        $this->assertEquals($shouldBeAllowed, $policy->restore($user, $student));
    }

    public static function restorePermissionProvider()
    {
        return [
            'user with permission' => ['restore-student', true],
            'user without permission' => [null, false],
        ];
    }

    /**
     * @test
     *
     * @dataProvider forceDeletePermissionProvider
     */
    public function it_checks_force_delete_permission($permission, $shouldBeAllowed)
    {
        $user = User::factory()->create();
        if ($permission) {
            $user->givePermissionTo(Permission::create(['name' => $permission]));
        }
        $student = Student::factory()->create();
        $policy = new StudentPolicy;
        $this->assertEquals($shouldBeAllowed, $policy->forceDelete($user, $student));
    }

    public static function forceDeletePermissionProvider()
    {
        return [
            'user with permission' => ['force-delete-student', true],
            'user without permission' => [null, false],
        ];
    }
}
