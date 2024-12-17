<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\CreateAdminAction;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAdminActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateAdminAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        School::factory()->adminSchool()->create();
        $this->action = new CreateAdminAction();
    }

    public function testCreateAdmin(): void
    {
        $user = $this->action->execute([
            "firstname" => "Admin Name",
            "surname" => "Admin Surname",
            "email" => "adminexample@admin.com",
            "password" => "password",
        ]);

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "firstname" => "Admin Name",
            "surname" => "Admin Surname",
            "email" => "adminexample@admin.com",
        ]);

        $this->assertTrue($user->hasRole("admin"));
    }
}
