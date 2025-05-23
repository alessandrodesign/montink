<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';

    public static function fromString(string $role): self
    {
        return match ($role) {
            'admin' => self::ADMIN,
            'user' => self::USER,
            default => throw new \InvalidArgumentException("Invalid role: $role")
        };
    }
}