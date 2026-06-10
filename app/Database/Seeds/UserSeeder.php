<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Admin
            [
                'name'                 => 'Admin SmarKost',
                'email'                => 'admin@smartkost.com',
                'password'             => password_hash('Admin123', PASSWORD_DEFAULT),
                'role'                 => 'admin',
                'phone'                => '081200000001',
                'is_active'            => 1,
                'must_change_password' => 0,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            // PJ
            [
                'name'                 => 'Budi Santoso',
                'email'                => 'budi@smartkost.com',
                'password'             => password_hash('081200000002', PASSWORD_DEFAULT),
                'role'                 => 'pj',
                'phone'                => '081200000002',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Hendra Wijaya',
                'email'                => 'hendra@smartkost.com',
                'password'             => password_hash('081200000003', PASSWORD_DEFAULT),
                'role'                 => 'pj',
                'phone'                => '081200000003',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            // Penyewa
            [
                'name'                 => 'Andi Pratama',
                'email'                => 'andi@gmail.com',
                'password'             => password_hash('081200000004', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081200000004',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Siti Rahayu',
                'email'                => 'siti@gmail.com',
                'password'             => password_hash('081200000005', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081200000005',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Rizky Firmansyah',
                'email'                => 'rizky@gmail.com',
                'password'             => password_hash('081200000006', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081200000006',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Dewi Lestari',
                'email'                => 'dewi@gmail.com',
                'password'             => password_hash('081200000007', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081200000007',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Fajar Nugroho',
                'email'                => 'fajar@gmail.com',
                'password'             => password_hash('081200000008', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081200000008',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Maya Sari',
                'email'                => 'maya@gmail.com',
                'password'             => password_hash('081200000009', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081200000009',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Doni Setiawan',
                'email'                => 'doni@gmail.com',
                'password'             => password_hash('081200000010', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081200000010',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Putri Handayani',
                'email'                => 'putri@gmail.com',
                'password'             => password_hash('081200000011', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081200000011',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Wahyu Hidayat',
                'email'                => 'wahyu@gmail.com',
                'password'             => password_hash('081200000012', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081200000012',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
