<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PenyewaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['user_id' => 4,  'kamar_id' => 1,  'tanggal_masuk' => '2024-01-01', 'tanggal_keluar' => null, 'alamat' => 'Jl. Mawar No. 1, Jakarta',   'asal_kota' => 'Jakarta',   'status_pekerjaan_id' => 2, 'status_pernikahan_id' => 1, 'nomor_darurat' => '081100001001', 'rating' => null, 'testimoni' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 5,  'kamar_id' => 2,  'tanggal_masuk' => '2024-02-01', 'tanggal_keluar' => null, 'alamat' => 'Jl. Melati No. 2, Bandung',   'asal_kota' => 'Bandung',   'status_pekerjaan_id' => 1, 'status_pernikahan_id' => 1, 'nomor_darurat' => '081100001002', 'rating' => 5, 'testimoni' => 'Kamarnya bersih banget dan fasilitas lengkap. WiFi kenceng, AC dingin, dan lokasinya super strategis dekat kampus. Paling suka suasananya yang tenang buat belajar.', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 6,  'kamar_id' => 3,  'tanggal_masuk' => '2024-01-15', 'tanggal_keluar' => null, 'alamat' => 'Jl. Anggrek No. 3, Surabaya',  'asal_kota' => 'Surabaya',  'status_pekerjaan_id' => 2, 'status_pernikahan_id' => 1, 'nomor_darurat' => '081100001003', 'rating' => 5, 'testimoni' => 'Pindah ke SmartKost adalah keputusan terbaik. Pemiliknya ramah, responsif, dan pengurusannya serba digital. Pembayaran lewat app, laporan kerusakan juga gampang.', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 7,  'kamar_id' => 5,  'tanggal_masuk' => '2024-03-01', 'tanggal_keluar' => null, 'alamat' => 'Jl. Kenanga No. 4, Yogyakarta', 'asal_kota' => 'Yogyakarta', 'status_pekerjaan_id' => 1, 'status_pernikahan_id' => 2, 'nomor_darurat' => '081100001004', 'rating' => 5, 'testimoni' => 'Harga terjangkau tapi kualitas premium! Kamar Premium Suite worth it banget. Ada area bersama yang nyaman juga buat kerja dari kost. Highly recommended!', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 8,  'kamar_id' => 6,  'tanggal_masuk' => '2024-02-15', 'tanggal_keluar' => null, 'alamat' => 'Jl. Dahlia No. 5, Semarang',   'asal_kota' => 'Semarang',  'status_pekerjaan_id' => 2, 'status_pernikahan_id' => 1, 'nomor_darurat' => '081100001005', 'rating' => null, 'testimoni' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 9,  'kamar_id' => 7,  'tanggal_masuk' => '2024-04-01', 'tanggal_keluar' => null, 'alamat' => 'Jl. Flamboyan No. 6, Medan',   'asal_kota' => 'Medan',     'status_pekerjaan_id' => 1, 'status_pernikahan_id' => 1, 'nomor_darurat' => '081100001006', 'rating' => null, 'testimoni' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 10, 'kamar_id' => 9,  'tanggal_masuk' => '2024-01-01', 'tanggal_keluar' => null, 'alamat' => 'Jl. Bougenville No. 7, Depok',  'asal_kota' => 'Depok',     'status_pekerjaan_id' => 2, 'status_pernikahan_id' => 1, 'nomor_darurat' => '081100001007', 'rating' => null, 'testimoni' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 11, 'kamar_id' => 10, 'tanggal_masuk' => '2024-03-15', 'tanggal_keluar' => null, 'alamat' => 'Jl. Cempaka No. 8, Bekasi',    'asal_kota' => 'Bekasi',    'status_pekerjaan_id' => 1, 'status_pernikahan_id' => 1, 'nomor_darurat' => '081100001008', 'rating' => null, 'testimoni' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['user_id' => 12, 'kamar_id' => 11, 'tanggal_masuk' => '2024-05-01', 'tanggal_keluar' => null, 'alamat' => 'Jl. Teratai No. 9, Tangerang',  'asal_kota' => 'Tangerang', 'status_pekerjaan_id' => 2, 'status_pernikahan_id' => 1, 'nomor_darurat' => '081100001009', 'rating' => null, 'testimoni' => null, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('penyewa')->insertBatch($data);
    }
}