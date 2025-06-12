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
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'roll_number' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{4}'),
            'department_id' => Department::factory(),
            'division_id' => Division::factory(),
            'batch' => $this->faker->numberBetween(2020, 2025),
            'password' => Hash::make('password'),
            'is_active' => $this->faker->boolean,
        ];
    }
}
