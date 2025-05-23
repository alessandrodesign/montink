<?php

namespace App\Entities\Cast;

use App\Enums\ProductStatus;
use CodeIgniter\Entity\Cast\BaseCast;

class ProductStatusCast extends BaseCast
{
    public static function get($value, array $params = [])
    {
        return ProductStatus::tryFrom($value);
    }

    public static function set($value, array $params = [])
    {
        return $value instanceof ProductStatus ? $value->value : $value;
    }
}