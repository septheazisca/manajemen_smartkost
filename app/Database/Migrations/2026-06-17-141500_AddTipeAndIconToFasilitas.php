<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipeAndIconToFasilitas extends Migration
{
    public function up()
    {
        $fields = [
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['kamar', 'bersama'],
                'default'    => 'kamar',
                'after'      => 'nama_fasilitas',
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'tipe',
            ],
        ];
        $this->forge->addColumn('fasilitas', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('fasilitas', ['tipe', 'icon']);
    }
}
