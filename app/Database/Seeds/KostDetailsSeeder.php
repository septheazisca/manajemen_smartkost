<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KostDetailsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'id'             => 1,
            'detail_kost'    => 'Hunian modern dan nyaman yang didesain khusus untuk mendukung produktivitas mahasiswa serta profesional muda. Nikmati hidup bebas ribet dengan fasilitas super lengkap, lingkungan yang kondusif, dan harga sewa bersahabat yang pas di kantong generasi muda.',
            'link_instagram' => 'https://instagram.com/smartkost',
            'link_tiktok'    => 'https://tiktok.com/@smartkost',
            'link_twitter'   => 'https://twitter.com/smartkost',
            'link_whatsapp'  => 'https://wa.me/6281234567890',
            'alamat'         => 'Jl. Margonda Raya No. 42, Depok, Jawa Barat',
            'no_telepon'     => '+62 812-3456-7890',
            'email'          => 'halo@smartkost.id',
            'jam_operasi'    => "Senin – Sabtu: 08.00 – 20.00 WIB\nMinggu: 09.00 – 17.00 WIB",
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        // Gunakan replace agar record ID 1 selalu disisipkan atau di-overwrite (tidak duplikat) jika seeder dijalankan ulang
        $this->db->table('kost_details')->replace($data);
    }
}
