<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TagihanSeeder extends Seeder
{
    public function run()
    {
        $bulanList = ['03', '04', '05'];
        $tahun     = '2025';
        $data      = [];

        // penyewa_id 1-9, harga sesuai kamar
        $harga = [1 => 800000, 2 => 800000, 3 => 800000, 4 => 1000000, 5 => 1000000, 6 => 1000000, 7 => 1200000, 8 => 1200000, 9 => 1200000];

        $statusMap = [
            // bulan 03
            [3, 3, 3, 3, 3, 3, 3, 3, 3],
            // bulan 04
            [3, 3, 4, 3, 3, 4, 3, 3, 4],
            // bulan 05
            [3, 2, 4, 3, 1, 4, 2, 1, 4],
        ];

        foreach ($bulanList as $bi => $bulan) {
            foreach (range(1, 9) as $penyewaId) {
                $nominalUnik = ($penyewaId % 999) + 1;
                $data[] = [
                    'penyewa_id'        => $penyewaId,
                    'bulan'             => $bulan,
                    'tahun'             => $tahun,
                    'jumlah'            => $harga[$penyewaId],
                    'nominal_unik'      => $nominalUnik,
                    'status_tagihan_id' => $statusMap[$bi][$penyewaId - 1],
                    'jatuh_tempo'       => $tahun . '-' . $bulan . '-10',
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => date('Y-m-d H:i:s'),
                ];
            }
        }

        $this->db->table('tagihan')->insertBatch($data);
    }
}
