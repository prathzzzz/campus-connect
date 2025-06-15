<?php

namespace Tests\Unit\Filament;

use App\Filament\Resources\DivisionResource;
use App\Filament\Resources\DivisionResource\Pages\ListDivisions;
use App\Models\User;
use Filament\Forms\Form;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DivisionResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_form_fields()
    {
        $form = DivisionResource::form(new Form($this->createMock(\Filament\Forms\Contracts\HasForms::class)));
        $fields = $form->getComponents();

        $this->assertCount(3, $fields);
        $this->assertEquals('department_id', $fields[0]->getName());
        $this->assertEquals('name', $fields[1]->getName());
        $this->assertEquals('is_active', $fields[2]->getName());
    }

    /** @test */
    public function it_has_correct_table_columns()
    {
        $adminRole = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'view-any-division']);
        $adminRole->givePermissionTo($permission);
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $this->actingAs($admin);

        Livewire::test(ListDivisions::class)
            ->assertTableColumnExists('department.name')
            ->assertTableColumnExists('name')
            ->assertTableColumnExists('is_active')
            ->assertTableColumnExists('created_at')
            ->assertTableColumnExists('updated_at');
    }
}
