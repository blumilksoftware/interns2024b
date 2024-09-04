<?php

declare(strict_types=1);

namespace App\Policies;

use App\Http\Requests\UserRequest;

class AuthServiceProvider
{
    protected $policies = [
        UserRequest::class => UserPolicy::class,
    ];
}
