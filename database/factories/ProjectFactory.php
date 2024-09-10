<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->word()), // Generates a random project name
            'description' => $this->faker->paragraph(), // Generates a random description
            'created_at' => $this->faker->dateTimeBetween('2024-09-01', '2024-09-01'),
            'updated_at' => $this->faker->dateTimeBetween('2024-09-01', '2024-09-01'),
        ];
    }
}
