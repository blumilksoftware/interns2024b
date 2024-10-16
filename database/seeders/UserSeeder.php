<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function fake;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i < 10; $i++) {
            $user = User::firstOrCreate(
                ["email" => "user{$i}@example.com"],
                [
                    "name" => fake()->name,
                    "surname" => fake()->name,
                    "email_verified_at" => Carbon::now(),
                    "password" => Hash::make("user{$i}@example.com"),
                    "remember_token" => Str::random(10),
                    "school_id" => School::all()->random()->id,
                ],
            );
            $user->syncRoles("user");
        }
    }
}
