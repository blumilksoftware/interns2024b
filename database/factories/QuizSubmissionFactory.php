<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuizSubmission>
 */
class QuizSubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            "quiz_id" => Quiz::factory()->locked(),
            "user_id" => User::factory(),
            "closed_at" => Carbon::now()->addSeconds(3600),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn(array $attributes): array => [
            "closed_at" => Carbon::now(),
        ]);
    }
}
