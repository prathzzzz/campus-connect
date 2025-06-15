<?php

namespace Tests\Unit\Models;

use App\Models\Division;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DivisionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_students()
    {
        Role::create(['name' => 'student']);
        $division = Division::factory()->create();
        $student = Student::factory()->create(['division_id' => $division->id]);

        $this->assertTrue($division->students->contains($student));
    }
}
