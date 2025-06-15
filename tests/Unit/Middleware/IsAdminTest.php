<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\IsAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Closure;

class IsAdminTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_admin_users()
    {
        $role = Role::create(['name' => 'admin']);
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user);

        $request = new Request();
        $next = function ($request) {
            return 'called';
        };

        $middleware = new IsAdmin();
        $response = $middleware->handle($request, $next);

        $this->assertEquals('called', $response);
    }

    /** @test */
    public function it_redirects_non_admin_users()
    {
        $role = Role::create(['name' => 'user']);
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user);

        $request = new Request();
        $next = function ($request) {
            return 'called';
        };

        $middleware = new IsAdmin();
        $response = $middleware->handle($request, $next);

        $this->assertEquals(302, $response->getStatusCode());
    }

    /** @test */
    public function it_redirects_guests()
    {
        $request = new Request();
        $next = function ($request) {
            return 'called';
        };

        $middleware = new IsAdmin();
        $response = $middleware->handle($request, $next);

        $this->assertEquals(302, $response->getStatusCode());
    }
}