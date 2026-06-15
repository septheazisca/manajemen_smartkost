<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTampilkanTestimoniToPenyewa extends Migration
{
    public function up()
    {
        $this->forge->addColumn('penyewa', [
            'tampilkan_testimoni' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
                'after'      => 'testimoni'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('penyewa', 'tampilkan_testimoni');
    }
}
