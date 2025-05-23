<?php

namespace App\Controllers;

use App\Services\Auth\AuthService;
use App\Services\Order\OrderService;

class OrderController extends BaseController
{
    protected OrderService $orderService;
    protected AuthService $authService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->authService = new AuthService();
    }

    public function index()
    {
        $this->requireLogin();

        $userId = $this->session->get('user_id');

        $data = [
            'title' => 'My Orders',
            'orders' => $this->orderService->getUserOrders($userId),
        ];

        return view('order/index', $data);
    }

    public function view($orderNumber)
    {
        $this->requireLogin();

        $order = $this->orderService->getOrderByNumber($orderNumber);

        if (!$order) {
            $this->setMessage('Order not found', 'error');
            return redirect()->to('/orders');
        }

        // Check if the order belongs to the current user or if the user is admin
        if ($order->user_id != $this->session->get('user_id') && !$this->isAdmin()) {
            $this->setMessage('Access denied', 'error');
            return redirect()->to('/orders');
        }

        $data = [
            'title' => 'Order #' . $order->order_number,
            'order' => $order,
        ];

        return view('order/view', $data);
    }

    public function process()
    {
        $this->requireLogin();

        $userId = $this->session->get('user_id');
        $shippingAddress = $this->request->getPost('shipping_address');
        $paymentMethod = $this->request->getPost('payment_method');
        $order = $this->orderService->createOrder($userId, $shippingAddress, $paymentMethod);

        if ($order) {
            $this->setMessage('Order placed successfully');
            return redirect()->to('/orders/success/' . $order->order_number);
        } else {
            $this->setValidationErrors($this->orderService->getErrors());
            return redirect()->to('/cart/checkout');
        }
    }

    public function success($orderNumber)
    {
        $this->requireLogin();

        $order = $this->orderService->getOrderByNumber($orderNumber);

        if (!$order) {
            $this->setMessage('Order not found', 'error');
            return redirect()->to('/orders');
        }

        // Check if the order belongs to the current user
        if ($order->user_id != $this->session->get('user_id')) {
            $this->setMessage('Access denied', 'error');
            return redirect()->to('/orders');
        }

        $data = [
            'title' => 'Order Successful',
            'order' => $order,
        ];

        return view('order/success', $data);
    }

    public function cancel($orderId)
    {
        $this->requireLogin();

        $order = $this->orderService->getOrder($orderId);

        if (!$order) {
            $this->setMessage('Order not found', 'error');
            return redirect()->to('/orders');
        }

        // Check if the order belongs to the current user or if the user is admin
        if ($order->user_id != $this->session->get('user_id') && !$this->isAdmin()) {
            $this->setMessage('Access denied', 'error');
            return redirect()->to('/orders');
        }

        if ($this->orderService->cancelOrder($orderId)) {
            $this->setMessage('Order cancelled successfully');
        } else {
            $this->setMessage($this->orderService->getErrors()['status'] ?? 'Failed to cancel order', 'error');
        }

        return redirect()->to('/orders');
    }

    // Admin methods

    public function adminIndex()
    {
        $this->requireAdmin();

        $data = [
            'title' => 'Manage Orders',
            'orders' => $this->orderModel->findAll(),
        ];

        return view('admin/order/index', $data);
    }

    public function updateStatus($orderId)
    {
        $this->requireAdmin();

        $order = $this->orderService->getOrder($orderId);

        if (!$order) {
            $this->setMessage('Order not found', 'error');
            return redirect()->to('/admin/orders');
        }

        if ($this->request->is('post')) {
            $status = $this->request->getPost('status');

            if ($this->orderService->updateOrderStatus($orderId, $status)) {
                $this->setMessage('Order status updated successfully');
                return redirect()->to('/admin/orders');
            } else {
                $this->setValidationErrors($this->orderService->getErrors());
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Update Order Status',
            'order' => $order,
        ];

        return view('admin/order/update_status', $data);
    }

    public function updatePaymentStatus($orderId)
    {
        $this->requireAdmin();

        $order = $this->orderService->getOrder($orderId);

        if (!$order) {
            $this->setMessage('Order not found', 'error');
            return redirect()->to('/admin/orders');
        }

        if ($this->request->is('post')) {
            $status = $this->request->getPost('payment_status');

            if ($this->orderService->updatePaymentStatus($orderId, $status)) {
                $this->setMessage('Payment status updated successfully');
                return redirect()->to('/admin/orders');
            } else {
                $this->setValidationErrors($this->orderService->getErrors());
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Update Payment Status',
            'order' => $order,
        ];

        return view('admin/order/update_payment_status', $data);
    }
}