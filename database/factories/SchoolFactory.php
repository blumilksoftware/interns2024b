<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

use function str;

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
            "name" => fake()->company(),
            "regon" => str(fake()->randomNumber(9)),
            "city" => fake()->city(),
            "street" => fake()->streetName(),
            "building_number" => fake()->buildingNumber(),
            "apartment_number" => fake()->buildingNumber(),
            "zip_code" => fake()->postcode(),
        ];
    }

    public function withoutApartment(): static
    {
        return $this->state(fn(array $attributes): array => [
            "apartment_number" => null,
        ]);
    }
}
