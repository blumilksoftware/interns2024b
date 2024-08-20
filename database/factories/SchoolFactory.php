<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<School>
 */
class SchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->name(),
            "city" => fake()->name(),
            "street" => fake()->name(),
            "building_number" => fake()->name(),
            "apartment_number" => fake()->name(),
            "zip_code" => fake()->name(),
        ];
    }

    public function withoutApartment(): static
    {
        return $this->state(fn(array $attributes): array => [
            "apartment_number" => null,
        ]);
    }
}
