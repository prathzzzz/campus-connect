<?php

namespace Tests\Unit\Filament;

use App\Filament\Resources\ActivityLogResource\Pages\ListActivityLogs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityLogResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_table_columns()
    {
        $adminRole = Role::create(['name' => 'admin']);
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $this->actingAs($admin);

        Livewire::test(ListActivityLogs::class)
            ->assertTableColumnExists('description')
            ->assertTableColumnExists('causer.name')
            ->assertTableColumnExists('subject_type')
            ->assertTableColumnExists('created_at');
    }
}
