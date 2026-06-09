<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KamarSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nomor_kamar' => 'A-01', 'lantai' => 1, 'luas' => '3x4', 'harga' => 800000,  'status' => 'terisi',   'deskripsi' => 'Kamar standar lantai 1', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'A-02', 'lantai' => 1, 'luas' => '3x4', 'harga' => 800000,  'status' => 'terisi',   'deskripsi' => 'Kamar standar lantai 1', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'A-03', 'lantai' => 1, 'luas' => '3x4', 'harga' => 800000,  'status' => 'terisi',   'deskripsi' => 'Kamar standar lantai 1', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'A-04', 'lantai' => 1, 'luas' => '3x4', 'harga' => 800000,  'status' => 'kosong',   'deskripsi' => 'Kamar standar lantai 1', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'B-01', 'lantai' => 2, 'luas' => '4x4', 'harga' => 1000000, 'status' => 'terisi',   'deskripsi' => 'Kamar deluxe lantai 2',  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'B-02', 'lantai' => 2, 'luas' => '4x4', 'harga' => 1000000, 'status' => 'terisi',   'deskripsi' => 'Kamar deluxe lantai 2',  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'B-03', 'lantai' => 2, 'luas' => '4x4', 'harga' => 1000000, 'status' => 'terisi',   'deskripsi' => 'Kamar deluxe lantai 2',  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'B-04', 'lantai' => 2, 'luas' => '4x4', 'harga' => 1000000, 'status' => 'kosong',   'deskripsi' => 'Kamar deluxe lantai 2',  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'C-01', 'lantai' => 3, 'luas' => '4x5', 'harga' => 1200000, 'status' => 'terisi',   'deskripsi' => 'Kamar premium lantai 3', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'C-02', 'lantai' => 3, 'luas' => '4x5', 'harga' => 1200000, 'status' => 'terisi',   'deskripsi' => 'Kamar premium lantai 3', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'C-03', 'lantai' => 3, 'luas' => '4x5', 'harga' => 1200000, 'status' => 'terisi',   'deskripsi' => 'Kamar premium lantai 3', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'C-04', 'lantai' => 3, 'luas' => '4x5', 'harga' => 1200000, 'status' => 'kosong',   'deskripsi' => 'Kamar premium lantai 3', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('kamar')->insertBatch($data);
    }
}
