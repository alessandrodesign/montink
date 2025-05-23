<?php

namespace App\Services\Order;

use App\Entities\OrderEntity;
use App\Services\ServiceInterface;

interface OrderServiceInterface extends ServiceInterface
{
    public function createOrder(int $userId, string $shippingAddress, ?string $paymentMethod = null): ?OrderEntity;

    public function getOrder(int $id): ?OrderEntity;

    public function getOrderByNumber(string $orderNumber): ?OrderEntity;

    public function getUserOrders(int $userId): array;

    public function updateOrderStatus(int $orderId, string $status): bool;

    public function updatePaymentStatus(int $orderId, string $status): bool;

    public function cancelOrder(int $orderId): bool;
}