<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function testUserCanRegister(): void
    {
        $school = School::factory()->create();

        $this->post("/auth/register", [
            "name" => "Test",
            "surname" => "Test",
            "email" => "test@example.com",
            "password" => "123456890",
            "school_id" => $school->id,
        ])->assertRedirect("/");

        $this->assertDatabaseHas("users", [
            "name" => "Test",
            "surname" => "Test",
            "email" => "test@example.com",
            "school_id" => $school->id,
        ]);

        $this->assertDatabaseMissing("users", [
            "password" => "123456890",
        ]);
    }
}
