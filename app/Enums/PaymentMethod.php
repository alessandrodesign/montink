<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CREDIT_CARD = 'credit_card';
    case BANK_SLIP = 'bank_slip';
    case PIX = 'pix';

    public static function fromString(string $method): self
    {
        return match ($method) {
            self::CREDIT_CARD->value => self::CREDIT_CARD,
            self::BANK_SLIP->value => self::BANK_SLIP,
            self::PIX->value => self::PIX,
            default => throw new \InvalidArgumentException("Invalid payment method: $method")
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::CREDIT_CARD => t('Credit card'),
            self::BANK_SLIP => t('Bank slip'),
            self::PIX => 'PIX',
        };
    }
}