<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'identity_type' => $this->faker->randomElement(['CC', 'CE', 'NIT', 'Passport']),
            'identity_number' => $this->faker->unique()->randomNumber(8),
            'phone_number' => $this->faker->unique()->phoneNumber(),
            'email' => $this->faker->unique()->email(),
        ];
    }
}
