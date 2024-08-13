<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
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

    public function testUserCanNotRegisterWithAlreadyTakenEmail(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create();

        $this->post("/auth/register", [
            "name" => "Test",
            "surname" => "Test",
            "email" => $user->email,
            "password" => "123456890",
            "school_id" => $school->id,
        ])->assertRedirect("/")->assertSessionHasErrors(["email" => "The email has already been taken."]);
    }

    public function testUserCanNotRegisterWithWrongSchool(): void
    {
        $school = School::factory()->create();

        $this->post("/auth/register", [
            "name" => "Test",
            "surname" => "Test",
            "email" => "test@example.com",
            "password" => "123456890",
            "school_id" => $school->id + 99999,
        ])->assertRedirect("/")->assertSessionHasErrors(["school_id" => "The selected school id is invalid."]);
    }
}
