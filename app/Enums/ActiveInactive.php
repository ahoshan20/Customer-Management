<?php

namespace App\Enums;

enum ActiveInactive: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'badge badge-success',
            self::INACTIVE => 'badge badge-danger',
        };
    }
}
