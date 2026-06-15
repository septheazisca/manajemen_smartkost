<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/tenant/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Tagihan Saya</span>
</div>

<!-- Stat Cards -->
<?php
$totalPending   = 0;
$totalMenunggak = 0;
$totalLunas     = 0;
foreach ($tagihan as $t) {
    $nominal = $t['jumlah'] + $t['nominal_unik'];
    if ($t['status'] == 'pending' || $t['status'] == 'menunggu_konfirmasi') $totalPending   += $nominal;
    if ($t['status'] == 'menunggak')  $totalMenunggak += $nominal;
    if ($t['status'] == 'lunas')      $totalLunas     += $nominal;
}
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e6fff4;">
                <i class="bi bi-check-all" style="color: #10b981;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Sudah Bayar</div>
                <div class="stat-value" style="font-size: 1.1rem;">Rp <?= number_format($totalLunas, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff7e6;">
                <i class="bi bi-clock-history" style="color: #f59e0b;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Pending</div>
                <div class="stat-value" style="font-size: 1.1rem;">Rp <?= number_format($totalPending, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff1f0;">
                <i class="bi bi-exclamation-circle" style="color: #ff4d4f;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Menunggak</div>
                <div class="stat-value" style="font-size: 1.1rem;">Rp <?= number_format($totalMenunggak, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Flash Message -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <i class="bi bi-x-circle-fill me-2"></i><?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Tabel Tagihan -->
<div class="table-card">
    <div class="table-card-header">
        <div>
            <div class="table-card-title">Riwayat Tagihan</div>
            <div class="table-card-sub">Daftar semua tagihan sewa kamu</div>
        </div>
        <div class="toolbar">
            <a href="/tenant/tagihan/export" class="btn btn-success text-white text-nowrap d-flex align-items-center gap-1" style="background: #198754; border: none; font-size: .83rem; font-weight: 600; padding: .5rem 1.1rem; border-radius: var(--radius-sm); transition: all .2s; box-shadow: 0 4px 14px rgba(25, 135, 84, 0.25);">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="tbl-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Periode</th>
                    <th>Jumlah Sewa</th>
                    <th>Kode Unik</th>
                    <th>Total Bayar</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tagihan)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: #6c757d;">
                            <i class="bi bi-receipt" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                            Belum ada tagihan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $bulanList = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                    foreach ($tagihan as $i => $t):
                        $periode    = ($bulanList[str_pad($t['bulan'], 2, '0', STR_PAD_LEFT)] ?? $t['bulan']) . ' ' . $t['tahun'];
                        $totalBayar = $t['jumlah'] + $t['nominal_unik'];
                        $badgeCfg   = match ($t['status']) {
                            'lunas'               => ['bg' => 'success',   'label' => 'Lunas'],
                            'pending'             => ['bg' => 'warning',   'label' => 'Pending'],
                            'menunggu_konfirmasi' => ['bg' => 'info',      'label' => 'Menunggu Konfirmasi'],
                            'menunggak'           => ['bg' => 'danger',    'label' => 'Menunggak'],
                            default               => ['bg' => 'secondary', 'label' => ucfirst($t['status'])],
                        };
                    ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-bold"><?= $periode ?></td>
                            <td>Rp <?= number_format($t['jumlah'], 0, ',', '.') ?></td>
                            <td><small class="text-muted">+Rp <?= number_format($t['nominal_unik'], 0, ',', '.') ?></small></td>
                            <td class="fw-bold">Rp <?= number_format($totalBayar, 0, ',', '.') ?></td>
                            <td>
                                <small><?= date('d/m/Y', strtotime($t['jatuh_tempo'])) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-<?= $badgeCfg['bg'] ?>"><?= $badgeCfg['label'] ?></span>
                            </td>
                            <td>
                                <?php if ($t['status'] === 'pending' || $t['status'] === 'menunggak'): ?>
                                    <button class="action-btn edit" data-bs-toggle="modal" data-bs-target="#modalUpload<?= $t['id'] ?>" title="Upload Bukti">
                                        <i class="bi bi-upload"></i>
                                    </button>
                                <?php elseif ($t['status'] === 'menunggu_konfirmasi'): ?>
                                    <span class="text-muted small"><i class="bi bi-clock"></i> Diproses</span>
                                <?php else: ?>
                                    <span class="text-success small"><i class="bi bi-check-circle"></i> Lunas</span>
                                <?php endif; ?>
                                <a href="/tenant/tagihan/detail/<?= $t['id'] ?>" class="action-btn edit" title="Detail">
                                        <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL UPLOAD (foreach) -->
<?php
$bulanList = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
foreach ($tagihan as $t):
    if ($t['status'] !== 'pending' && $t['status'] !== 'menunggak') continue;
    $periode    = ($bulanList[str_pad($t['bulan'], 2, '0', STR_PAD_LEFT)] ?? $t['bulan']) . ' ' . $t['tahun'];
    $totalBayar = $t['jumlah'] + $t['nominal_unik'];
?>
    <div class="modal fade" id="modalUpload<?= $t['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title d-flex align-items-center">
                        <span class="modal-icon add"><i class="bi bi-upload"></i></span>
                        Upload Bukti Transfer
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/tenant/tagihan/upload-bukti/<?= $t['id'] ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <div class="text-center mb-4 p-3" style="background: #f8f9ff; border-radius: 12px;">
                            <small class="text-muted d-block mb-1">Periode: <strong><?= $periode ?></strong></small>
                            <small class="text-muted d-block mb-2">Total yang harus ditransfer:</small>
                            <h4 class="fw-bold text-primary mb-0">Rp <?= number_format($totalBayar, 0, ',', '.') ?></h4>
                            <small class="text-muted">Sudah termasuk kode unik Rp <?= number_format($t['nominal_unik'], 0, ',', '.') ?></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Foto Bukti Transfer <span class="text-danger">*</span></label>
                            <input type="file" name="bukti_transfer" class="form-control" accept="image/jpeg,image/png,image/jpg" required>
                            <small class="text-muted">Format JPG/PNG, maksimal 2MB.</small>
                        </div>
                        <div class="alert alert-warning border-0 small" style="border-radius: 10px;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Pastikan nominal transfer sesuai sampai digit terakhir termasuk kode unik.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-primary-custom"><i class="bi bi-send"></i> Kirim Bukti</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?= $this->endSection() ?>