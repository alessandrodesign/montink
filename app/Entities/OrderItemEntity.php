<?php

namespace App\Entities;

use App\Models\ProductModel;
use App\ValueObjects\Money;
use CodeIgniter\Entity\Entity;

class OrderItemEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at'];
    protected $casts   = [
        'id' => 'integer',
        'order_id' => 'integer',
        'product_id' => 'integer',
        'variation_id' => '?integer',
        'quantity' => 'integer',
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
            $this->priceObject = new Money((float) $this->attributes['price']);
        }
        return $this->priceObject;
    }

    public function getSubtotal(): Money
    {
        return new Money($this->price->getAmount() * $this->attributes['quantity']);
    }

    public function Product()
    {
        return (new ProductModel())->find($this->product_id);
    }
}