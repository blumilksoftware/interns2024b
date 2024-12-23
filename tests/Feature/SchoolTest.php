<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\FetchSchoolsJob;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SchoolTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $admin_school = School::factory()->disabled()->adminSchool()->create();
        $this->admin = User::factory(["school_id" => $admin_school->id])->admin()->create();
    }

    public function testAdminCanViewSchools(): void
    {
        School::factory()->count(2)->create();

        $this->actingAs($this->admin)
            ->get("/admin/schools")
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component("Admin/SchoolsPanel")
                    ->has("schools.data", 2),
            );
    }

    public function testFilteringAndSortingSchools(): void
    {
        School::factory()->disabled()->create(["name" => "AAAAAA"]);
        School::factory()->create(["name" => "BB"]);
        School::factory()->create(["name" => "CC"]);
        School::factory()->create(["name" => "DD"]);
        School::factory()->create(["name" => "EE"]);
        School::factory()->disabled()->create(["name" => "ZZZZZZ"]);

        $this->actingAs($this->admin)
            ->get("/admin/schools?sort=name&order=asc&disabled=true")
            ->assertInertia(fn(Assert $page) => $page
                ->component("Admin/SchoolsPanel")
                ->has("schools.data", 6)
                ->where("schools.data.0.name", "AAAAAA")
                ->where("schools.data.5.name", "ZZZZZZ"));

        $this->actingAs($this->admin)
            ->get("/admin/schools?sort=name&order=desc&disabled=true")
            ->assertInertia(fn(Assert $page) => $page
                ->component("Admin/SchoolsPanel")
                ->has("schools.data", 6)
                ->where("schools.data.0.name", "ZZZZZZ")
                ->where("schools.data.5.name", "AAAAAA"));

        $this->actingAs($this->admin)
            ->get("/admin/schools?sort=name&order=asc&disabled=false")
            ->assertInertia(fn(Assert $page) => $page
                ->component("Admin/SchoolsPanel")
                ->has("schools.data", 4));

        $this->actingAs($this->admin)
            ->get("/admin/schools?search=AAAAAA&order=asc&disabled=true")
            ->assertInertia(fn(Assert $page) => $page
                ->component("Admin/SchoolsPanel")
                ->has("schools.data", 1));
    }

    public function testAdminCanCreateSchool(): void
    {
        $data = [
            "name" => "test",
            "city" => "Moria",
            "regon" => "743428671",
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
                "regon" => "743428671",
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
            "regon" => "743428671",
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

    public function testAdminCanDeleteEmptySchool(): void
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

    public function testAdminCannotDeleteDisabledSchool(): void
    {
        $school = School::factory()->disabled()->create();

        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/schools/{$school->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("schools", [
            "id" => $school->id,
        ]);
    }

    public function testAdminCannotDeleteSchoolWithStudents(): void
    {
        $school = School::factory()->create();
        $user = User::factory()->create();
        $user->school()->associate($school)->save();

        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/schools/{$school->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas("schools", [
            "id" => $school->id,
        ]);
    }

    public function testAdminCanDisableSchool(): void
    {
        $school = School::factory()->create();

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/schools/{$school->id}/disable")
            ->assertRedirect("/");

        $this->assertDatabaseHas("schools", [
            "id" => $school->id,
            "is_disabled" => true,
        ]);
    }

    public function testAdminCanEnableSchool(): void
    {
        $school = School::factory()->disabled()->create();

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/schools/{$school->id}/enable")
            ->assertRedirect("/");

        $this->assertDatabaseHas("schools", [
            "id" => $school->id,
            "is_disabled" => false,
        ]);
    }

    public function testAdminCannotDeleteSchoolThatNotExisted(): void
    {
        $this->actingAs($this->admin)
            ->from("/")
            ->delete("/admin/schools/1")
            ->assertStatus(404);
    }

    public function testAdminCanFetchSchools(): void
    {
        Queue::fake([
            FetchSchoolsJob::class,
        ]);

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/schools/fetch")
            ->assertJson([
                "message" => "Pobieranie rozpoczęte",
            ]);

        Queue::assertPushed(FetchSchoolsJob::class, 1);
    }

    public function testAdminCannotFetchSchoolsIfFetchingIsAlreadyStared(): void
    {
        Queue::fake([
            FetchSchoolsJob::class,
        ]);

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/schools/fetch")
            ->assertJson([
                "message" => "Pobieranie rozpoczęte",
            ]);

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/schools/fetch")
            ->assertJson([
                "message" => "Pobieranie w toku, proszę czekać",
            ]);

        Queue::assertPushed(FetchSchoolsJob::class, 1);
    }

    public function testAdminCanCheckFetchingProgress(): void
    {
        Queue::fake([
            FetchSchoolsJob::class,
        ]);

        $this->actingAs($this->admin)
            ->from("/")
            ->get("/admin/schools/status")
            ->assertJson([
                "done" => true,
                "count" => 0,
            ]);

        $this->actingAs($this->admin)
            ->from("/")
            ->post("/admin/schools/fetch")
            ->assertJson([
                "message" => "Pobieranie rozpoczęte",
            ]);

        $this->actingAs($this->admin)
            ->from("/")
            ->get("/admin/schools/status")
            ->assertJson([
                "done" => false,
                "count" => 0,
            ]);
    }

    public function testAdminCanSearchForSchools(): void
    {
        School::factory(["name" => "TEST"])->create();

        $this->actingAs($this->admin)->get("/schools/search?search=TEST")
            ->assertStatus(200)
            ->assertJsonCount(1, "data")
            ->assertJsonPath("data.0.name", "TEST");
    }

    public function testGuestCanSearchForSchools(): void
    {
        School::factory(["name" => "TEST"])->create();

        $this->get("/schools/search?search=TEST")
            ->assertStatus(200)
            ->assertJsonCount(1, "data")
            ->assertJsonPath("data.0.name", "TEST");
    }
}
