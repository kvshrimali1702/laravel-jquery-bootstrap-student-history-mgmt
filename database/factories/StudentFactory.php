<?php

namespace Database\Factories;

use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'profile_picture' => fake()->optional()->imageUrl(200, 200, 'people'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'birth_date' => fake()->dateTimeBetween('-18 years', '-10 years'),
            'standard' => fake()->numberBetween(1, 12),
            'status' => fake()->randomElement([StudentStatus::Active, StudentStatus::Inactive]),
        ];
    }
}
