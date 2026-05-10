<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTagihanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'penyewa_id' => [
                'type' => 'INT',
            ],
            'bulan' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'jumlah' => [
                'type' => 'INT',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'cicilan', 'lunas', 'menunggak'],
                'default' => 'pending',
            ],
            'jatuh_tempo' => [
                'type' => 'DATE',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tagihan');
    }

    public function down()
    {
        $this->forge->dropTable('tagihan');
    }
}
