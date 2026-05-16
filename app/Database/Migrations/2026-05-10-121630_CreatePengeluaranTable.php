<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengeluaranTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'kategori' => [
                'type'       => 'ENUM',
                'constraint' => ['maintenance', 'gaji', 'lainnya'],
                'default'    => 'lainnya',
            ],
            'jumlah' => [
                'type' => 'INT',
            ],
            'bulan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'tahun' => [
                'type' => 'YEAR',
                'null' => true,
            ],
            'pj_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'maintenance_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
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
        $this->forge->addForeignKey('pj_id', 'penanggung_jawab', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('maintenance_id', 'maintenance', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('pengeluaran');
    }

    public function down()
    {
        $this->forge->dropTable('pengeluaran');
    }
}
