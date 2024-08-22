<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SchoolTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function testAdminCanViewSchools(): void
    {
        School::factory()->count(2)->create();

        $this->actingAs($this->admin)
            ->get("/admin/schools")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("SchoolsPanel")
                    ->has("schools", 3),
            );
    }

    public function testAdminCanCreateSchool(): void
    {
        $data = [
            "name" => "test",
            "city" => "Moria",
            "street" => "example",
            "building_number" => "3",
            "zip_code" => "00-120",
        ];

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/schools", $data)
            ->assertRedirect("/");

        $this->assertDatabaseHas("schools", $data);
    }

    public function testAdminCannotCreateInvalidSchool(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/schools", [])
            ->assertRedirect("/")->assertSessionHasErrors(["name", "city", "street", "building_number", "zip_code"]);
    }

    public function testAdminCanEditSchool(): void
    {
        $school = School::factory()->create();

        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/schools/{$school->id}", [
                "name" => "test",
                "city" => "Moria",
                "street" => "example",
                "building_number" => "3",
                "apartment_number" => "2A",
                "zip_code" => "00-120",
            ])
            ->assertRedirect("/");

        $this->assertDatabaseHas("schools", [
            "id" => $school->id,
            "name" => "test",
            "city" => "Moria",
            "street" => "example",
            "building_number" => "3",
            "apartment_number" => "2A",
            "zip_code" => "00-120",
        ]);
    }

    public function testAdminCannotEditSchoolThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->patch("/admin/schools/1", [])
            ->assertStatus(404);
    }

    public function testAdminCanDeleteSchool(): void
    {
        $school = School::factory()->create();

        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/schools/{$school->id}")
            ->assertRedirect("/");

        $this->assertDatabaseMissing("schools", [
            "id" => $school->id,
        ]);
    }

    public function testAdminCannotDeleteSchoolThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/schools/1")
            ->assertStatus(404);
    }
}
