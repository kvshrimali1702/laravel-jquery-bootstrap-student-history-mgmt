<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $streetNumber = fake()->optional()->numberBetween(1, 9999);
        $streetName = fake()->optional()->streetName();

        return [
            'student_id' => null, // Will be set when creating
            'full_address' => fake()->address(),
            'street_number' => $streetNumber ? (string) $streetNumber : null,
            'street_name' => $streetName,
            'city' => fake()->city(),
            'postcode' => fake()->postcode(),
            'state' => fake()->state(),
            'country' => fake()->country(),
        ];
    }
}
