<?php

namespace App\Services\Coupon;

use App\Entities\CouponEntity;
use App\Enums\DiscountType;
use App\Models\CouponModel;
use App\Services\BaseService;

class CouponService extends BaseService implements CouponServiceInterface
{
    protected CouponModel $couponModel;

    public function __construct()
    {
        $this->couponModel = new CouponModel();
    }

    public function getAllCoupons(): array
    {
        return $this->couponModel->findAll();
    }

    public function getCoupon(int $id): ?CouponEntity
    {
        return $this->couponModel->find($id);
    }

    public function getCouponByCode(string $code): ?CouponEntity
    {
        return $this->couponModel->findByCode($code);
    }

    public function createCoupon(array $data): ?CouponEntity
    {
        $this->clearErrors();

        $coupon = new CouponEntity($data);

        if (!$this->couponModel->save($coupon)) {
            $this->errors = $this->couponModel->errors();
            return null;
        }

        $coupon->id = $this->couponModel->getInsertID();

        return $coupon;
    }

    public function updateCoupon(int $id, array $data): ?CouponEntity
    {
        $this->clearErrors();

        $coupon = $this->couponModel->find($id);

        if (!$coupon) {
            $this->setError('id', 'Coupon not found');
            return null;
        }

        $coupon->fill($data);

        if (!$this->couponModel->save($coupon)) {
            $this->errors = $this->couponModel->errors();
            return null;
        }

        return $coupon;
    }

    public function deleteCoupon(int $id): bool
    {
        $this->clearErrors();

        $coupon = $this->couponModel->find($id);

        if (!$coupon) {
            $this->setError('id', 'Coupon not found');
            return false;
        }

        return $this->couponModel->delete($id);
    }

    public function validateCoupon(string $code, float $cartTotal): ?CouponEntity
    {
        $this->clearErrors();

        $coupon = $this->couponModel->findByCode($code);

        if (!$coupon) {
            $this->setError('code', 'Coupon not found');
            return null;
        }

        if (!$coupon->isValid()) {
            $this->setError('code', 'Coupon is not valid or has expired');
            return null;
        }

        $minPurchase = $coupon->getMinPurchase()->getAmount();
        if ($minPurchase > 0 && $cartTotal < $minPurchase) {
            $this->setError('code', 'Minimum purchase amount not met');
            return null;
        }

        return $coupon;
    }

    public function calculateDiscount(CouponEntity $coupon, float $cartTotal): float
    {
        $discountAmount = $coupon->getDiscountAmount()->getAmount();

        if ($coupon->getDiscountType() === DiscountType::PERCENTAGE) {
            return min($cartTotal * ($discountAmount / 100), $cartTotal);
        } else {
            return min($discountAmount, $cartTotal);
        }
    }
}