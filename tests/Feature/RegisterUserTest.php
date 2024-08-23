<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(["name" => "admin", "guard_name" => "web"]);
        Role::create(["name" => "user", "guard_name" => "web"]);
    }

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

    public function testUserCanNotCheckIfEmailIsAlreadyTakenViaRegisterForm(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create([
            "email" => "test@gmail.com",
        ]);
        $user->assignRole("user");

        $this->post("/auth/register", [
            "name" => "Test",
            "surname" => "Test",
            "email" => "test@gmail.com",
            "password" => "123456890",
            "school_id" => $school->id,
        ])->assertRedirect("/");
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
            ->assertSessionHasErrors(["school_id" => "Szkoła nie istnieje. Sprawdź ponownie."]);
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
        ])->assertRedirect("/")->assertSessionHasErrors(["email" => "Pole e-mail nie może być dłuższe niż 255 znaków."]);
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
        ])->assertRedirect("/")->assertSessionHasErrors(["email" => "Pole e-mail nie jest poprawnym adresem e-mail."]);
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
        ])->assertRedirect("/")->assertSessionHasErrors(["name" => "Pole imię nie może być dłuższe niż 255 znaków."]);
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
        ])->assertRedirect("/")->assertSessionHasErrors(["surname" => "Pole nazwisko nie może być dłuższe niż 255 znaków."]);
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
        ])->assertRedirect("/")->assertSessionHasErrors(["password" => "Pole hasło musi mieć przynajmniej 8 znaków."]);
    }
}
