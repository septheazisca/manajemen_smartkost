<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTagihanTable extends Migration
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
            ],
            'bulan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'tahun' => [
                'type'       => 'YEAR',
                'null'       => true,
            ],
            'jumlah' => [
                'type' => 'INT',
            ],
            'nominal_unik' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'menunggu_konfirmasi', 'lunas', 'menunggak'],
                'default'    => 'pending',
            ],
            'jatuh_tempo' => [
                'type' => 'DATE',
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
        $this->forge->addForeignKey('penyewa_id', 'penyewa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tagihan');
    }

    public function down()
    {
        $this->forge->dropTable('tagihan');
    }
}
