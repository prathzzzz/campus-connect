<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_check_if_user_has_a_permission()
    {
        $user = User::factory()->create();
        $permission = Permission::create(['name' => 'edit articles']);

        $user->givePermissionTo($permission);

        $this->assertTrue($user->hasPermissionTo($permission));
    }
}
