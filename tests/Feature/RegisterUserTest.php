<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanRegister(): void
    {
        $school = School::factory()->create();

        $this->post("/auth/register", [
            "name" => "Test",
            "surname" => "Test",
            "email" => "test@gmail.com",
            "password" => "123456890",
            "school_id" => $school->id,
        ])->assertRedirect("/");

        $this->assertDatabaseHas("users", [
            "name" => "Test",
            "surname" => "Test",
            "email" => "test@gmail.com",
            "school_id" => $school->id,
        ]);

        $this->assertDatabaseMissing("users", [
            "password" => "123456890",
        ]);
    }

    public function testUserCanNotRegisterWithAlreadyTakenEmail(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create([
            "email" => "test@gmail.com",
        ]);

        $this->post("/auth/register", [
            "name" => "Test",
            "surname" => "Test",
            "email" => "test@gmail.com",
            "password" => "123456890",
            "school_id" => $school->id,
        ])->assertRedirect("/")
            ->assertSessionHasErrors(["email" => "The email has already been taken."]);
    }

    public function testUserCanNotRegisterWithWrongSchoolIndex(): void
    {
        $school = School::factory()->create();

        $this->post("/auth/register", [
            "name" => "Test",
            "surname" => "Test",
            "email" => "test@gmail.com",
            "password" => "123456890",
            "school_id" => $school->id + 99999,
        ])->assertRedirect("/")
            ->assertSessionHasErrors(["school_id" => "Your school is invalid. Check it again."]);
    }

    public function testUserCanNotRegisterWithTooLongEmail(): void
    {
        $longMail = Str::random(250);
        $school = School::factory()->create();

        $this->post("/auth/register", [
            "name" => "Test",
            "surname" => "Test",
            "email" => $longMail . "@gmail.com",
            "password" => "123456890",
            "school_id" => $school->id,
        ])->assertRedirect("/")->assertSessionHasErrors(["email" => "Your email is too long. It must not be greater than 255 characters."]);
    }

    public function testUserCanNotRegisterWithWrongEmailDomain(): void
    {
        $school = School::factory()->create();

        $this->post("/auth/register", [
            "name" => "Test",
            "surname" => "Test",
            "email" => "test@gmail.pl",
            "password" => "123456890",
            "school_id" => $school->id,
        ])->assertRedirect("/")->assertSessionHasErrors(["email" => "Your email is invalid."]);
    }

    public function testUserCanNotRegisterWithTooLongName(): void
    {
        $longName = Str::random(256);
        $school = School::factory()->create();

        $this->post("/auth/register", [
            "name" => $longName,
            "surname" => "Test",
            "email" => "test@gmail.com",
            "password" => "123456890",
            "school_id" => $school->id,
        ])->assertRedirect("/")->assertSessionHasErrors(["name" => "Your name is too long. It must not be greater than 255 characters."]);
    }

    public function testUserCanNotRegisterWithTooLongSurname(): void
    {
        $longSurname = Str::random(256);
        $school = School::factory()->create();

        $this->post("/auth/register", [
            "name" => "Test",
            "surname" => $longSurname,
            "email" => "test@gmail.com",
            "password" => "123456890",
            "school_id" => $school->id,
        ])->assertRedirect("/")->assertSessionHasErrors(["surname" => "Your surname is too long. It must not be greater than 255 characters."]);
    }

    public function testUserCanNotRegisterWithTooShortPassword(): void
    {
        $school = School::factory()->create();

        $this->post("/auth/register", [
            "name" => "Test",
            "surname" => "Test",
            "email" => "test@gmail.com",
            "password" => "123",
            "school_id" => $school->id,
        ])->assertRedirect("/")->assertSessionHasErrors(["password" => "Your password is too short. It must be at least 8 characters."]);
    }
}
