<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ForecastTask>
 */
class ForecastTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'project_id' => Project::factory(),
            'scheduled_at' => fake()->dateTimeBetween('now', '+3 months'),
            'minutes' => fake()->numberBetween(30, 480),
            'icalUID' => fake()->uuid(),
            'is_service' => fake()->boolean(20),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
