<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // php spark db:seed DatabaseSeeder
    public function run()
    {
        $this->call('UserSeeder');
        $this->call('ProductSeeder');
        $this->call('VariationSeeder');
        $this->call('StockSeeder');
        $this->call('CouponSeeder');
        $this->call('OrderSeeder');
    }
}