<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run()
    {
        $stock = [
            [
                'product_id'   => 1,
                'variation_id' => null,
                'quantity'     => 10,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'product_id'   => 2,
                'variation_id' => null,
                'quantity'     => 50,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'product_id'   => 1,
                'variation_id' => 1,
                'quantity'     => 5,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'product_id'   => 1,
                'variation_id' => 2,
                'quantity'     => 2,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('stock')->insertBatch($stock);
    }
}