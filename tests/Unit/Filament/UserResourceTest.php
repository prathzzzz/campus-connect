<?php

namespace Tests\Unit\Filament;

use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('app:sync-permissions');
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->user = User::factory()->create();
    }

    /** @test */
    public function users_with_permission_can_list_users()
    {
        $this->actingAs($this->admin);
        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords(User::all());
    }

    /** @test */
    public function users_without_permission_cannot_list_users()
    {
        $this->actingAs($this->user);
        Livewire::test(ListUsers::class)
            ->assertForbidden();
    }
}
