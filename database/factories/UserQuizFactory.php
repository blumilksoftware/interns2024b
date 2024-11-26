<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\User;
use App\Models\UserQuiz;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserQuiz>
 */
class UserQuizFactory extends Factory
{
    public function definition(): array
    {
        return [
            "quiz_id" => Quiz::factory()->locked(),
            "user_id" => User::factory(),
            "closed_at" => Carbon::now()->addMinutes(60),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn(array $attributes): array => [
            "closed_at" => Carbon::now(),
        ]);
    }
}
