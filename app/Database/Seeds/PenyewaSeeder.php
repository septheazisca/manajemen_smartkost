<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PenyewaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['user_id' => 4,  'kamar_id' => 1,  'tanggal_masuk' => '2024-01-01', 'tanggal_keluar' => null, 'alamat' => 'Jl. Mawar No. 1, Jakarta',   'asal_kota' => 'Jakarta',   'status_pekerjaan' => 'pelajar/mahasiswa', 'status_pernikahan' => 'belum menikah', 'nomor_darurat' => '081299990001', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 5,  'kamar_id' => 2,  'tanggal_masuk' => '2024-02-01', 'tanggal_keluar' => null, 'alamat' => 'Jl. Melati No. 2, Bandung',   'asal_kota' => 'Bandung',   'status_pekerjaan' => 'bekerja',           'status_pernikahan' => 'belum menikah', 'nomor_darurat' => '081299990002', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 6,  'kamar_id' => 3,  'tanggal_masuk' => '2024-01-15', 'tanggal_keluar' => null, 'alamat' => 'Jl. Anggrek No. 3, Surabaya',  'asal_kota' => 'Surabaya',  'status_pekerjaan' => 'pelajar/mahasiswa', 'status_pernikahan' => 'belum menikah', 'nomor_darurat' => '081299990003', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 7,  'kamar_id' => 5,  'tanggal_masuk' => '2024-03-01', 'tanggal_keluar' => null, 'alamat' => 'Jl. Kenanga No. 4, Yogyakarta', 'asal_kota' => 'Yogyakarta', 'status_pekerjaan' => 'bekerja',           'status_pernikahan' => 'menikah',       'nomor_darurat' => '081299990004', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 8,  'kamar_id' => 6,  'tanggal_masuk' => '2024-02-15', 'tanggal_keluar' => null, 'alamat' => 'Jl. Dahlia No. 5, Semarang',   'asal_kota' => 'Semarang',  'status_pekerjaan' => 'pelajar/mahasiswa', 'status_pernikahan' => 'belum menikah', 'nomor_darurat' => '081299990005', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 9,  'kamar_id' => 7,  'tanggal_masuk' => '2024-04-01', 'tanggal_keluar' => null, 'alamat' => 'Jl. Flamboyan No. 6, Medan',   'asal_kota' => 'Medan',     'status_pekerjaan' => 'bekerja',           'status_pernikahan' => 'belum menikah', 'nomor_darurat' => '081299990006', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 10, 'kamar_id' => 9,  'tanggal_masuk' => '2024-01-01', 'tanggal_keluar' => null, 'alamat' => 'Jl. Bougenville No. 7, Depok',  'asal_kota' => 'Depok',     'status_pekerjaan' => 'pelajar/mahasiswa', 'status_pernikahan' => 'belum menikah', 'nomor_darurat' => '081299990007', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 11, 'kamar_id' => 10, 'tanggal_masuk' => '2024-03-15', 'tanggal_keluar' => null, 'alamat' => 'Jl. Cempaka No. 8, Bekasi',    'asal_kota' => 'Bekasi',    'status_pekerjaan' => 'bekerja',           'status_pernikahan' => 'belum menikah', 'nomor_darurat' => '081299990008', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 12, 'kamar_id' => 11, 'tanggal_masuk' => '2024-05-01', 'tanggal_keluar' => null, 'alamat' => 'Jl. Teratai No. 9, Tangerang',  'asal_kota' => 'Tangerang', 'status_pekerjaan' => 'pelajar/mahasiswa', 'status_pernikahan' => 'belum menikah', 'nomor_darurat' => '081299990009', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('penyewa')->insertBatch($data);
    }
}
