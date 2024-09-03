<?php

declare(strict_types=1);

namespace App\Policies;

use App\Http\Requests\UserRequest;
use App\Models\Quiz;

class AuthServiceProvider
{
    protected $policies = [
        UserRequest::class => UserPolicy::class,
        Quiz::class => RankingPolicy::class,
    ];
}
