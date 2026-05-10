<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKamarTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'nomor_kamar' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'lantai' => [
                'type' => 'INT',
                'null' => true,
            ],
            'luas' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'harga' => [
                'type' => 'INT',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['kosong', 'terisi', 'nonaktif'],
                'default' => 'kosong',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('kamar');
    }

    public function down()
    {
        $this->forge->dropTable('kamar');
    }
}
