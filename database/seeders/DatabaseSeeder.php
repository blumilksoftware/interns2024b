<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("schools")->insert([
            "name" => Str::random(10),
            "city" => Str::random(10),
            "street" => Str::random(10),
            "building_number" => Str::random(10),
            "zipCode" => Str::random(10),
        ]);

        DB::table("users")->insert([
            "name" => Str::random(10),
            "surname" => Str::random(10),
            "email" => Str::random(10) . "@example.com",
            "password" => Hash::make("password"),
            "school_id" => 1,
        ]);
    }
}
