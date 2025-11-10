<?php

namespace Database\Factories;

use App\Models\Organisation;
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
            'organisation_id' => Organisation::factory(),
            'name' => ucfirst(fake()->word()), // Generates a random project name
            'description' => fake()->paragraph(), // Generates a random description
            'hour_tariff' => fake()->randomFloat(2, 50, 150),
            'is_fixed' => fake()->boolean(30), // 30% chance of being fixed price
            'notifications' => fake()->boolean(70), // 70% chance of notifications enabled
            'is_internal' => fake()->boolean(20), // 20% chance of being internal
            'project_code' => strtoupper(fake()->lexify('???')),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
