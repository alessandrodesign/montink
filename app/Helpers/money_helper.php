<?php

use App\ValueObjects\Money;

/**
 * Helper para formatar valores monetários facilmente
 *
 * @param float|int|string $amount
 * @return string
 */
if (!function_exists('money')) {
    function money($amount): string
    {
        $money = new Money($amount);
        return $money->format();
    }
}