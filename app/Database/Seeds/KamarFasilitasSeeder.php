<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KamarFasilitasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // A-01 (id 1): WiFi, Kasur, Lemari, Meja
            ['kamar_id' => 1, 'fasilitas_id' => 1],
            ['kamar_id' => 1, 'fasilitas_id' => 4],
            ['kamar_id' => 1, 'fasilitas_id' => 5],
            ['kamar_id' => 1, 'fasilitas_id' => 6],
            // A-02 (id 2): WiFi, Kasur, Lemari
            ['kamar_id' => 2, 'fasilitas_id' => 1],
            ['kamar_id' => 2, 'fasilitas_id' => 4],
            ['kamar_id' => 2, 'fasilitas_id' => 6],
            // A-03 (id 3): WiFi, Kasur
            ['kamar_id' => 3, 'fasilitas_id' => 1],
            ['kamar_id' => 3, 'fasilitas_id' => 6],
            // B-01 (id 5): WiFi, AC, KM Dalam, Lemari, Meja, Kasur
            ['kamar_id' => 5, 'fasilitas_id' => 1],
            ['kamar_id' => 5, 'fasilitas_id' => 2],
            ['kamar_id' => 5, 'fasilitas_id' => 3],
            ['kamar_id' => 5, 'fasilitas_id' => 4],
            ['kamar_id' => 5, 'fasilitas_id' => 5],
            ['kamar_id' => 5, 'fasilitas_id' => 6],
            // B-02 (id 6): WiFi, AC, KM Dalam, Kasur
            ['kamar_id' => 6, 'fasilitas_id' => 1],
            ['kamar_id' => 6, 'fasilitas_id' => 2],
            ['kamar_id' => 6, 'fasilitas_id' => 3],
            ['kamar_id' => 6, 'fasilitas_id' => 6],
            // C-01 (id 9): Semua fasilitas
            ['kamar_id' => 9, 'fasilitas_id' => 1],
            ['kamar_id' => 9, 'fasilitas_id' => 2],
            ['kamar_id' => 9, 'fasilitas_id' => 3],
            ['kamar_id' => 9, 'fasilitas_id' => 4],
            ['kamar_id' => 9, 'fasilitas_id' => 5],
            ['kamar_id' => 9, 'fasilitas_id' => 6],
            ['kamar_id' => 9, 'fasilitas_id' => 7],
            ['kamar_id' => 9, 'fasilitas_id' => 9],
            ['kamar_id' => 9, 'fasilitas_id' => 10],
        ];

        $this->db->table('kamar_fasilitas')->insertBatch($data);
    }
}
