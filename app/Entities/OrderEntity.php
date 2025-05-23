<?php

namespace App\Entities;

use App\Entities\Cast\OrderStatusCast;
use App\Entities\Cast\PaymentStatusCast;
use App\Entities\Cast\PaymentMethodCast;
use App\Enums\OrderStatus;
use App\Models\OrderModel;
use App\Services\Order\OrderService;
use App\ValueObjects\Money;
use CodeIgniter\Entity\Entity;

class OrderEntity extends Entity
{
    protected $datamap = [];
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'coupon_id' => '?integer',
        'status' => 'orderStatus',
        'payment_status' => 'paymentStatus',
        'payment_method' => 'paymentMethod',
    ];
    protected $castHandlers = [
        'orderStatus' => OrderStatusCast::class,
        'paymentStatus' => PaymentStatusCast::class,
        'paymentMethod' => PaymentMethodCast::class,
    ];

    private Money $totalAmountObject;
    private Money $discountAmountObject;
    private Money $finalAmountObject;

    public function setTotalAmount(float|string $amount): self
    {
        $this->totalAmountObject = new Money($amount);
        $this->attributes['total_amount'] = $this->totalAmountObject->getAmount();
        return $this;
    }

    public function getTotalAmount(): Money
    {
        if (!isset($this->totalAmountObject)) {
            $this->totalAmountObject = new Money((float)$this->attributes['total_amount']);
        }
        return $this->totalAmountObject;
    }

    public function setDiscountAmount(float|string $amount): self
    {
        $this->discountAmountObject = new Money($amount);
        $this->attributes['discount_amount'] = $this->discountAmountObject->getAmount();
        return $this;
    }

    public function getDiscountAmount(): Money
    {
        if (!isset($this->discountAmountObject)) {
            $this->discountAmountObject = new Money((float)$this->attributes['discount_amount']);
        }
        return $this->discountAmountObject;
    }

    public function setFinalAmount(float|string $amount): self
    {
        $this->finalAmountObject = new Money($amount);
        $this->attributes['final_amount'] = $this->finalAmountObject->getAmount();
        return $this;
    }

    public function getFinalAmount(): Money
    {
        if (!isset($this->finalAmountObject)) {
            $this->finalAmountObject = new Money((float)$this->attributes['final_amount']);
        }
        return $this->finalAmountObject;
    }

    public function generateOrderNumber(): self
    {
        $this->attributes['order_number'] = 'ORD-' . time() . '-' . rand(1000, 9999);
        return $this;
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::PENDING;
    }
}