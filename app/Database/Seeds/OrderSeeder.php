<?php

namespace App\Database\Seeds;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use CodeIgniter\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $order = [
            'user_id' => 2,
            'order_number' => 'ORD' . str_pad(date('ymd'), 10, '0', STR_PAD_LEFT),
            'total_amount' => 3920.00,
            'final_amount' => 3920.00,
            'status' => OrderStatus::COMPLETED->value,
            'payment_status' => PaymentStatus::PAID->value,
            'shipping_address' => 'Rua Exemplo, 123, Centro, Cidade',
            'payment_method' => PaymentMethod::CREDIT_CARD->value,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->table('orders')->insert($order);

        $orderId = $this->db->insertID();

        $items = [
            [
                'order_id' => $orderId,
                'product_id' => 1,
                'variation_id' => 1,
                'price' => 3900.00,
                'quantity' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'order_id' => $orderId,
                'product_id' => 2,
                'variation_id' => null,
                'price' => 20.00,
                'quantity' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('order_items')->insertBatch($items);
    }
}