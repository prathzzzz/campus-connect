<?php

namespace Tests\Unit\Models;

use App\Models\Department;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_students()
    {
        Role::create(['name' => 'student']);
        $department = Department::factory()->create();
        $student = Student::factory()->create(['department_id' => $department->id]);

        $this->assertTrue($department->students->contains($student));
    }
}