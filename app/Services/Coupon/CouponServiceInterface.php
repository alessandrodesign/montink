<?php

namespace App\Services\Coupon;

use App\Entities\CouponEntity;
use App\Services\ServiceInterface;

interface CouponServiceInterface extends ServiceInterface
{
    public function getAllCoupons(): array;

    public function getCoupon(int $id): ?CouponEntity;

    public function getCouponByCode(string $code): ?CouponEntity;

    public function createCoupon(array $data): ?CouponEntity;

    public function updateCoupon(int $id, array $data): ?CouponEntity;

    public function deleteCoupon(int $id): bool;

    public function validateCoupon(string $code, float $cartTotal): ?CouponEntity;

    public function calculateDiscount(CouponEntity $coupon, float $cartTotal): float;
}