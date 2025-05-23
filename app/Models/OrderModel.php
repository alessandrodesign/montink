<?php

namespace App\Models;

use App\Entities\OrderEntity;
use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = OrderEntity::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id', 'coupon_id', 'order_number', 'total_amount',
        'discount_amount', 'final_amount', 'status', 'payment_status',
        'payment_method', 'shipping_address'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id'               => 'permit_empty',
        'user_id'          => 'required|integer|is_not_unique[users.id]',
        'coupon_id'        => 'permit_empty',
        'order_number'     => 'required|min_length[5]|max_length[50]|is_unique[orders.order_number,id,{id}]',
        'total_amount'     => 'required|numeric|greater_than[0]',
        'discount_amount'  => 'required|numeric|greater_than_equal_to[0]',
        'final_amount'     => 'required|numeric|greater_than[0]',
        'status'           => 'required|in_list[pending,processing,completed,cancelled]',
        'payment_status'   => 'required|in_list[pending,paid,failed]',
        'payment_method'   => 'permit_empty',
        'shipping_address' => 'permit_empty',
    ];

    public function findByUser(int $userId)
    {
        return $this->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll();
    }

    public function findByOrderNumber(string $orderNumber)
    {
        return $this->where('order_number', $orderNumber)->first();
    }

    public function getOrderWithItems(int $orderId)
    {
        $order = $this->find($orderId);

        if (!$order) {
            return null;
        }

        $orderItemModel = new OrderItemModel();
        $order->items = $orderItemModel->findByOrder($orderId);

        return $order;
    }
}