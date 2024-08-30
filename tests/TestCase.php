<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $roles = config("roles.roles");

        foreach ($roles as $role) {
            Role::firstOrCreate(["name" => $role]);
        }
        $this->withoutVite();
    }
}
