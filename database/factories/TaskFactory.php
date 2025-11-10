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
            'name' => fake()->sentence(3), // Generates a random task name
            'description' => fake()->paragraph(), // Generates a random description
            'project_id' => Project::factory(), // Associates the task with a project
            'completed_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'minutes' => fake()->numberBetween(30, 480), // 30 minutes to 8 hours
            'icalUID' => fake()->uuid(),
            'is_service' => fake()->boolean(20), // 20% chance of being a service
            'invoiced' => fake()->boolean(60) ? now() : null, // 60% chance of being invoiced
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
