<?php

namespace App\Entities;

use App\Enums\DiscountType;
use App\Enums\ProductStatus;
use App\ValueObjects\Money;
use CodeIgniter\Entity\Entity;
use DateTime;
use Exception;

class CouponEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'starts_at', 'expires_at'];
    protected $casts   = [
        'id' => 'integer',
    ];

    private Money $discountAmountObject;
    private Money $minPurchaseObject;
    private DiscountType $discountTypeEnum;
    private ProductStatus $statusEnum;

    public function setDiscountAmount(float $amount): self
    {
        $this->discountAmountObject = new Money($amount);
        $this->attributes['discount_amount'] = (string) $this->discountAmountObject;
        return $this;
    }

    public function getDiscountAmount(): Money
    {
        if (!isset($this->discountAmountObject)) {
            $this->discountAmountObject = new Money((float) $this->attributes['discount_amount']);
        }
        return $this->discountAmountObject;
    }

    public function setMinPurchase(float $amount): self
    {
        $this->minPurchaseObject = new Money($amount);
        $this->attributes['min_purchase'] = (string) $this->minPurchaseObject;
        return $this;
    }

    public function getMinPurchase(): Money
    {
        if (!isset($this->minPurchaseObject)) {
            $this->minPurchaseObject = new Money((float) $this->attributes['min_purchase']);
        }
        return $this->minPurchaseObject;
    }

    public function setDiscountType(string $type): self
    {
        $this->discountTypeEnum = DiscountType::fromString($type);
        $this->attributes['discount_type'] = $this->discountTypeEnum->value;
        return $this;
    }

    public function getDiscountType(): DiscountType
    {
        if (!isset($this->discountTypeEnum)) {
            $this->discountTypeEnum = DiscountType::fromString($this->attributes['discount_type']);
        }
        return $this->discountTypeEnum;
    }

    public function setStatus(string $status): self
    {
        $this->statusEnum = ProductStatus::fromString($status);
        $this->attributes['status'] = $this->statusEnum->value;
        return $this;
    }

    public function getStatus(): ProductStatus
    {
        if (!isset($this->statusEnum)) {
            $this->statusEnum = ProductStatus::fromString($this->attributes['status']);
        }
        return $this->statusEnum;
    }

    /**
     * @throws Exception
     */
    public function isValid(): bool
    {
        $now = new DateTime();
        $startsAt = $this->attributes['starts_at'] ? new DateTime($this->attributes['starts_at']) : null;
        $expiresAt = $this->attributes['expires_at'] ? new DateTime($this->attributes['expires_at']) : null;

        return $this->getStatus() === ProductStatus::ACTIVE &&
            (!$startsAt || $now >= $startsAt) &&
            (!$expiresAt || $now <= $expiresAt);
    }
}