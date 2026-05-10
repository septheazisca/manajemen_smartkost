<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMaintenanceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'penyewa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'kamar_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'pj_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'biaya' => [
                'type'    => 'INT',
                'null'    => true,
            ],
            'catatan_pj' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu', 'proses', 'selesai'],
                'default'    => 'menunggu',
            ],
            'assigned_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'selesai_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kamar_id', 'kamar', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('maintenance');
    }

    public function down()
    {
        $this->forge->dropTable('maintenance');
    }
}
