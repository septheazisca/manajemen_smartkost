<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKamarFasilitasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'kamar_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'fasilitas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kamar_id', 'kamar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('fasilitas_id', 'fasilitas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kamar_fasilitas');
    }

    public function down()
    {
        $this->forge->dropTable('kamar_fasilitas');
    }
}
