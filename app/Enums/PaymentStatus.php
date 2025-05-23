<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';

    public static function fromString(string $status): self
    {
        return match ($status) {
            'pending' => self::PENDING,
            'paid' => self::PAID,
            'failed' => self::FAILED,
            default => throw new \InvalidArgumentException("Invalid status: $status")
        };
    }
}