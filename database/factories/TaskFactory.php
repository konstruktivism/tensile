<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3), // Generates a random task name
            'description' => $this->faker->paragraph(), // Generates a random description
            'project_id' => Project::factory(), // Associates the task with a project
            'completed_at' => $this->faker->optional()->date(), // Generates a random date or null
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
