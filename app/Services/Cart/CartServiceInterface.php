<?php

namespace App\Services\Cart;

use App\Entities\CouponEntity;
use App\Services\ServiceInterface;

interface CartServiceInterface extends ServiceInterface
{
    public function getCart(): array;

    public function addItem(int $productId, int $quantity = 1, ?int $variationId = null): bool;

    public function updateItem(string $rowId, int $quantity): bool;

    public function removeItem(string $rowId): bool;

    public function clear(): bool;

    public function getTotal(): float;

    public function getSubtotal(): float;

    public function getDiscount(): float;

    public function applyCoupon(CouponEntity $coupon): bool;

    public function removeCoupon(): bool;

    public function getCoupon(): ?CouponEntity;

    public function getItemCount(): int;

    public function hasItems(): bool;

    public function validateStock(): bool;
}