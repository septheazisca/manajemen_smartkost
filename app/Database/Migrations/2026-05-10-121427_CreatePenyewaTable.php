<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenyewaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
            ],
            'kamar_id' => [
                'type' => 'INT',
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'asal_kota' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'status_pekerjaan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'status_pernikahan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'nomor_darurat' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('penyewa');
    }

    public function down()
    {
        $this->forge->dropTable('penyewa');
    }
}
