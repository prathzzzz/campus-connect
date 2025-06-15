<?php

namespace Tests\Unit\Filament;

use App\Filament\Resources\StudentResource;
use App\Filament\Resources\StudentResource\Pages\ListStudents;
use App\Models\User;
use Filament\Forms\Form;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_form_fields()
    {
        $form = StudentResource::form(new Form($this->createMock(\Filament\Forms\Contracts\HasForms::class)));
        $fields = $form->getComponents();

        $this->assertCount(8, $fields);
        $this->assertEquals('name', $fields[0]->getName());
        $this->assertEquals('email', $fields[1]->getName());
        $this->assertEquals('roll_number', $fields[2]->getName());
        $this->assertEquals('department_id', $fields[3]->getName());
        $this->assertEquals('division_id', $fields[4]->getName());
        $this->assertEquals('batch', $fields[5]->getName());
        $this->assertEquals('is_active', $fields[6]->getName());
        $this->assertEquals('password', $fields[7]->getName());
    }

    /** @test */
    public function it_has_correct_table_columns()
    {
        $adminRole = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'view-any-student']);
        $adminRole->givePermissionTo($permission);
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $this->actingAs($admin);

        Livewire::test(ListStudents::class)
            ->assertTableColumnExists('name')
            ->assertTableColumnExists('email')
            ->assertTableColumnExists('roll_number')
            ->assertTableColumnExists('department.name')
            ->assertTableColumnExists('division.name')
            ->assertTableColumnExists('batch')
            ->assertTableColumnExists('is_active')
            ->assertTableColumnExists('created_at')
            ->assertTableColumnExists('updated_at');
    }

    /** @test */
    public function it_mutates_form_data_before_create()
    {
        $data = [
            'name' => 'Test Student',
            'email' => 'test@test.com',
            'password' => '',
        ];

        $mutatedData = StudentResource::mutateFormDataBeforeCreate($data);

        $this->assertNotEmpty($mutatedData['password']);
        $this->assertTrue(Hash::check('password', $mutatedData['password']));
    }
}
