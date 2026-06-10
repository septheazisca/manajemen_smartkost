<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MaintenanceSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['penyewa_id' => 1, 'kamar_id' => 1,  'pj_id' => 1,    'deskripsi' => 'Lampu kamar mati',           'foto' => null, 'biaya' => 50000,  'catatan_pj' => 'Lampu sudah diganti baru',      'status' => 'selesai',  'assigned_at' => '2025-03-02 09:00:00', 'selesai_at' => '2025-03-03 14:00:00', 'created_at' => '2025-03-01 08:00:00', 'updated_at' => '2025-03-03 14:00:00'],
            ['penyewa_id' => 2, 'kamar_id' => 2,  'pj_id' => 2,    'deskripsi' => 'Kran air bocor',              'foto' => null, 'biaya' => 75000,  'catatan_pj' => 'Kran sudah diganti',            'status' => 'selesai',  'assigned_at' => '2025-03-05 09:00:00', 'selesai_at' => '2025-03-06 11:00:00', 'created_at' => '2025-03-04 10:00:00', 'updated_at' => '2025-03-06 11:00:00'],
            ['penyewa_id' => 3, 'kamar_id' => 3,  'pj_id' => 1,    'deskripsi' => 'AC tidak dingin',             'foto' => null, 'biaya' => 200000, 'catatan_pj' => 'AC sudah di-service dan diisi freon', 'status' => 'selesai', 'assigned_at' => '2025-04-02 09:00:00', 'selesai_at' => '2025-04-03 15:00:00', 'created_at' => '2025-04-01 09:00:00', 'updated_at' => '2025-04-03 15:00:00'],
            ['penyewa_id' => 4, 'kamar_id' => 5,  'pj_id' => 2,    'deskripsi' => 'Pintu kamar tidak bisa dikunci', 'foto' => null, 'biaya' => 100000, 'catatan_pj' => 'Kunci sudah diganti',         'status' => 'selesai',  'assigned_at' => '2025-04-10 09:00:00', 'selesai_at' => '2025-04-11 10:00:00', 'created_at' => '2025-04-09 08:00:00', 'updated_at' => '2025-04-11 10:00:00'],
            ['penyewa_id' => 5, 'kamar_id' => 6,  'pj_id' => 1,    'deskripsi' => 'Stop kontak rusak',           'foto' => null, 'biaya' => 80000,  'catatan_pj' => 'Stop kontak sudah diganti',     'status' => 'selesai',  'assigned_at' => '2025-04-15 09:00:00', 'selesai_at' => '2025-04-16 13:00:00', 'created_at' => '2025-04-14 10:00:00', 'updated_at' => '2025-04-16 13:00:00'],
            ['penyewa_id' => 6, 'kamar_id' => 7,  'pj_id' => null, 'deskripsi' => 'Jendela tidak bisa ditutup',  'foto' => null, 'biaya' => null,   'catatan_pj' => null,                            'status' => 'menunggu', 'assigned_at' => null,                  'selesai_at' => null,                  'created_at' => '2025-05-01 08:00:00', 'updated_at' => '2025-05-01 08:00:00'],
            ['penyewa_id' => 7, 'kamar_id' => 9,  'pj_id' => 1,    'deskripsi' => 'Shower mampet',               'foto' => null, 'biaya' => null,   'catatan_pj' => null,                            'status' => 'proses',   'assigned_at' => '2025-05-03 09:00:00', 'selesai_at' => null,                  'created_at' => '2025-05-02 09:00:00', 'updated_at' => '2025-05-03 09:00:00'],
            ['penyewa_id' => 8, 'kamar_id' => 10, 'pj_id' => 2,    'deskripsi' => 'Kipas angin mati',            'foto' => null, 'biaya' => null,   'catatan_pj' => null,                            'status' => 'proses',   'assigned_at' => '2025-05-04 09:00:00', 'selesai_at' => null,                  'created_at' => '2025-05-03 10:00:00', 'updated_at' => '2025-05-04 09:00:00'],
            ['penyewa_id' => 9, 'kamar_id' => 11, 'pj_id' => null, 'deskripsi' => 'Tembok retak',                'foto' => null, 'biaya' => null,   'catatan_pj' => null,                            'status' => 'menunggu', 'assigned_at' => null,                  'selesai_at' => null,                  'created_at' => '2025-05-05 08:00:00', 'updated_at' => '2025-05-05 08:00:00'],
            ['penyewa_id' => 1, 'kamar_id' => 1,  'pj_id' => null, 'deskripsi' => 'Saluran air kamar mandi tersumbat', 'foto' => null, 'biaya' => null, 'catatan_pj' => null,                       'status' => 'menunggu', 'assigned_at' => null,                  'selesai_at' => null,                  'created_at' => '2025-05-06 07:00:00', 'updated_at' => '2025-05-06 07:00:00'],
        ];

        $this->db->table('maintenance')->insertBatch($data);
    }
}
