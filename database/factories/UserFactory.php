<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\School;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use function fake;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            "firstname" => fake()->firstName(),
            "surname" => fake()->lastName(),
            "email" => fake()->unique()->safeEmail(),
            "email_verified_at" => Carbon::now(),
            "password" => Hash::make("password"),
            "remember_token" => Str::random(10),
            "school_id" => School::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            $user->assignRole("user");
        });
    }

    public function admin(): static
    {
        return $this->afterCreating(function (User $user): void {
            $user->syncRoles("admin");
        });
    }

    public function superAdmin(): static
    {
        return $this->afterCreating(function (User $user): void {
            $user->syncRoles("super_admin");
        });
    }

    public function unverifiedUser(): static
    {
        return $this->state(function (array $attributes) {
            return [
                "email_verified_at" => null,
            ];
        });
    }

    public function anonymized(): static
    {
        return $this->state(function (array $attributes) {
            return [
                "firstname" => "Anonymous",
                "surname" => "User",
                "email" => "anonymous" . fake()->unique()->randomNumber() . "@email",
                "is_anonymized" => true,
            ];
        });
    }
}
