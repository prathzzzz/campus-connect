<?php

namespace Tests\Unit\Filament;

use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('app:sync-permissions');
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->user = User::factory()->create();
    }

    #[Test]
    public function users_with_permission_can_list_users()
    {
        $this->actingAs($this->admin);
        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords(User::all());
    }

    #[Test]
    public function users_without_permission_cannot_list_users()
    {
        $this->actingAs($this->user);
        Livewire::test(ListUsers::class)
            ->assertForbidden();
    }
}
