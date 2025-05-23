<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run()
    {
        $coupons = [
            [
                'code'           => 'WELCOME10',
                'discount_type'  => 'percent',
                'discount_amount'=> 10,
                'min_purchase'   => 100,
                'starts_at'      => date('Y-m-d'),
                'expires_at'     => date('Y-m-d', strtotime('+30 days')),
                'status'         => 'active',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'code'           => 'FREESHIP',
                'discount_type'  => 'fixed',
                'discount_amount'=> 20,
                'min_purchase'   => 200,
                'starts_at'      => date('Y-m-d'),
                'expires_at'     => date('Y-m-d', strtotime('+60 days')),
                'status'         => 'active',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('coupons')->insertBatch($coupons);
    }
}