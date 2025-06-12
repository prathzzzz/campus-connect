<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Department;
use App\Models\Division;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $department = Department::inRandomOrder()->first();
        $division = $department ? $department->divisions()->inRandomOrder()->first() : null;
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'roll_number' => strtoupper(Str::random(2)) . $this->faker->unique()->numberBetween(1000, 9999),
            'department_id' => $department?->id ?? 1,
            'division_id' => $division?->id ?? 1,
            'batch' => $this->faker->numberBetween(2020, 2025),
            'password' => Hash::make('password'),
            'is_active' => true,
        ];
    }
}
