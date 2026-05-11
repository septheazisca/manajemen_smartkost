<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/tenant/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Laporan Kerusakan</span>
</div>

<!-- Stat Cards -->
<?php
$totalMenunggu = 0;
$totalProses   = 0;
$totalSelesai  = 0;
foreach ($maintenance as $m) {
    if ($m['status'] == 'menunggu') $totalMenunggu++;
    if ($m['status'] == 'proses')   $totalProses++;
    if ($m['status'] == 'selesai')  $totalSelesai++;
}
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff7e6;">
                <i class="bi bi-hourglass-split" style="color: #f59e0b;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Menunggu</div>
                <div class="stat-value"><?= $totalMenunggu ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e6f4ff;">
                <i class="bi bi-wrench-adjustable" style="color: #3b82f6;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Sedang Diproses</div>
                <div class="stat-value"><?= $totalProses ?></div>
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
                <div class="stat-value"><?= $totalSelesai ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Flash -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error') || session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?php if (session()->getFlashdata('error')): ?>
            <?= session()->getFlashdata('error') ?>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <?php foreach (session()->getFlashdata('errors') as $e): ?>
                <div><?= esc($e) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Table Card -->
<div class="table-card">
    <div class="table-card-header">
        <div>
            <div class="table-card-title">Laporan Kerusakan</div>
            <div class="table-card-sub">Pantau status perbaikan fasilitas kamar kamu</div>
        </div>
        <div class="toolbar">
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalLapor">
                <i class="bi bi-plus-lg"></i> Lapor Kerusakan
            </button>
        </div>
    </div>

    <div class="tbl-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kamar</th>
                    <th>Deskripsi</th>
                    <th>Foto</th>
                    <th>Status</th>
                    <th>Catatan Petugas</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($maintenance)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: #6c757d;">
                            <i class="bi bi-wrench" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                            Belum ada laporan kerusakan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($maintenance as $i => $m):
                        $badgeCfg = match ($m['status']) {
                            'menunggu' => ['bg' => 'warning', 'label' => 'Menunggu'],
                            'proses'   => ['bg' => 'info',    'label' => 'Diproses'],
                            'selesai'  => ['bg' => 'success', 'label' => 'Selesai'],
                            default    => ['bg' => 'secondary', 'label' => ucfirst($m['status'])],
                        };
                    ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-bold">Kamar <?= esc($m['nomor_kamar'] ?? '-') ?></td>
                            <td style="max-width: 200px;">
                                <span title="<?= esc($m['deskripsi']) ?>">
                                    <?= esc(strlen($m['deskripsi']) > 60 ? substr($m['deskripsi'], 0, 60) . '...' : $m['deskripsi']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($m['foto']): ?>
                                    <a href="<?= base_url('uploads/maintenance/' . $m['foto']) ?>"
                                        target="_blank" class="action-btn edit" title="Lihat Foto">
                                        <i class="bi bi-image"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $badgeCfg['bg'] ?>"><?= $badgeCfg['label'] ?></span>
                            </td>
                            <td style="max-width: 180px;">
                                <?php if ($m['catatan_pj']): ?>
                                    <span class="text-muted small fst-italic" title="<?= esc($m['catatan_pj']) ?>">
                                        "<?= esc(strlen($m['catatan_pj']) > 50 ? substr($m['catatan_pj'], 0, 50) . '...' : $m['catatan_pj']) ?>"
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small><?= date('d/m/Y', strtotime($m['created_at'])) ?></small><br>
                                <small class="text-muted"><?= date('H:i', strtotime($m['created_at'])) ?> WIB</small>
                            </td>
                            <td>
                                <?php if ($m['foto']): ?>
                                    <a href="<?= base_url('uploads/maintenance/' . $m['foto']) ?>"
                                        target="_blank" class="action-btn edit" title="Lihat Foto">
                                        <i class="bi bi-image"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="/tenant/maintenance/<?= $m['id'] ?>" class="action-btn edit" title="Detail">
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

<!-- Modal Lapor -->
<div class="modal fade" id="modalLapor" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title d-flex align-items-center">
                    <span class="modal-icon add"><i class="bi bi-plus-circle-fill"></i></span>
                    Lapor Kerusakan
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/tenant/maintenance/lapor" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Kerusakan <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-control" rows="4" required
                            minlength="10"
                            placeholder="Contoh: Lampu kamar mandi mati, kran air bocor..."><?= old('deskripsi') ?></textarea>
                        <small class="text-muted">Minimal 10 karakter.</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Foto Pendukung <span class="text-muted small">(opsional)</span></label>
                        <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/jpg">
                        <small class="text-muted">Format JPG/PNG, maksimal 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-primary-custom"><i class="bi bi-send"></i> Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>