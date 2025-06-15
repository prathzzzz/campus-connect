<?php

namespace Tests\Unit\Models;

use App\Models\Department;
use App\Models\Division;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'student']);
    }

    /** @test */
    public function it_belongs_to_a_department()
    {
        $department = Department::factory()->create();
        $student = Student::factory()->create(['department_id' => $department->id]);

        $this->assertTrue($student->department->is($department));
    }

    /** @test */
    public function it_belongs_to_a_division()
    {
        $division = Division::factory()->create();
        $student = Student::factory()->create(['division_id' => $division->id]);

        $this->assertTrue($student->division->is($division));
    }
}
