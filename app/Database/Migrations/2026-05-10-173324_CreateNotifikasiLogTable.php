<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotifikasiLogTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'pesan' => [
                'type' => 'TEXT',
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['tagihan', 'tunggakan', 'info', 'custom'],
                'default'    => 'custom',
            ],
            'status_kirim' => [
                'type'       => 'ENUM',
                'constraint' => ['terkirim', 'gagal'],
                'default'    => 'terkirim',
            ],
            'response_fonnte' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('notifikasi_log');
    }

    public function down()
    {
        $this->forge->dropTable('notifikasi_log');
    }
}
