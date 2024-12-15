<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\ForceChangePasswordAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForceChangePasswordActionTest extends TestCase
{
    use RefreshDatabase;

    private ForceChangePasswordAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new ForceChangePasswordAction();
    }

    public function testActionSetQuizLocal(): void
    {
        $user = User::factory()->create();
        $this->action->execute($user);

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "force_password_change" => true,
        ]);
    }
}
