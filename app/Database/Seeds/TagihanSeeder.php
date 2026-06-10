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
            ['lunas', 'lunas', 'lunas', 'lunas', 'lunas', 'lunas', 'lunas', 'lunas', 'lunas'],
            // bulan 04
            ['lunas', 'lunas', 'menunggak', 'lunas', 'lunas', 'menunggak', 'lunas', 'lunas', 'menunggak'],
            // bulan 05
            ['lunas', 'menunggu_konfirmasi', 'menunggak', 'lunas', 'pending', 'menunggak', 'menunggu_konfirmasi', 'pending', 'menunggak'],
        ];

        foreach ($bulanList as $bi => $bulan) {
            foreach (range(1, 9) as $penyewaId) {
                $nominalUnik = ($penyewaId % 999) + 1;
                $data[] = [
                    'penyewa_id'   => $penyewaId,
                    'bulan'        => $bulan,
                    'tahun'        => $tahun,
                    'jumlah'       => $harga[$penyewaId],
                    'nominal_unik' => $nominalUnik,
                    'status'       => $statusMap[$bi][$penyewaId - 1],
                    'jatuh_tempo'  => $tahun . '-' . $bulan . '-10',
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ];
            }
        }

        $this->db->table('tagihan')->insertBatch($data);
    }
}
