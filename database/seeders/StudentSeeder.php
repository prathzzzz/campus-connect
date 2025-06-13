<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Division::all()->each(function ($division) {
            Student::factory(10)->create([
                'division_id' => $division->id,
                'department_id' => $division->department_id,
            ]);
        });
    }
}
