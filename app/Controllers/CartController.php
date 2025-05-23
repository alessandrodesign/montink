<?php

namespace App\Controllers;

use App\Services\Cart\CartService;
use App\Services\Coupon\CouponService;
use App\Services\Product\ProductService;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class CartController extends BaseController
{
    use ResponseTrait;

    protected CartService $cartService;
    protected ProductService $productService;
    protected CouponService $couponService;

    public function __construct()
    {
        $this->cartService = new CartService();
        $this->productService = new ProductService();
        $this->couponService = new CouponService();
    }

    public function index()
    {
        $data = [
            'title' => 'Shopping Cart',
            'cart' => $this->cartService->getCart(),
        ];

        return view('cart/index', $data);
    }

    public function add()
    {
        $productId = (int)$this->request->getPost('product_id');
        $quantity = (int)$this->request->getPost('quantity');
        $variationId = $this->request->getPost('variation_id') ? (int)$this->request->getPost('variation_id') : null;

        if ($quantity <= 0) {
            $quantity = 1;
        }

        if ($this->cartService->addItem($productId, $quantity, $variationId)) {
            $this->setMessage('Product added to cart');
        } else {
            $this->setMessage($this->cartService->getErrors()['stock'] ?? 'Failed to add product to cart', 'error');
        }

        return redirect()->back();
    }

    public function update()
    {
        $rowId = $this->request->getPost('row_id');
        $quantity = (int)$this->request->getPost('quantity');

        if ($this->cartService->updateItem($rowId, $quantity)) {
            $this->setMessage('Cart updated');
        } else {
            $this->setMessage($this->cartService->getErrors()['stock'] ?? 'Failed to update cart', 'error');
        }

        return redirect()->to('/cart');
    }

    public function remove($rowId)
    {
        $this->cartService->removeItem($rowId);
        $this->setMessage('Item removed from cart');

        return redirect()->to('/cart');
    }

    public function clear()
    {
        $this->cartService->clear();
        $this->setMessage('Cart cleared');

        return redirect()->to('/cart');
    }

    public function applyCoupon(): ResponseInterface
    {
        $code = $this->request->getVar('coupon_code');

        if (empty($code)) {
            return $this->fail(t('Please enter a coupon code'));
        }

        $cartTotal = $this->cartService->getSubtotal();

        $coupon = $this->couponService->validateCoupon($code, $cartTotal);

        if ($coupon) {
            $this->cartService->applyCoupon($coupon);
            return $this->respond([
                'message' => t('Coupon applied successfully'),
                'type' => 'primary',
                'reload' => true,
            ]);
        }

        return $this->fail(t($this->couponService->getErrors()['code'] ?? 'Invalid coupon code'));
    }

    public function removeCoupon()
    {
        $this->cartService->removeCoupon();
        $this->setMessage('Coupon removed');

        return redirect()->to('/cart');
    }

    public function checkout()
    {
        $this->requireLogin();

        if (!$this->cartService->hasItems()) {
            $this->setMessage('Your cart is empty', 'error');
            return redirect()->to('/cart');
        }

        if (!$this->cartService->validateStock()) {
            $errors = $this->cartService->getErrors();
            $errorMessage = reset($errors);
            $this->setMessage($errorMessage, 'error');
            return redirect()->to('/cart');
        }

        $data = [
            'title' => 'Checkout',
            'cart' => $this->cartService->getCart(),
            'user' => $this->session->get('user'),
        ];

        return view('cart/checkout', $data);
    }
}