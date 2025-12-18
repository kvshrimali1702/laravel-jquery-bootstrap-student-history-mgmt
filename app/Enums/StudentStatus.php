<?php

namespace App\Enums;

enum StudentStatus: int
{
    case Inactive = 0;
    case Active = 1;

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
        };
    }
}
