<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Division;
use App\Models\Student;

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
