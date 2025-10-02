<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class OrganisationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'email' => $this->faker->companyEmail(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'website' => $this->faker->url(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
