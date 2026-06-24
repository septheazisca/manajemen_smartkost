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
                'phone'                => '081100001001',
                'is_active'            => 1,
                'must_change_password' => 0,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            // PJ
            [
                'name'                 => 'Budi Santoso',
                'email'                => 'budi@smartkost.com',
                'password'             => password_hash('081100001002', PASSWORD_DEFAULT),
                'role'                 => 'pj',
                'phone'                => '081100001002',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Hendra Wijaya',
                'email'                => 'hendra@smartkost.com',
                'password'             => password_hash('081100001003', PASSWORD_DEFAULT),
                'role'                 => 'pj',
                'phone'                => '081100001003',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            // Penyewa
            [
                'name'                 => 'Andi Pratama',
                'email'                => 'andi@gmail.com',
                'password'             => password_hash('081100001004', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081100001004',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Siti Rahayu',
                'email'                => 'siti@gmail.com',
                'password'             => password_hash('081100001005', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081100001005',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Rizky Firmansyah',
                'email'                => 'rizky@gmail.com',
                'password'             => password_hash('081100001006', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081100001006',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Dewi Lestari',
                'email'                => 'dewi@gmail.com',
                'password'             => password_hash('081100001007', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081100001007',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Fajar Nugroho',
                'email'                => 'fajar@gmail.com',
                'password'             => password_hash('081100001008', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081100001008',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Maya Sari',
                'email'                => 'maya@gmail.com',
                'password'             => password_hash('081100001009', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081100001009',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Doni Setiawan',
                'email'                => 'doni@gmail.com',
                'password'             => password_hash('081100001010', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081100001010',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Putri Handayani',
                'email'                => 'putri@gmail.com',
                'password'             => password_hash('081100001011', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081100001011',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'name'                 => 'Wahyu Hidayat',
                'email'                => 'wahyu@gmail.com',
                'password'             => password_hash('081100001012', PASSWORD_DEFAULT),
                'role'                 => 'penyewa',
                'phone'                => '081100001012',
                'is_active'            => 1,
                'must_change_password' => 1,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
        ];

        // Avoid seeding failure when user already exists (email is UNIQUE)
        $builder = $this->db->table('users');
        foreach ($data as $row) {
            $exists = $builder->where('email', $row['email'])->countAllResults() > 0;
            if (!$exists) {
                $builder->insert($row);
            }
        }
    }
}

