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
        $school = School::firstOrCreate(
            ["is_admin_school" => true],
            [
                "name" => "Admin school",
                "regon" => "",
                "city" => "",
                "street" => "",
                "building_number" => "",
                "zip_code" => "",
                "is_disabled" => true,
                "is_admin_school" => true,
            ],
        );

        $superAdmin = User::firstOrCreate(
            ["email" => "superadmin@example.com"],
            [
                "firstname" => "Super",
                "surname" => "Admin",
                "email_verified_at" => Carbon::now(),
                "password" => Hash::make("interns2024b"),
                "remember_token" => Str::random(10),
                "school_id" => $school->id,
            ],
        );

        $superAdmin->syncRoles("super_admin");
    }
}
