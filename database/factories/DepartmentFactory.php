<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => fake()->randomElement([
                'Sales',
                'Marketing',
                'Human Resources',
                'IT',
                'Customer Service',
                'Operations',
                'Finance',
                'Engineering',
                'Design',
                'Support',
            ]),
        ];
    }
}