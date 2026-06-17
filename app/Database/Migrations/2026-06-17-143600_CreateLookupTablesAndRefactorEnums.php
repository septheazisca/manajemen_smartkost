<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLookupTablesAndRefactorEnums extends Migration
{
    public function up()
    {
        // ==========================================
        // 1. CREATE LOOKUP TABLES
        // ==========================================

        // Status Kamar
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nama_status' => ['type' => 'VARCHAR', 'constraint' => 50],
            'badge_class' => ['type' => 'VARCHAR', 'constraint' => 100],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('status_kamar', true);

        // Status Tagihan
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nama_status' => ['type' => 'VARCHAR', 'constraint' => 50],
            'badge_class' => ['type' => 'VARCHAR', 'constraint' => 100],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('status_tagihan', true);

        // Status Pembayaran
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nama_status' => ['type' => 'VARCHAR', 'constraint' => 50],
            'badge_class' => ['type' => 'VARCHAR', 'constraint' => 100],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('status_pembayaran', true);

        // Status Maintenance
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nama_status' => ['type' => 'VARCHAR', 'constraint' => 50],
            'badge_class' => ['type' => 'VARCHAR', 'constraint' => 100],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('status_maintenance', true);

        // Kategori Pengeluaran
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nama_kategori' => ['type' => 'VARCHAR', 'constraint' => 50],
            'badge_class' => ['type' => 'VARCHAR', 'constraint' => 100],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('kategori_pengeluaran', true);

        // Status Pekerjaan
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nama_status' => ['type' => 'VARCHAR', 'constraint' => 50],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('status_pekerjaan', true);

        // Status Pernikahan
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nama_status' => ['type' => 'VARCHAR', 'constraint' => 50],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('status_pernikahan', true);

        // ==========================================
        // 2. SEED INITIAL DATA
        // ==========================================

        $this->db->table('status_kamar')->insertBatch([
            ['id' => 1, 'nama_status' => 'kosong', 'badge_class' => 'bg-danger-subtle text-danger border-danger px-2 small', 'icon' => 'bi-door-closed'],
            ['id' => 2, 'nama_status' => 'terisi', 'badge_class' => 'bg-success-subtle text-success border-success px-2 small', 'icon' => 'bi-door-open'],
            ['id' => 3, 'nama_status' => 'nonaktif', 'badge_class' => 'bg-secondary-subtle text-secondary border-secondary px-2 small', 'icon' => 'bi-dash-circle'],
        ]);

        $this->db->table('status_tagihan')->insertBatch([
            ['id' => 1, 'nama_status' => 'pending', 'badge_class' => 'bg-warning-subtle text-warning border border-warning px-2 small', 'icon' => 'bi-hourglass-split'],
            ['id' => 2, 'nama_status' => 'menunggu_konfirmasi', 'badge_class' => 'bg-info-subtle text-info border border-info px-2 small', 'icon' => 'bi-clock-history'],
            ['id' => 3, 'nama_status' => 'lunas', 'badge_class' => 'bg-success-subtle text-success border border-success px-2 small', 'icon' => 'bi-check-circle-fill'],
            ['id' => 4, 'nama_status' => 'menunggak', 'badge_class' => 'bg-danger-subtle text-danger border border-danger px-2 small', 'icon' => 'bi-exclamation-triangle-fill'],
        ]);

        $this->db->table('status_pembayaran')->insertBatch([
            ['id' => 1, 'nama_status' => 'pending', 'badge_class' => 'bg-warning-subtle text-warning border-warning', 'icon' => 'bi-hourglass-split'],
            ['id' => 2, 'nama_status' => 'approved', 'badge_class' => 'bg-success-subtle text-success border-success', 'icon' => 'bi-check-circle-fill'],
            ['id' => 3, 'nama_status' => 'ditolak', 'badge_class' => 'bg-danger-subtle text-danger border-danger', 'icon' => 'bi-x-circle-fill'],
        ]);

        $this->db->table('status_maintenance')->insertBatch([
            ['id' => 1, 'nama_status' => 'menunggu', 'badge_class' => 'danger', 'icon' => 'bi-hourglass'],
            ['id' => 2, 'nama_status' => 'proses', 'badge_class' => 'primary', 'icon' => 'bi-gear-wide-connected'],
            ['id' => 3, 'nama_status' => 'selesai', 'badge_class' => 'success', 'icon' => 'bi-check-circle-fill'],
        ]);

        $this->db->table('kategori_pengeluaran')->insertBatch([
            ['id' => 1, 'nama_kategori' => 'maintenance', 'badge_class' => 'warning', 'icon' => 'bi-tools'],
            ['id' => 2, 'nama_kategori' => 'gaji', 'badge_class' => 'success', 'icon' => 'bi-cash'],
            ['id' => 3, 'nama_kategori' => 'lainnya', 'badge_class' => 'secondary', 'icon' => 'bi-three-dots'],
        ]);

        $this->db->table('status_pekerjaan')->insertBatch([
            ['id' => 1, 'nama_status' => 'bekerja'],
            ['id' => 2, 'nama_status' => 'pelajar/mahasiswa'],
            ['id' => 3, 'nama_status' => 'lainnya'],
        ]);

        $this->db->table('status_pernikahan')->insertBatch([
            ['id' => 1, 'nama_status' => 'belum menikah'],
            ['id' => 2, 'nama_status' => 'menikah'],
            ['id' => 3, 'nama_status' => 'lainnya'],
        ]);

        // ==========================================
        // 3. REFACTOR EXISTING COLUMNS TO FK ID
        // ==========================================

        // --- KAMAR ---
        $this->forge->addColumn('kamar', [
            'status_kamar_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'status']
        ]);
        $this->db->query("UPDATE kamar SET status_kamar_id = 1 WHERE status = 'kosong'");
        $this->db->query("UPDATE kamar SET status_kamar_id = 2 WHERE status = 'terisi'");
        $this->db->query("UPDATE kamar SET status_kamar_id = 3 WHERE status = 'nonaktif'");
        $this->forge->dropColumn('kamar', 'status');
        $this->forge->modifyColumn('kamar', [
            'status_kamar_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false]
        ]);
        $this->db->query("ALTER TABLE kamar ADD CONSTRAINT fk_kamar_status FOREIGN KEY (status_kamar_id) REFERENCES status_kamar(id) ON DELETE RESTRICT ON UPDATE CASCADE");

        // --- TAGIHAN ---
        $this->forge->addColumn('tagihan', [
            'status_tagihan_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'status']
        ]);
        $this->db->query("UPDATE tagihan SET status_tagihan_id = 1 WHERE status = 'pending'");
        $this->db->query("UPDATE tagihan SET status_tagihan_id = 2 WHERE status = 'menunggu_konfirmasi'");
        $this->db->query("UPDATE tagihan SET status_tagihan_id = 3 WHERE status = 'lunas'");
        $this->db->query("UPDATE tagihan SET status_tagihan_id = 4 WHERE status = 'menunggak'");
        $this->forge->dropColumn('tagihan', 'status');
        $this->forge->modifyColumn('tagihan', [
            'status_tagihan_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false]
        ]);
        $this->db->query("ALTER TABLE tagihan ADD CONSTRAINT fk_tagihan_status FOREIGN KEY (status_tagihan_id) REFERENCES status_tagihan(id) ON DELETE RESTRICT ON UPDATE CASCADE");

        // --- PEMBAYARAN ---
        $this->forge->addColumn('pembayaran', [
            'status_pembayaran_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'status']
        ]);
        $this->db->query("UPDATE pembayaran SET status_pembayaran_id = 1 WHERE status = 'pending'");
        $this->db->query("UPDATE pembayaran SET status_pembayaran_id = 2 WHERE status = 'approved'");
        $this->db->query("UPDATE pembayaran SET status_pembayaran_id = 3 WHERE status = 'ditolak'");
        $this->forge->dropColumn('pembayaran', 'status');
        $this->forge->modifyColumn('pembayaran', [
            'status_pembayaran_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false]
        ]);
        $this->db->query("ALTER TABLE pembayaran ADD CONSTRAINT fk_pembayaran_status FOREIGN KEY (status_pembayaran_id) REFERENCES status_pembayaran(id) ON DELETE RESTRICT ON UPDATE CASCADE");

        // --- MAINTENANCE ---
        $this->forge->addColumn('maintenance', [
            'status_maintenance_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'status']
        ]);
        $this->db->query("UPDATE maintenance SET status_maintenance_id = 1 WHERE status = 'menunggu'");
        $this->db->query("UPDATE maintenance SET status_maintenance_id = 2 WHERE status = 'proses'");
        $this->db->query("UPDATE maintenance SET status_maintenance_id = 3 WHERE status = 'selesai'");
        $this->forge->dropColumn('maintenance', 'status');
        $this->forge->modifyColumn('maintenance', [
            'status_maintenance_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false]
        ]);
        $this->db->query("ALTER TABLE maintenance ADD CONSTRAINT fk_maintenance_status FOREIGN KEY (status_maintenance_id) REFERENCES status_maintenance(id) ON DELETE RESTRICT ON UPDATE CASCADE");

        // --- PENGELUARAN ---
        $this->forge->addColumn('pengeluaran', [
            'kategori_pengeluaran_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'kategori']
        ]);
        $this->db->query("UPDATE pengeluaran SET kategori_pengeluaran_id = 1 WHERE kategori = 'maintenance'");
        $this->db->query("UPDATE pengeluaran SET kategori_pengeluaran_id = 2 WHERE kategori = 'gaji'");
        $this->db->query("UPDATE pengeluaran SET kategori_pengeluaran_id = 3 WHERE kategori = 'lainnya'");
        $this->forge->dropColumn('pengeluaran', 'kategori');
        $this->forge->modifyColumn('pengeluaran', [
            'kategori_pengeluaran_id' => ['type' => 'INT', 'constraint' => 11, 'null' => false]
        ]);
        $this->db->query("ALTER TABLE pengeluaran ADD CONSTRAINT fk_pengeluaran_kategori FOREIGN KEY (kategori_pengeluaran_id) REFERENCES kategori_pengeluaran(id) ON DELETE RESTRICT ON UPDATE CASCADE");

        // --- PENYEWA ---
        $this->forge->addColumn('penyewa', [
            'status_pekerjaan_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'status_pekerjaan'],
            'status_pernikahan_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'status_pernikahan'],
        ]);
        $this->db->query("UPDATE penyewa SET status_pekerjaan_id = 1 WHERE status_pekerjaan = 'bekerja'");
        $this->db->query("UPDATE penyewa SET status_pekerjaan_id = 2 WHERE status_pekerjaan = 'pelajar/mahasiswa'");
        $this->db->query("UPDATE penyewa SET status_pekerjaan_id = 3 WHERE status_pekerjaan = 'lainnya'");
        
        $this->db->query("UPDATE penyewa SET status_pernikahan_id = 1 WHERE status_pernikahan = 'belum menikah'");
        $this->db->query("UPDATE penyewa SET status_pernikahan_id = 2 WHERE status_pernikahan = 'menikah'");
        $this->db->query("UPDATE penyewa SET status_pernikahan_id = 3 WHERE status_pernikahan = 'lainnya'");

        $this->forge->dropColumn('penyewa', 'status_pekerjaan');
        $this->forge->dropColumn('penyewa', 'status_pernikahan');

        $this->db->query("ALTER TABLE penyewa ADD CONSTRAINT fk_penyewa_pekerjaan FOREIGN KEY (status_pekerjaan_id) REFERENCES status_pekerjaan(id) ON DELETE RESTRICT ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE penyewa ADD CONSTRAINT fk_penyewa_pernikahan FOREIGN KEY (status_pernikahan_id) REFERENCES status_pernikahan(id) ON DELETE RESTRICT ON UPDATE CASCADE");
    }

    public function down()
    {
        // 1. Drop Foreign Keys
        $this->db->query("ALTER TABLE kamar DROP FOREIGN KEY fk_kamar_status");
        $this->db->query("ALTER TABLE tagihan DROP FOREIGN KEY fk_tagihan_status");
        $this->db->query("ALTER TABLE pembayaran DROP FOREIGN KEY fk_pembayaran_status");
        $this->db->query("ALTER TABLE maintenance DROP FOREIGN KEY fk_maintenance_status");
        $this->db->query("ALTER TABLE pengeluaran DROP FOREIGN KEY fk_pengeluaran_kategori");
        $this->db->query("ALTER TABLE penyewa DROP FOREIGN KEY fk_penyewa_pekerjaan");
        $this->db->query("ALTER TABLE penyewa DROP FOREIGN KEY fk_penyewa_pernikahan");

        // 2. Re-create ENUM columns
        // Kamar
        $this->forge->addColumn('kamar', [
            'status' => ['type' => 'ENUM', 'constraint' => ['kosong', 'terisi', 'nonaktif'], 'default' => 'kosong', 'after' => 'status_kamar_id']
        ]);
        $this->db->query("UPDATE kamar SET status = 'kosong' WHERE status_kamar_id = 1");
        $this->db->query("UPDATE kamar SET status = 'terisi' WHERE status_kamar_id = 2");
        $this->db->query("UPDATE kamar SET status = 'nonaktif' WHERE status_kamar_id = 3");
        $this->forge->dropColumn('kamar', 'status_kamar_id');

        // Tagihan
        $this->forge->addColumn('tagihan', [
            'status' => ['type' => 'ENUM', 'constraint' => ['pending', 'menunggu_konfirmasi', 'lunas', 'menunggak'], 'default' => 'pending', 'after' => 'status_tagihan_id']
        ]);
        $this->db->query("UPDATE tagihan SET status = 'pending' WHERE status_tagihan_id = 1");
        $this->db->query("UPDATE tagihan SET status = 'menunggu_konfirmasi' WHERE status_tagihan_id = 2");
        $this->db->query("UPDATE tagihan SET status = 'lunas' WHERE status_tagihan_id = 3");
        $this->db->query("UPDATE tagihan SET status = 'menunggak' WHERE status_tagihan_id = 4");
        $this->forge->dropColumn('tagihan', 'status_tagihan_id');

        // Pembayaran
        $this->forge->addColumn('pembayaran', [
            'status' => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'ditolak'], 'default' => 'pending', 'after' => 'status_pembayaran_id']
        ]);
        $this->db->query("UPDATE pembayaran SET status = 'pending' WHERE status_pembayaran_id = 1");
        $this->db->query("UPDATE pembayaran SET status = 'approved' WHERE status_pembayaran_id = 2");
        $this->db->query("UPDATE pembayaran SET status = 'ditolak' WHERE status_pembayaran_id = 3");
        $this->forge->dropColumn('pembayaran', 'status_pembayaran_id');

        // Maintenance
        $this->forge->addColumn('maintenance', [
            'status' => ['type' => 'ENUM', 'constraint' => ['menunggu', 'proses', 'selesai'], 'default' => 'menunggu', 'after' => 'status_maintenance_id']
        ]);
        $this->db->query("UPDATE maintenance SET status = 'menunggu' WHERE status_maintenance_id = 1");
        $this->db->query("UPDATE maintenance SET status = 'proses' WHERE status_maintenance_id = 2");
        $this->db->query("UPDATE maintenance SET status = 'selesai' WHERE status_maintenance_id = 3");
        $this->forge->dropColumn('maintenance', 'status_maintenance_id');

        // Pengeluaran
        $this->forge->addColumn('pengeluaran', [
            'kategori' => ['type' => 'ENUM', 'constraint' => ['maintenance', 'gaji', 'lainnya'], 'default' => 'lainnya', 'after' => 'kategori_pengeluaran_id']
        ]);
        $this->db->query("UPDATE pengeluaran SET kategori = 'maintenance' WHERE kategori_pengeluaran_id = 1");
        $this->db->query("UPDATE pengeluaran SET kategori = 'gaji' WHERE kategori_pengeluaran_id = 2");
        $this->db->query("UPDATE pengeluaran SET kategori = 'lainnya' WHERE kategori_pengeluaran_id = 3");
        $this->forge->dropColumn('pengeluaran', 'kategori_pengeluaran_id');

        // Penyewa
        $this->forge->addColumn('penyewa', [
            'status_pekerjaan' => ['type' => 'ENUM', 'constraint' => ['bekerja', 'pelajar/mahasiswa', 'lainnya'], 'null' => true, 'after' => 'status_pekerjaan_id'],
            'status_pernikahan' => ['type' => 'ENUM', 'constraint' => ['belum menikah', 'menikah', 'lainnya'], 'null' => true, 'after' => 'status_pernikahan_id'],
        ]);
        $this->db->query("UPDATE penyewa SET status_pekerjaan = 'bekerja' WHERE status_pekerjaan_id = 1");
        $this->db->query("UPDATE penyewa SET status_pekerjaan = 'pelajar/mahasiswa' WHERE status_pekerjaan_id = 2");
        $this->db->query("UPDATE penyewa SET status_pekerjaan = 'lainnya' WHERE status_pekerjaan_id = 3");

        $this->db->query("UPDATE penyewa SET status_pernikahan = 'belum menikah' WHERE status_pernikahan_id = 1");
        $this->db->query("UPDATE penyewa SET status_pernikahan = 'menikah' WHERE status_pernikahan_id = 2");
        $this->db->query("UPDATE penyewa SET status_pernikahan = 'lainnya' WHERE status_pernikahan_id = 3");

        $this->forge->dropColumn('penyewa', 'status_pekerjaan_id');
        $this->forge->dropColumn('penyewa', 'status_pernikahan_id');

        // 3. Drop Tables
        $this->forge->dropTable('status_kamar', true);
        $this->forge->dropTable('status_tagihan', true);
        $this->forge->dropTable('status_pembayaran', true);
        $this->forge->dropTable('status_maintenance', true);
        $this->forge->dropTable('kategori_pengeluaran', true);
        $this->forge->dropTable('status_pekerjaan', true);
        $this->forge->dropTable('status_pernikahan', true);
    }
}
