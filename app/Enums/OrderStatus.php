<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public static function fromString(string $status): self
    {
        return match ($status) {
            'pending' => self::PENDING,
            'processing' => self::PROCESSING,
            'completed' => self::COMPLETED,
            'cancelled' => self::CANCELLED,
            default => throw new \InvalidArgumentException("Invalid status: $status")
        };
    }
}