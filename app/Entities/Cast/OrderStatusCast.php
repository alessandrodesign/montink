<?php

namespace App\Entities\Cast;

use App\Enums\OrderStatus;
use CodeIgniter\Entity\Cast\BaseCast;

class OrderStatusCast extends BaseCast
{
    public static function get($value, array $params = [])
    {
        return OrderStatus::tryFrom($value);
    }

    public static function set($value, array $params = [])
    {
        return $value instanceof OrderStatus ? $value->value : $value;
    }
}