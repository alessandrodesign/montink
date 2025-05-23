<?php

namespace App\Entities;

use App\ValueObjects\Money;
use CodeIgniter\Entity\Entity;

class VariationEntity extends Entity
{
    protected $datamap = [];
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = [
        'id' => 'integer',
        'product_id' => 'integer',
    ];

    private Money $priceAdjustmentObject;

    public function setPriceAdjustment(float|string $priceAdjustment): self
    {
        $this->priceAdjustmentObject = new Money($priceAdjustment);
        $this->attributes['price_adjustment'] = $this->priceAdjustmentObject->getAmount();
        return $this;
    }

    public function getPriceAdjustment(): Money
    {
        if (!isset($this->priceAdjustmentObject)) {
            $this->priceAdjustmentObject = new Money((float)$this->attributes['price_adjustment']);
        }
        return $this->priceAdjustmentObject;
    }
}