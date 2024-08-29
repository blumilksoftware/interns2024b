<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ["email" => "superadmin@example.com"],
            [
                "name" => "Super Admin Name",
                "surname" => "Super Admin Surname",
                "email_verified_at" => Carbon::now(),
                "password" => Hash::make("superadmin@example.com"),
                "remember_token" => Str::random(10),
                "school_id" => School::factory()->create()->id,
            ],
        );
        $superAdmin->syncRoles("super_admin");

        $admin = User::firstOrCreate(
            ["email" => "admin@example.com"],
            [
                "name" => "Admin Name",
                "surname" => "Admin Surname",
                "email_verified_at" => Carbon::now(),
                "password" => Hash::make("superadmin@example.com"),
                "remember_token" => Str::random(10),
                "school_id" => School::factory()->create()->id,
            ],
        );
        $admin->syncRoles("admin");

        $user = User::firstOrCreate(
            ["email" => "user@example.com"],
            [
                "name" => "User Name",
                "surname" => "User Surname",
                "email_verified_at" => Carbon::now(),
                "password" => Hash::make("user@example.com"),
                "remember_token" => Str::random(10),
                "school_id" => School::factory()->create()->id,
            ],
        );
        $user->syncRoles("user");
    }
}
