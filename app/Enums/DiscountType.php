<?php

namespace App\Enums;

enum DiscountType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public static function fromString(string $type): self
    {
        return match ($type) {
            'percentage' => self::PERCENTAGE,
            'fixed' => self::FIXED,
            default => throw new \InvalidArgumentException("Invalid discount type: $type")
        };
    }
}