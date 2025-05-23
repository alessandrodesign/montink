<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class VariationSeeder extends Seeder
{
    public function run()
    {
        $variations = [
            [
                'product_id'      => 1,
                'name'            => '16GB RAM',
                'price_adjustment'=> 400.00,
                'created_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'product_id'      => 1,
                'name'            => '32GB RAM',
                'price_adjustment'=> 800.00,
                'created_at'      => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('variations')->insertBatch($variations);
    }
}