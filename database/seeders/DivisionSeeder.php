<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Division;
use Illuminate\Database\Seeder;

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
