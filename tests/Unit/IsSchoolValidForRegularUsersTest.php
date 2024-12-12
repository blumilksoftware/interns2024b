<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\School;
use App\Models\User;
use App\Rules\IsSchoolValidForRegularUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class IsSchoolValidForRegularUsersTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();
    }

    public function testValidSchool(): void
    {
        $school = School::factory()->create();

        $this->assertTrue($this->validate($school->id));
    }

    public function testAdminSchool(): void
    {
        Lang::shouldReceive("get")->with("validation.custom.school_id.exists");

        $school = School::factory()->adminSchool()->create();
        $this->assertFalse($this->validate($school->id));
    }

    public function testDisabledSchool(): void
    {
        Lang::shouldReceive("get")->with("validation.custom.school_id.exists");

        $school = School::factory()->disabled()->create();
        $this->assertFalse($this->validate($school->id));
    }

    protected function validate(int $value): bool
    {
        $failed = false;
        $rule = new IsSchoolValidForRegularUsers();

        $rule->validate("regon", $value, function ($err) use (&$failed): void {
            $failed = true;
        });

        return !$failed;
    }
}
