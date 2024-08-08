<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Test>
 */
class TestFactory extends Factory
{
    public function definition(): array
    {
        return [
            "name" => fake()->name(),
        ];
    }

    public function locked(): static
    {
        return $this->state(fn(array $attributes): array => [
            "locked_at" => Carbon::now(),
        ]);
    }
}
