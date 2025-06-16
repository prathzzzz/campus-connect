<?php

namespace Tests\Feature\UserManagement;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $regularUser;

    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->regularUser = User::factory()->create();
        $this->testUser = User::factory()->create();
        $this->actingAs($this->regularUser);
    }

    public function test_unauthorized_user_cannot_view_user_list(): void
    {
        $this->get(UserResource::getUrl('index'))->assertForbidden();
    }

    public function test_unauthorized_user_cannot_view_create_user_page(): void
    {
        $this->get(UserResource::getUrl('create'))->assertForbidden();
    }

    public function test_unauthorized_user_cannot_view_edit_user_page(): void
    {
        $this->get(UserResource::getUrl('edit', ['record' => $this->testUser]))->assertForbidden();
    }
}
