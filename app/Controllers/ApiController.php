<?php

namespace App\Controllers;

use App\Services\Auth\AuthService;
use App\Services\Cart\CartService;
use App\Services\Coupon\CouponService;
use App\Services\Order\OrderService;
use App\Services\Product\ProductService;
use CodeIgniter\API\ResponseTrait;

class ApiController extends BaseController
{
    use ResponseTrait;

    protected AuthService $authService;
    protected ProductService $productService;
    protected CartService $cartService;
    protected CouponService $couponService;
    protected OrderService $orderService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->productService = new ProductService();
        $this->cartService = new CartService();
        $this->couponService = new CouponService();
        $this->orderService = new OrderService();
    }

    // Authentication endpoints

    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->authService->login($email, $password);

        if ($user) {
            return $this->respond([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ]);
        } else {
            return $this->failUnauthorized('Invalid email or password');
        }
    }

    // Product endpoints

    public function getProducts()
    {
        $activeOnly = $this->request->getGet('active_only') === 'true';
        $products = $this->productService->getAllProducts($activeOnly);

        return $this->respond([
            'status' => 'success',
            'products' => $products,
        ]);
    }

    public function getProduct($id)
    {
        $product = $this->productService->getProductWithVariations($id);

        if ($product) {
            return $this->respond([
                'status' => 'success',
                'product' => $product,
            ]);
        } else {
            return $this->failNotFound('Product not found');
        }
    }

    // Cart endpoints

    public function getCart()
    {
        $cart = $this->cartService->getCart();

        return $this->respond([
            'status' => 'success',
            'cart' => $cart,
        ]);
    }

    public function addToCart()
    {
        $productId = (int) $this->request->getPost('product_id');
        $quantity = (int) $this->request->getPost('quantity');
        $variationId = $this->request->getPost('variation_id') ? (int) $this->request->getPost('variation_id') : null;

        if ($quantity <= 0) {
            $quantity = 1;
        }

        if ($this->cartService->addItem($productId, $quantity, $variationId)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Product added to cart',
                'cart' => $this->cartService->getCart(),
            ]);
        } else {
            return $this->fail($this->cartService->getErrors()['stock'] ?? 'Failed to add product to cart');
        }
    }

    public function updateCart()
    {
        $rowId = $this->request->getPost('row_id');
        $quantity = (int) $this->request->getPost('quantity');

        if ($this->cartService->updateItem($rowId, $quantity)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Cart updated',
                'cart' => $this->cartService->getCart(),
            ]);
        } else {
            return $this->fail($this->cartService->getErrors()['stock'] ?? 'Failed to update cart');
        }
    }

    public function removeFromCart($rowId)
    {
        $this->cartService->removeItem($rowId);

        return $this->respond([
            'status' => 'success',
            'message' => 'Item removed from cart',
            'cart' => $this->cartService->getCart(),
        ]);
    }

    public function clearCart()
    {
        $this->cartService->clear();

        return $this->respond([
            'status' => 'success',
            'message' => 'Cart cleared',
            'cart' => $this->cartService->getCart(),
        ]);
    }

    public function applyCoupon()
    {
        $code = $this->request->getPost('coupon_code');

        if (empty($code)) {
            return $this->fail('Please enter a coupon code');
        }

        $cartTotal = $this->cartService->getSubtotal();
        $coupon = $this->couponService->validateCoupon($code, $cartTotal);

        if ($coupon) {
            $this->cartService->applyCoupon($coupon);

            return $this->respond([
                'status' => 'success',
                'message' => 'Coupon applied successfully',
                'cart' => $this->cartService->getCart(),
            ]);
        } else {
            return $this->fail($this->couponService->getErrors()['code'] ?? 'Invalid coupon code');
        }
    }

    // Order endpoints

    public function createOrder()
    {
        if (!$this->authService->isLoggedIn()) {
            return $this->failUnauthorized('Please login to place an order');
        }

        $userId = $this->session->get('user_id');
        $shippingAddress = $this->request->getPost('shipping_address');
        $paymentMethod = $this->request->getPost('payment_method');

        $order = $this->orderService->createOrder($userId, $shippingAddress, $paymentMethod);

        if ($order) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Order placed successfully',
                'order' => $order,
            ]);
        } else {
            return $this->fail($this->orderService->getErrors());
        }
    }

    public function getOrders()
    {
        if (!$this->authService->isLoggedIn()) {
            return $this->failUnauthorized('Please login to view orders');
        }

        $userId = $this->session->get('user_id');
        $orders = $this->orderService->getUserOrders($userId);

        return $this->respond([
            'status' => 'success',
            'orders' => $orders,
        ]);
    }

    public function getOrder($orderNumber)
    {
        if (!$this->authService->isLoggedIn()) {
            return $this->failUnauthorized('Please login to view order');
        }

        $order = $this->orderService->getOrderByNumber($orderNumber);

        if (!$order) {
            return $this->failNotFound('Order not found');
        }

        // Check if the order belongs to the current user or if the user is admin
        if ($order->user_id != $this->session->get('user_id') && !$this->authService->isAdmin()) {
            return $this->failForbidden('Access denied');
        }

        return $this->respond([
            'status' => 'success',
            'order' => $order,
        ]);
    }

    public function cancelOrder($orderId)
    {
        if (!$this->authService->isLoggedIn()) {
            return $this->failUnauthorized('Please login to cancel order');
        }

        $order = $this->orderService->getOrder($orderId);

        if (!$order) {
            return $this->failNotFound('Order not found');
        }

        // Check if the order belongs to the current user or if the user is admin
        if ($order->user_id != $this->session->get('user_id') && !$this->authService->isAdmin()) {
            return $this->failForbidden('Access denied');
        }

        if ($this->orderService->cancelOrder($orderId)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Order cancelled successfully',
            ]);
        } else {
            return $this->fail($this->orderService->getErrors()['status'] ?? 'Failed to cancel order');
        }
    }
}