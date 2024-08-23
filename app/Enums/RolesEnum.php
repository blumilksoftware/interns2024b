<?php

declare(strict_types=1);

namespace App\Enums;

enum RolesEnum: string
{
    case USER = "user";
    case ADMIN = "admin";
    case SUPERADMIN = "super-admin";

    public function label(): string
    {
        return match ($this) {
            static::USER => "Users",
            static::ADMIN => "Admins",
            static::SUPERADMIN => "Super Admins",
        };
    }
}
