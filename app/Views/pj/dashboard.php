<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/pj/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Overview</span>
</div>

<!-- Welcome -->
<div class="table-card mb-4" style="padding: 1.5rem;">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle"
            style="width: 56px; height: 56px; background: linear-gradient(135deg, #C484F5, #7c3aed); font-size: 1.5rem; color: #fff; font-weight: bold; flex-shrink: 0;">
            <?= strtoupper(substr($pj['nama'], 0, 1)) ?>
        </div>
        <div>
            <h5 class="fw-bold mb-0">Selamat datang, <?= esc($pj['nama']) ?>!</h5>
            <small class="text-muted">
                <?php if ($pj['spesialisasi']): ?>
                    <i class="bi bi-wrench me-1"></i><?= esc($pj['spesialisasi']) ?>
                <?php else: ?>
                    <i class="bi bi-person-gear me-1"></i>Penanggung Jawab
                <?php endif; ?>
            </small>
        </div>
        <div class="ms-auto">
            <a href="/pj/maintenance" class="btn-add border-0">
                <i class="bi bi-wrench-adjustable"></i> Lihat Tugas
            </a>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #ede9fe;">
                <i class="bi bi-list-task" style="color: #7c3aed;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Tugas</div>
                <div class="stat-value"><?= $total_tugas ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e6f4ff;">
                <i class="bi bi-wrench-adjustable" style="color: #3b82f6;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Sedang Dikerjakan</div>
                <div class="stat-value"><?= $tugas_proses ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e6fff4;">
                <i class="bi bi-check-circle" style="color: #10b981;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Selesai</div>
                <div class="stat-value"><?= $tugas_selesai ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Gaji -->
<div class="table-card">
    <div class="table-card-header">
        <div>
            <div class="table-card-title">Riwayat Gaji</div>
            <div class="table-card-sub">Rekap pembayaran gaji kamu</div>
        </div>
    </div>

    <div class="tbl-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Jumlah</th>
                    <th>Tanggal Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($riwayat_gaji)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: #6c757d;">
                            <i class="bi bi-cash-stack" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                            Belum ada riwayat pembayaran gaji.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $bulanList = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                    foreach ($riwayat_gaji as $i => $g):
                    ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $bulanList[str_pad($g['bulan'], 2, '0', STR_PAD_LEFT)] ?? $g['bulan'] ?></td>
                            <td><?= $g['tahun'] ?></td>
                            <td class="fw-bold text-success">Rp <?= number_format($g['jumlah'], 0, ',', '.') ?></td>
                            <td>
                                <small><?= date('d/m/Y', strtotime($g['created_at'])) ?></small><br>
                                <small class="text-muted"><?= date('H:i', strtotime($g['created_at'])) ?> WIB</small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>