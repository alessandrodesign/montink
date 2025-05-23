<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name'        => 'Notebook Pro',
                'description' => 'High performance notebook.',
                'price'       => 3500.00,
                'status'      => 'active',
                'image'       => null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse.',
                'price'       => 120.00,
                'status'      => 'active',
                'image'       => null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('products')->insertBatch($products);
    }
}