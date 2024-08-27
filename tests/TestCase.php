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

        Role::create(["name" => "admin", "guard_name" => "web"]);
        Role::create(["name" => "user", "guard_name" => "web"]);

        $this->withoutVite();
    }
}
