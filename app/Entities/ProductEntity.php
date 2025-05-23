<?php

namespace App\Entities;

use App\Entities\Cast\ProductStatusCast;
use App\Enums\ProductStatus;
use App\ValueObjects\Money;
use CodeIgniter\Entity\Entity;

class ProductEntity extends Entity
{
    protected $datamap = [];
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = [
        'id' => 'integer',
        'status' => 'productStatus',
    ];
    protected $castHandlers = [
        'productStatus' => ProductStatusCast::class,
    ];

    private Money $priceObject;

    public function setPrice(float|string $price): self
    {
        $this->priceObject = new Money($price);
        $this->attributes['price'] = $this->priceObject->getAmount();
        return $this;
    }

    public function getPrice(): Money
    {
        if (!isset($this->priceObject)) {
            $this->priceObject = new Money((float)$this->attributes['price']);
        }
        return $this->priceObject;
    }

    public function isActive(): bool
    {
        return $this->status === ProductStatus::ACTIVE;
    }
}