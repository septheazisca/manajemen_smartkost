<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengeluaranSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Gaji PJ bulan 03
            ['keterangan' => 'Gaji penanggung jawab: Budi Santoso (03/2025)',   'kategori' => 'gaji',        'jumlah' => 2500000, 'bulan' => '03', 'tahun' => '2025', 'pj_id' => 1, 'maintenance_id' => null, 'created_at' => '2025-03-31 10:00:00', 'updated_at' => '2025-03-31 10:00:00'],
            ['keterangan' => 'Gaji penanggung jawab: Hendra Wijaya (03/2025)',  'kategori' => 'gaji',        'jumlah' => 2500000, 'bulan' => '03', 'tahun' => '2025', 'pj_id' => 2, 'maintenance_id' => null, 'created_at' => '2025-03-31 10:00:00', 'updated_at' => '2025-03-31 10:00:00'],
            // Biaya maintenance otomatis
            ['keterangan' => 'Biaya maintenance: Lampu kamar mati',             'kategori' => 'maintenance', 'jumlah' => 50000,   'bulan' => '03', 'tahun' => '2025', 'pj_id' => 1, 'maintenance_id' => 1,    'created_at' => '2025-03-03 14:00:00', 'updated_at' => '2025-03-03 14:00:00'],
            ['keterangan' => 'Biaya maintenance: Kran air bocor',                'kategori' => 'maintenance', 'jumlah' => 75000,   'bulan' => '03', 'tahun' => '2025', 'pj_id' => 2, 'maintenance_id' => 2,    'created_at' => '2025-03-06 11:00:00', 'updated_at' => '2025-03-06 11:00:00'],
            // Pengeluaran lainnya bulan 03
            ['keterangan' => 'Bayar tagihan listrik',                           'kategori' => 'lainnya',     'jumlah' => 500000,  'bulan' => '03', 'tahun' => '2025', 'pj_id' => null, 'maintenance_id' => null, 'created_at' => '2025-03-15 09:00:00', 'updated_at' => '2025-03-15 09:00:00'],
            ['keterangan' => 'Bayar tagihan air PDAM',                          'kategori' => 'lainnya',     'jumlah' => 200000,  'bulan' => '03', 'tahun' => '2025', 'pj_id' => null, 'maintenance_id' => null, 'created_at' => '2025-03-15 09:00:00', 'updated_at' => '2025-03-15 09:00:00'],
            // Gaji PJ bulan 04
            ['keterangan' => 'Gaji penanggung jawab: Budi Santoso (04/2025)',   'kategori' => 'gaji',        'jumlah' => 2500000, 'bulan' => '04', 'tahun' => '2025', 'pj_id' => 1, 'maintenance_id' => null, 'created_at' => '2025-04-30 10:00:00', 'updated_at' => '2025-04-30 10:00:00'],
            ['keterangan' => 'Gaji penanggung jawab: Hendra Wijaya (04/2025)',  'kategori' => 'gaji',        'jumlah' => 2500000, 'bulan' => '04', 'tahun' => '2025', 'pj_id' => 2, 'maintenance_id' => null, 'created_at' => '2025-04-30 10:00:00', 'updated_at' => '2025-04-30 10:00:00'],
            // Biaya maintenance bulan 04
            ['keterangan' => 'Biaya maintenance: AC tidak dingin',              'kategori' => 'maintenance', 'jumlah' => 200000,  'bulan' => '04', 'tahun' => '2025', 'pj_id' => 1, 'maintenance_id' => 3,    'created_at' => '2025-04-03 15:00:00', 'updated_at' => '2025-04-03 15:00:00'],
            ['keterangan' => 'Biaya maintenance: Pintu kamar tidak bisa dikunci', 'kategori' => 'maintenance', 'jumlah' => 100000,  'bulan' => '04', 'tahun' => '2025', 'pj_id' => 2, 'maintenance_id' => 4,    'created_at' => '2025-04-11 10:00:00', 'updated_at' => '2025-04-11 10:00:00'],
            ['keterangan' => 'Biaya maintenance: Stop kontak rusak',            'kategori' => 'maintenance', 'jumlah' => 80000,   'bulan' => '04', 'tahun' => '2025', 'pj_id' => 1, 'maintenance_id' => 5,    'created_at' => '2025-04-16 13:00:00', 'updated_at' => '2025-04-16 13:00:00'],
            // Pengeluaran lainnya bulan 04
            ['keterangan' => 'Bayar tagihan listrik',                           'kategori' => 'lainnya',     'jumlah' => 520000,  'bulan' => '04', 'tahun' => '2025', 'pj_id' => null, 'maintenance_id' => null, 'created_at' => '2025-04-15 09:00:00', 'updated_at' => '2025-04-15 09:00:00'],
            ['keterangan' => 'Bayar tagihan air PDAM',                          'kategori' => 'lainnya',     'jumlah' => 210000,  'bulan' => '04', 'tahun' => '2025', 'pj_id' => null, 'maintenance_id' => null, 'created_at' => '2025-04-15 09:00:00', 'updated_at' => '2025-04-15 09:00:00'],
            // Pengeluaran bulan 05
            ['keterangan' => 'Bayar tagihan listrik',                           'kategori' => 'lainnya',     'jumlah' => 510000,  'bulan' => '05', 'tahun' => '2025', 'pj_id' => null, 'maintenance_id' => null, 'created_at' => '2025-05-15 09:00:00', 'updated_at' => '2025-05-15 09:00:00'],
            ['keterangan' => 'Bayar tagihan air PDAM',                          'kategori' => 'lainnya',     'jumlah' => 195000,  'bulan' => '05', 'tahun' => '2025', 'pj_id' => null, 'maintenance_id' => null, 'created_at' => '2025-05-15 09:00:00', 'updated_at' => '2025-05-15 09:00:00'],
        ];

        $this->db->table('pengeluaran')->insertBatch($data);
    }
}
