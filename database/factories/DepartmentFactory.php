<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'IT', 'HR', 'Finance', 'Marketing', 'Operations', 'Sales', 'Legal', 'Admin',
            ]),
        ];
    }
}
