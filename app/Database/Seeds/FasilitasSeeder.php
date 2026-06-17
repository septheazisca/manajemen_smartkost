<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FasilitasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Fasilitas Kamar
            ['nama_fasilitas' => 'WiFi',               'tipe' => 'kamar', 'icon' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_fasilitas' => 'AC',                  'tipe' => 'kamar', 'icon' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_fasilitas' => 'Kamar Mandi Dalam',   'tipe' => 'kamar', 'icon' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_fasilitas' => 'Lemari',              'tipe' => 'kamar', 'icon' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_fasilitas' => 'Meja Belajar',        'tipe' => 'kamar', 'icon' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_fasilitas' => 'Kasur',               'tipe' => 'kamar', 'icon' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            // Fasilitas Bersama
            ['nama_fasilitas' => 'CCTV 24 Jam',          'tipe' => 'bersama', 'icon' => 'fa-shield-halved', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_fasilitas' => 'Parkir Motor Luas',     'tipe' => 'bersama', 'icon' => 'fa-motorcycle',    'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_fasilitas' => 'Laundry Mandiri',       'tipe' => 'bersama', 'icon' => 'fa-tshirt',        'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_fasilitas' => 'Dapur Bersama',         'tipe' => 'bersama', 'icon' => 'fa-utensils',      'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_fasilitas' => 'Ruang Tamu Bersama',     'tipe' => 'bersama', 'icon' => 'fa-couch',         'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['nama_fasilitas' => 'Taman Mini',            'tipe' => 'bersama', 'icon' => 'fa-leaf',          'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('fasilitas')->insertBatch($data);
    }
}
