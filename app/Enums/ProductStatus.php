<?php

namespace App\Enums;

enum ProductStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public static function fromString(string $status): self
    {
        return match ($status) {
            'active' => self::ACTIVE,
            'inactive' => self::INACTIVE,
            default => throw new \InvalidArgumentException("Invalid status: $status")
        };
    }
}