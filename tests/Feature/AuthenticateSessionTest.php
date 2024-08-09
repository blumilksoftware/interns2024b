<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticateSessionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function testUserCanLogin(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create(["email" => "test@example.com", "password" => "1234567890"]);

        $this->post("/auth/login", [
            "email" => "test@example.com",
            "password" => "123456890",
        ])->assertRedirect("/");

        $this->assertDatabaseHas("users", [
            "email" => "test@example.com",
        ]);
    }
}
