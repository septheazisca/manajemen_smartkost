<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Admin Kost',
                'email' => 'admin@kost.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'role' => 'admin',
                'is_active' => 1,
            ]
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
