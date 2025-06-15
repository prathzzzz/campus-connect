<?php

namespace Tests\Unit\Filament;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Forms\Form;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_form_fields()
    {
        $form = UserResource::form(new Form($this->createMock(\Filament\Forms\Contracts\HasForms::class)));
        $fields = $form->getComponents();

        $this->assertCount(5, $fields);
        $this->assertEquals('name', $fields[0]->getName());
        $this->assertEquals('email', $fields[1]->getName());
        $this->assertEquals('email_verified_at', $fields[2]->getName());
        $this->assertEquals('password', $fields[3]->getName());
        $this->assertEquals('roles', $fields[4]->getName());
    }

    /** @test */
    public function it_has_correct_table_columns()
    {
        $adminRole = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'view-any-user']);
        $adminRole->givePermissionTo($permission);
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->assertTableColumnExists('name')
            ->assertTableColumnExists('email')
            ->assertTableColumnExists('roles.name')
            ->assertTableColumnExists('email_verified_at')
            ->assertTableColumnExists('created_at')
            ->assertTableColumnExists('updated_at');
    }

    /** @test */
    public function it_eager_loads_roles_in_get_eloquent_query()
    {
        $query = UserResource::getEloquentQuery();
        $user = $query->find(User::factory()->create()->id);

        $this->assertTrue($user->relationLoaded('roles'));
    }
}