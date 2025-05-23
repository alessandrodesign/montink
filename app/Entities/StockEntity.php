<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class StockEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at'];
    protected $casts   = [
        'id' => 'integer',
        'product_id' => 'integer',
        'variation_id' => '?integer',
        'quantity' => 'integer',
    ];
}