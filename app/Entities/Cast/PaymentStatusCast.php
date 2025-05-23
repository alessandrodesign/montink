<?php

namespace App\Entities\Cast;

use App\Enums\PaymentStatus;
use CodeIgniter\Entity\Cast\BaseCast;

class PaymentStatusCast extends BaseCast
{
    public static function get($value, array $params = [])
    {
        return PaymentStatus::tryFrom($value);
    }

    public static function set($value, array $params = [])
    {
        return $value instanceof PaymentStatus ? $value->value : $value;
    }
}