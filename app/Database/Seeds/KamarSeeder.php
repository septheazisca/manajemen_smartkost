<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KamarSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nomor_kamar' => 'A-01', 'tipe' => 'Standard', 'lantai' => 1, 'luas' => '3x4', 'harga' => 800000,  'status_kamar_id' => 2,   'deskripsi' => 'Kamar standar lantai 1', 'foto' => 'room-standard.jpg', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'A-02', 'tipe' => 'Standard', 'lantai' => 1, 'luas' => '3x4', 'harga' => 800000,  'status_kamar_id' => 2,   'deskripsi' => 'Kamar standar lantai 1', 'foto' => 'room-standard.jpg', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'A-03', 'tipe' => 'Standard', 'lantai' => 1, 'luas' => '3x4', 'harga' => 800000,  'status_kamar_id' => 2,   'deskripsi' => 'Kamar standar lantai 1', 'foto' => 'room-standard.jpg', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'A-04', 'tipe' => 'Standard', 'lantai' => 1, 'luas' => '3x4', 'harga' => 800000,  'status_kamar_id' => 1,   'deskripsi' => 'Kamar standar lantai 1', 'foto' => 'room-standard.jpg', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'B-01', 'tipe' => 'Deluxe',   'lantai' => 2, 'luas' => '4x4', 'harga' => 1000000, 'status_kamar_id' => 2,   'deskripsi' => 'Kamar deluxe lantai 2',  'foto' => 'room-deluxe.jpg',   'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'B-02', 'tipe' => 'Deluxe',   'lantai' => 2, 'luas' => '4x4', 'harga' => 1000000, 'status_kamar_id' => 2,   'deskripsi' => 'Kamar deluxe lantai 2',  'foto' => 'room-deluxe.jpg',   'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'B-03', 'tipe' => 'Deluxe',   'lantai' => 2, 'luas' => '4x4', 'harga' => 1000000, 'status_kamar_id' => 2,   'deskripsi' => 'Kamar deluxe lantai 2',  'foto' => 'room-deluxe.jpg',   'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'B-04', 'tipe' => 'Deluxe',   'lantai' => 2, 'luas' => '4x4', 'harga' => 1000000, 'status_kamar_id' => 1,   'deskripsi' => 'Kamar deluxe lantai 2',  'foto' => 'room-deluxe.jpg',   'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'C-01', 'tipe' => 'Premium',  'lantai' => 3, 'luas' => '4x5', 'harga' => 1200000, 'status_kamar_id' => 2,   'deskripsi' => 'Kamar premium lantai 3', 'foto' => 'room-premium.jpg',  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'C-02', 'tipe' => 'Premium',  'lantai' => 3, 'luas' => '4x5', 'harga' => 1200000, 'status_kamar_id' => 2,   'deskripsi' => 'Kamar premium lantai 3', 'foto' => 'room-premium.jpg',  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'C-03', 'tipe' => 'Premium',  'lantai' => 3, 'luas' => '4x5', 'harga' => 1200000, 'status_kamar_id' => 2,   'deskripsi' => 'Kamar premium lantai 3', 'foto' => 'room-premium.jpg',  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nomor_kamar' => 'C-04', 'tipe' => 'Premium',  'lantai' => 3, 'luas' => '4x5', 'harga' => 1200000, 'status_kamar_id' => 1,   'deskripsi' => 'Kamar premium lantai 3', 'foto' => 'room-premium.jpg',  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('kamar')->insertBatch($data);
    }
}
