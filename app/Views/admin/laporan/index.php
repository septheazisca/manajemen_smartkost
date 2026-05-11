<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Laporan Keuangan</span>
</div>

<!-- Header & Export Tools -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Laporan Keuangan</h4>
        <p class="text-muted small mb-0">Ringkasan arus kas masuk dan keluar sistem</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/laporan/export-pdf?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-cancel border-danger text-danger" style="text-decoration: none;">
            <i class="bi bi-file-pdf me-1"></i> PDF
        </a>
        <a href="/admin/laporan/export-excel?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-primary-custom bg-success border-success" style="text-decoration: none;">
            <i class="bi bi-file-earmark-excel me-1"></i> Excel
        </a>
    </div>
</div>

<!-- Filter Periode -->
<div class="table-card mb-4" style="padding: 1.5rem;">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-bold small text-uppercase">Bulan</label>
            <select name="bulan" class="form-select">
                <?php foreach ($list_bulan as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $bulan == $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold small text-uppercase">Tahun</label>
            <input type="number" name="tahun" class="form-control" value="<?= $tahun ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-primary-custom w-100" style="height: 38px;">
                <i class="bi bi-filter"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Main Summary (High Level) -->
<div class="row mb-4 g-3">
    <div class="col-md-4">
        <div class="table-card p-4 border-start border-success border-2 h-100">
            <p class="text-muted mb-1 small fw-bold text-uppercase">Total Pemasukan</p>
            <h3 class="fw-bold text-success mb-0">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h3>
            <i class="bi bi-arrow-up-right-circle text-success opacity-25 float-end fs-3" style="margin-top: -30px;"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card p-4 border-start border-danger border-2 h-100">
            <p class="text-muted mb-1 small fw-bold text-uppercase">Total Pengeluaran</p>
            <h3 class="fw-bold text-danger mb-0">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h3>
            <i class="bi bi-arrow-down-left-circle text-danger opacity-25 float-end fs-3" style="margin-top: -30px;"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card p-4 border-start border-primary border-2 h-100 bg-light">
            <p class="text-muted mb-1 small fw-bold text-uppercase">Saldo Bersih (Profit)</p>
            <h3 class="fw-bold <?= $saldo_bersih >= 0 ? 'text-primary' : 'text-danger' ?> mb-0">
                Rp <?= number_format($saldo_bersih, 0, ',', '.') ?>
            </h3>
            <i class="bi bi-wallet2 text-primary opacity-25 float-end fs-3" style="margin-top: -30px;"></i>
        </div>
    </div>
</div>

<!-- Sub Summary (Expense Breakdown) -->
<div class="row mb-4 g-3">
    <div class="col-md-4">
        <div class="table-card p-3 d-flex align-items-center justify-content-between">
            <span class="text-muted small fw-bold">MAINTENANCE</span>
            <span class="fw-bold text-info">Rp <?= number_format($total_maintenance, 0, ',', '.') ?></span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card p-3 d-flex align-items-center justify-content-between">
            <span class="text-muted small fw-bold">GAJI PJ</span>
            <span class="fw-bold text-warning">Rp <?= number_format($total_gaji, 0, ',', '.') ?></span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card p-3 d-flex align-items-center justify-content-between">
            <span class="text-muted small fw-bold">LAINNYA</span>
            <span class="fw-bold text-secondary">Rp <?= number_format($total_lainnya, 0, ',', '.') ?></span>
        </div>
    </div>
</div>

<!-- Detailed Tables -->
<div class="row g-4">
    <!-- Pemasukan Side -->
    <div class="col-lg-6">
        <div class="table-card">
            <div class="table-card-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="table-card-title text-success">Detail Pemasukan</div>
                    <div class="table-card-sub">Penerimaan dari tagihan kamar</div>
                </div>
                <a href="/admin/laporan/tagihan?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-cancel border p-2 py-1 small" style="font-size: 0.75rem; text-decoration: none;">
                    Lihat Semua
                </a>
            </div>
            <div class="tbl-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Penyewa</th>
                            <th>Kamar</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($detail_pemasukan)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted small">Tidak ada data pemasukan</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($detail_pemasukan as $p): ?>
                                <tr>
                                    <td><i class="bi bi-person me-1 text-muted"></i> <?= esc($p['name']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= esc($p['nomor_kamar']) ?></span></td>
                                    <td class="fw-bold text-success">Rp <?= number_format($p['jumlah'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pengeluaran Side -->
    <div class="col-lg-6">
        <div class="table-card">
            <div class="table-card-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="table-card-title text-danger">Detail Pengeluaran</div>
                    <div class="table-card-sub">Biaya operasional & maintenance</div>
                </div>
                <a href="/admin/pengeluaran/rekap?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-cancel border p-2 py-1 small" style="font-size: 0.75rem; text-decoration: none;">
                    Lihat Rekap
                </a>
            </div>
            <div class="tbl-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Keterangan</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($detail_pengeluaran)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted small">Tidak ada data pengeluaran</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($detail_pengeluaran as $p): ?>
                                <tr>
                                    <td class="small fw-bold"><?= esc($p['keterangan']) ?></td>
                                    <td>
                                        <?php
                                        $badge = match ($p['kategori']) {
                                            'maintenance' => 'info',
                                            'gaji'        => 'warning',
                                            default       => 'secondary',
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badge ?> opacity-75" style="font-size: 0.7rem;"><?= ucfirst($p['kategori']) ?></span>
                                    </td>
                                    <td class="fw-bold text-danger">Rp <?= number_format($p['jumlah'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>