<?php

namespace App\Database\Seeds;

use App\Enums\UserRole;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'     => 'Admin',
                'email'    => 'admin@erp.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => UserRole::ADMIN->value,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'     => 'User',
                'email'    => 'user@erp.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role'     => UserRole::USER->value,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}