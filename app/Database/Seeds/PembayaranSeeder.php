<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PembayaranSeeder extends Seeder
{
    public function run()
    {
        // Tagihan yang lunas punya pembayaran approved
        // tagihan id 1-9 = bulan 03 semua lunas
        // tagihan id 10-18 = bulan 04, id 10,11,13,14,16,17 lunas
        $data = [];

        $lunasBulan03 = range(1, 9);
        $lunasBulan04 = [10, 11, 13, 14, 16, 17];
        $konfirmasiBulan05 = [20, 25];

        $harga = [1 => 800000, 2 => 800000, 3 => 800000, 4 => 1000000, 5 => 1000000, 6 => 1000000, 7 => 1200000, 8 => 1200000, 9 => 1200000];

        foreach ($lunasBulan03 as $tagihanId) {
            $penyewaId   = $tagihanId;
            $nominalUnik = ($penyewaId % 999) + 1;
            $data[] = [
                'tagihan_id'           => $tagihanId,
                'jumlah_bayar'         => $harga[$penyewaId] + $nominalUnik,
                'bukti_transfer'       => null,
                'status_pembayaran_id' => 2,
                'catatan_admin'        => 'Pembayaran dikonfirmasi',
                'approved_at'          => '2025-03-08 10:00:00',
                'approved_by'          => 1,
                'created_at'           => '2025-03-07 09:00:00',
                'updated_at'           => '2025-03-08 10:00:00',
            ];
        }

        foreach ($lunasBulan04 as $tagihanId) {
            $penyewaId   = $tagihanId - 9;
            $nominalUnik = ($penyewaId % 999) + 1;
            $data[] = [
                'tagihan_id'           => $tagihanId,
                'jumlah_bayar'         => $harga[$penyewaId] + $nominalUnik,
                'bukti_transfer'       => null,
                'status_pembayaran_id' => 2,
                'catatan_admin'        => 'Pembayaran dikonfirmasi',
                'approved_at'          => '2025-04-08 10:00:00',
                'approved_by'          => 1,
                'created_at'           => '2025-04-07 09:00:00',
                'updated_at'           => '2025-04-08 10:00:00',
            ];
        }

        foreach ($konfirmasiBulan05 as $tagihanId) {
            $penyewaId   = $tagihanId - 18;
            $nominalUnik = ($penyewaId % 999) + 1;
            $data[] = [
                'tagihan_id'           => $tagihanId,
                'jumlah_bayar'         => $harga[$penyewaId] + $nominalUnik,
                'bukti_transfer'       => null,
                'status_pembayaran_id' => 1,
                'catatan_admin'        => null,
                'approved_at'          => null,
                'approved_by'          => null,
                'created_at'           => '2025-05-06 09:00:00',
                'updated_at'           => '2025-05-06 09:00:00',
            ];
        }

        $this->db->table('pembayaran')->insertBatch($data);
    }
}
