<?php

declare(strict_types=1);

namespace App;

enum Role: int
{
    case USER = 0;
    case ADMIN = 1;
    case SUPER_ADMIN = 2;
}
