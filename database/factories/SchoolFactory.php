<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Helpers\RegonHelper;
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
            "name" => fake()->company(),
            "regon" => RegonHelper::generateShortRegon(),
            "city" => fake()->city(),
            "street" => fake()->streetName(),
            "building_number" => fake()->buildingNumber(),
            "apartment_number" => fake()->buildingNumber(),
            "zip_code" => fake()->randomNumber(2) . "-" . fake()->randomNumber(3),
            "is_disabled" => false,
            "is_admin_school" => false,
        ];
    }

    public function disabled()
    {
        return $this->state(fn(array $attributes): array => [
            "is_disabled" => true,
        ]);
    }

    public function adminSchool()
    {
        return $this->state(fn(array $attributes): array => [
            "is_admin_school" => true,
        ]);
    }

    public function withoutApartment(): static
    {
        return $this->state(fn(array $attributes): array => [
            "apartment_number" => null,
        ]);
    }
}
