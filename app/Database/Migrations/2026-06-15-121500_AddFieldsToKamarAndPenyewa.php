<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToKamarAndPenyewa extends Migration
{
    public function up()
    {
        // Menambahkan kolom foto dan tipe ke tabel kamar
        $this->forge->addColumn('kamar', [
            'tipe' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'nomor_kamar'
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'deskripsi'
            ]
        ]);

        // Menambahkan kolom rating dan testimoni ke tabel penyewa
        $this->forge->addColumn('penyewa', [
            'rating' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'nomor_darurat'
            ],
            'testimoni' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'rating'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('kamar', ['foto', 'tipe']);
        $this->forge->dropColumn('penyewa', ['rating', 'testimoni']);
    }
}
