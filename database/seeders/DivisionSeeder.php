<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisionNames = ['A', 'B', 'C'];
        Department::all()->each(function ($department) use ($divisionNames) {
            foreach ($divisionNames as $name) {
                Division::create([
                    'name' => $name,
                    'department_id' => $department->id,
                    'is_active' => true,
                ]);
            }
        });
    }
}
