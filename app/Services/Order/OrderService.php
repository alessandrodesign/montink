<?php

namespace App\Services\Order;

use App\Entities\OrderEntity;
use App\Entities\OrderItemEntity;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\StockModel;
use App\Services\BaseService;
use App\Services\Cart\CartService;
use App\Services\Email\EmailService;

class OrderService extends BaseService implements OrderServiceInterface
{
    protected OrderModel $orderModel;
    protected OrderItemModel $orderItemModel;
    protected StockModel $stockModel;
    protected CartService $cartService;
    protected EmailService $emailService;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->stockModel = new StockModel();
        $this->cartService = new CartService();
        $this->emailService = new EmailService();
    }

    public function createOrder(int $userId, string $shippingAddress, ?string $paymentMethod = null): ?OrderEntity
    {
        $this->clearErrors();

        if (!$this->cartService->hasItems()) {
            $this->setError('cart', 'Cart is empty');
            return null;
        }

        if (!$this->cartService->validateStock()) {
            $this->errors = array_merge($this->errors, $this->cartService->getErrors());
            return null;
        }

        $cart = $this->cartService->getCart();
        $coupon = $this->cartService->getCoupon();

        $order = new OrderEntity([
            'user_id' => $userId,
            'coupon_id' => $coupon?->id,
            'total_amount' => $cart['subtotal'],
            'discount_amount' => $cart['discount'],
            'final_amount' => $cart['total'],
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
            'payment_method' => $paymentMethod,
            'shipping_address' => $shippingAddress,
        ]);

        $order->generateOrderNumber();

        if (!$this->orderModel->save($order)) {
            $this->errors = $this->orderModel->errors();
            return null;
        }

        $order->id = $this->orderModel->getInsertID();

        // Create order items
        foreach ($cart['items'] as $item) {
            $orderItem = new OrderItemEntity([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'variation_id' => $item['variation_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            if (!$this->orderItemModel->save($orderItem)) {
                // If there's an error, we should rollback the order
                $this->orderModel->delete($order->id);
                $this->errors = $this->orderItemModel->errors();
                return null;
            }

            // Decrement stock
            $this->stockModel->decrementStock(
                $item['product_id'],
                $item['variation_id'],
                $item['quantity']
            );
        }

        // Clear the cart
        $this->cartService->clear();

        // Send order confirmation email
        $this->emailService->sendOrderConfirmation($order);

        return $order;
    }

    public function getOrder(int $id): ?OrderEntity
    {
        return $this->orderModel->getOrderWithItems($id);
    }

    public function getOrderByNumber(string $orderNumber): ?OrderEntity
    {
        $order = $this->orderModel->findByOrderNumber($orderNumber);

        if (!$order) {
            return null;
        }

        return $this->getOrder($order->id);
    }

    public function getUserOrders(int $userId): array
    {
        return $this->orderModel->findByUser($userId);
    }

    public function updateOrderStatus(int $orderId, OrderStatus|string $status): bool
    {
        $this->clearErrors();

        $order = $this->orderModel->find($orderId);

        if (!$order) {
            $this->setError('id', 'Order not found');
            return false;
        }

        $order->status = $status;

        if (!$this->orderModel->save($order)) {
            $this->errors = $this->orderModel->errors();
            return false;
        }

        return true;
    }

    public function updatePaymentStatus(int $orderId, PaymentStatus|string $status): bool
    {
        $this->clearErrors();

        $order = $this->orderModel->find($orderId);

        if (!$order) {
            $this->setError('id', 'Order not found');
            return false;
        }

        $order->payment_status = $status;

        if (!$this->orderModel->save($order)) {
            $this->errors = $this->orderModel->errors();
            return false;
        }

        return true;
    }

    public function cancelOrder(int $orderId): bool
    {
        $this->clearErrors();

        $order = $this->getOrder($orderId);

        if (!$order) {
            $this->setError('id', 'Order not found');
            return false;
        }

        if ($order->status === OrderStatus::COMPLETED) {
            $this->setError('status', 'Cannot cancel a completed order');
            return false;
        }

        // Update order status
        $order->status = OrderStatus::CANCELLED;

        if (!$this->orderModel->save($order)) {
            $this->errors = $this->orderModel->errors();
            return false;
        }

        // Return items to stock
        foreach ($order->items as $item) {
            $this->stockModel->updateStock(
                $item->product_id,
                $item->variation_id,
                $item->quantity,
                true
            );
        }

        return true;
    }
}