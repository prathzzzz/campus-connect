<?php

namespace Tests\Feature\UserManagement;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::create(['name' => 'delete-user', 'guard_name' => 'web']);
    }

    public function test_user_can_delete_other_users_with_permission(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete-user');
        $otherUser = User::factory()->create();

        $this->assertTrue((new UserPolicy)->delete($user, $otherUser));
    }

    public function test_user_cannot_delete_other_users_without_permission(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->assertFalse((new UserPolicy)->delete($user, $otherUser));
    }

    public function test_user_cannot_delete_themselves(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete-user');

        $this->assertFalse((new UserPolicy)->delete($user, $user));
    }
}
