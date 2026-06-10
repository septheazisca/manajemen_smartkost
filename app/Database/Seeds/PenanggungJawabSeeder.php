<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PenanggungJawabSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'user_id'      => 2,
                'nama'         => 'Budi Santoso',
                'phone'        => '081200000002',
                'spesialisasi' => 'Listrik',
                'gaji_bulanan' => 2500000,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'user_id'      => 3,
                'nama'         => 'Hendra Wijaya',
                'phone'        => '081200000003',
                'spesialisasi' => 'Plumbing',
                'gaji_bulanan' => 2500000,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('penanggung_jawab')->insertBatch($data);
    }
}
