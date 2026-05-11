<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <i class="bi bi-house"></i> Dashboard
</div>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #f0e6ff;">
                <i class="bi bi-door-open" style="color: #C484F5;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Kamar</div>
                <div class="stat-value"><?= $total_kamar ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e6f4ff;">
                <i class="bi bi-people" style="color: #3b82f6;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Penyewa</div>
                <div class="stat-value"><?= $total_penyewa ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff7e6;">
                <i class="bi bi-receipt" style="color: #f59e0b;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Tagihan Pending</div>
                <div class="stat-value"><?= $tagihan_pending ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff0f0;">
                <i class="bi bi-tools" style="color: #ef4444;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Maintenance</div>
                <div class="stat-value"><?= $maintenance_pending ?></div>
            </div>
        </div>
    </div>

</div>

<!-- ROW 2 -->
<div class="row g-3 mb-4">

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e6fff4;">
                <i class="bi bi-check-circle" style="color: #10b981;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Kamar Terisi</div>
                <div class="stat-value"><?= $kamar_terisi ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #f0e6ff;">
                <i class="bi bi-door-closed" style="color: #C484F5;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Kamar Kosong</div>
                <div class="stat-value"><?= $kamar_kosong ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff0f0;">
                <i class="bi bi-exclamation-triangle" style="color: #ef4444;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Menunggak</div>
                <div class="stat-value"><?= $tagihan_menunggak ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e6fff4;">
                <i class="bi bi-cash-stack" style="color: #10b981;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Pemasukan Bulan Ini</div>
                <div class="stat-value" style="font-size: 16px;">
                    Rp <?= number_format($pemasukan_bulan_ini, 0, ',', '.') ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ROW BOTTOM -->
<div class="row g-3">

    <!-- TAGIHAN MENUNGGU KONFIRMASI -->
    <div class="col-md-6">
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">Menunggu Konfirmasi</div>
                    <div class="table-card-sub">Pembayaran yang perlu diverifikasi</div>
                </div>
                <a href="/admin/tagihan" class="btn-add" style="font-size: 12px; padding: 6px 12px;">
                    Lihat Semua
                </a>
            </div>

            <div class="tbl-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Penyewa</th>
                            <th>Kamar</th>
                            <th>Bulan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pembayaran_pending)) : ?>
                            <?php foreach ($pembayaran_pending as $p) : ?>
                                <tr>
                                    <td><?= esc($p['name']) ?></td>
                                    <td>Kamar <?= esc($p['nomor_kamar']) ?></td>
                                    <td><?= esc($p['bulan']) ?>/<?= esc($p['tahun']) ?></td>
                                    <td>
                                        <a href="/admin/tagihan/<?= $p['tagihan_id'] ?>"
                                            class="action-btn edit">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    Tidak ada pembayaran pending
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MAINTENANCE TERBARU -->
    <div class="col-md-6">
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">Laporan Maintenance</div>
                    <div class="table-card-sub">Komplain terbaru dari penyewa</div>
                </div>
                <a href="/admin/maintenance" class="btn-add" style="font-size: 12px; padding: 6px 12px;">
                    Lihat Semua
                </a>
            </div>

            <div class="tbl-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Penyewa</th>
                            <th>Kamar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($maintenance_terbaru)) : ?>
                            <?php foreach ($maintenance_terbaru as $m) : ?>
                                <tr>
                                    <td><?= esc($m['nama_penyewa']) ?></td>
                                    <td>Kamar <?= esc($m['nomor_kamar']) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = match ($m['status']) {
                                            'menunggu' => 'bg-warning text-dark',
                                            'proses'   => 'bg-info text-dark',
                                            'selesai'  => 'bg-success',
                                            default    => 'bg-secondary',
                                        };
                                        ?>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= ucfirst($m['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/admin/maintenance/<?= $m['id'] ?>"
                                            class="action-btn edit">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    Tidak ada laporan maintenance
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>