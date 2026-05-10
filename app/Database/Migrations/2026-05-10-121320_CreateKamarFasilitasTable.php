<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKamarFasilitasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'kamar_id' => [
                'type' => 'INT',
            ],
            'fasilitas_id' => [
                'type' => 'INT',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('kamar_fasilitas');
    }

    public function down()
    {
        $this->forge->dropTable('kamar_fasilitas');
    }
}
