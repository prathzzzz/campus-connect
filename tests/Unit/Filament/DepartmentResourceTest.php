<?php

namespace Tests\Unit\Filament;

use App\Filament\Resources\DepartmentResource;
use App\Filament\Resources\DepartmentResource\Pages\ListDepartments;
use App\Models\User;
use Filament\Forms\Form;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DepartmentResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_correct_form_fields()
    {
        $form = DepartmentResource::form(new Form($this->createMock(\Filament\Forms\Contracts\HasForms::class)));
        $fields = $form->getComponents();

        $this->assertCount(3, $fields);
        $this->assertEquals('name', $fields[0]->getName());
        $this->assertEquals('code', $fields[1]->getName());
        $this->assertEquals('is_active', $fields[2]->getName());
    }

    #[Test]
    public function it_has_correct_table_columns()
    {
        $adminRole = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'view-any-department']);
        $adminRole->givePermissionTo($permission);
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $this->actingAs($admin);

        Livewire::test(ListDepartments::class)
            ->assertTableColumnExists('name')
            ->assertTableColumnExists('code')
            ->assertTableColumnExists('is_active')
            ->assertTableColumnExists('created_at')
            ->assertTableColumnExists('updated_at');
    }
}
