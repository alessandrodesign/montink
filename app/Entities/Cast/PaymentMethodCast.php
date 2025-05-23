<?php

namespace App\Entities\Cast;

use App\Enums\PaymentMethod;
use CodeIgniter\Entity\Cast\BaseCast;

class PaymentMethodCast extends BaseCast
{
    public static function get($value, array $params = [])
    {
        return PaymentMethod::tryFrom($value);
    }

    public static function set($value, array $params = [])
    {
        return $value instanceof PaymentMethod ? $value->value : $value;
    }
}