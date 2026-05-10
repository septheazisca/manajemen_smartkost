<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenyewaTable extends Migration
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
            ],
            'kamar_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'tanggal_masuk' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_keluar' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'asal_kota' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status_pekerjaan' => [
                'type'       => 'ENUM',
                'constraint' => ['bekerja', 'pelajar/mahasiswa', 'lainnya'],
                'null'       => true,
            ],
            'status_pernikahan' => [
                'type'       => 'ENUM',
                'constraint' => ['belum menikah', 'menikah', 'lainnya'],
                'null'       => true,
            ],
            'nomor_darurat' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kamar_id', 'kamar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('penyewa');
    }

    public function down()
    {
        $this->forge->dropTable('penyewa');
    }
}
