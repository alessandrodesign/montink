<?php

namespace App\Controllers;

use App\Services\Coupon\CouponService;

class CouponController extends BaseController
{
    protected CouponService $couponService;

    public function __construct()
    {
        $this->couponService = new CouponService();
    }

    public function index()
    {
        $this->requireAdmin();

        $data = [
            'title' => 'Manage Coupons',
            'coupons' => $this->couponService->getAllCoupons(),
        ];

        return view('admin/coupon/index', $data);
    }

    public function create()
    {
        $this->requireAdmin();

        if ($this->request->is('post')) {
            $couponData = [
                'code' => $this->request->getPost('code'),
                'discount_type' => $this->request->getPost('discount_type'),
                'discount_amount' => $this->request->getPost('discount_amount'),
                'min_purchase' => $this->request->getPost('min_purchase') ?: 0,
                'starts_at' => $this->request->getPost('starts_at') ?: null,
                'expires_at' => $this->request->getPost('expires_at') ?: null,
                'status' => $this->request->getPost('status'),
            ];

            $coupon = $this->couponService->createCoupon($couponData);

            if ($coupon) {
                $this->setMessage('Coupon created successfully');
                return redirect()->to('/admin/coupons');
            } else {
                $this->setValidationErrors($this->couponService->getErrors());
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Create Coupon',
        ];

        return view('admin/coupon/create', $data);
    }

    public function edit($id)
    {
        $this->requireAdmin();

        $coupon = $this->couponService->getCoupon($id);

        if (!$coupon) {
            $this->setMessage('Coupon not found', 'error');
            return redirect()->to('/admin/coupons');
        }

        if ($this->request->is('post')) {
            $couponData = [
                'code' => $this->request->getPost('code'),
                'discount_type' => $this->request->getPost('discount_type'),
                'discount_amount' => $this->request->getPost('discount_amount'),
                'min_purchase' => $this->request->getPost('min_purchase') ?: 0,
                'starts_at' => $this->request->getPost('starts_at') ?: null,
                'expires_at' => $this->request->getPost('expires_at') ?: null,
                'status' => $this->request->getPost('status'),
            ];

            $coupon = $this->couponService->updateCoupon($id, $couponData);

            if ($coupon) {
                $this->setMessage('Coupon updated successfully');
                return redirect()->to('/admin/coupons');
            } else {
                $this->setValidationErrors($this->couponService->getErrors());
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Edit Coupon',
            'coupon' => $coupon,
        ];

        return view('admin/coupon/edit', $data);
    }

    public function delete($id)
    {
        $this->requireAdmin();

        if ($this->couponService->deleteCoupon($id)) {
            $this->setMessage('Coupon deleted successfully');
        } else {
            $this->setMessage('Failed to delete coupon', 'error');
        }

        return redirect()->to('/admin/coupons');
    }
}