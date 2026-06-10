<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Maintenance</span>
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
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Table -->
<div class="table-card">
    <div class="table-card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 w-100">
        <div class="text-nowrap">
            <div class="table-card-title fw-bold" style="font-size: 1.15rem; color: #1e293b;">Laporan Maintenance</div>
            <div class="table-card-sub text-muted small">Total <?= count($maintenance) ?> laporan</div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end" style="max-width: 500px;">
            <div class="input-group flex-grow-1" style="max-width: 260px;">
                <input type="text" id="searchMaintenance" class="form-control" placeholder="Cari laporan maintenance...">
                <span class="input-group-text bg-light text-muted">
                    <i class="bi bi-search"></i>
                </span>
            </div>
        </div>
    </div>

    <div class="tbl-wrap">
        <table class="data-table" id="tableMaintenance">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Penyewa</th>
                    <th>Kamar</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>PJ</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($maintenance)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: #6c757d;">
                            <i class="bi bi-wrench" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                            Belum ada laporan maintenance.
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
                            <td><strong><?= esc($m['nama_penyewa'] ?? '-') ?></strong></td>
                            <td>Kamar <?= esc($m['nomor_kamar'] ?? '-') ?></td>
                            <td style="max-width: 200px;">
                                <span title="<?= esc($m['deskripsi']) ?>">
                                    <?= esc(strlen($m['deskripsi']) > 60 ? substr($m['deskripsi'], 0, 60) . '...' : $m['deskripsi']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $badgeCfg['bg'] ?>"><?= $badgeCfg['label'] ?></span>
                            </td>
                            <td>
                                <?php if ($m['nama_pj']): ?>
                                    <span><?= esc($m['nama_pj']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border">Belum di-assign</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small><?= date('d/m/Y', strtotime($m['created_at'])) ?></small><br>
                                <small class="text-muted"><?= date('H:i', strtotime($m['created_at'])) ?> WIB</small>
                            </td>
                            <td>
                                <div style="display: flex; gap: .35rem;">
                                    <a href="/admin/maintenance/<?= $m['id'] ?>" class="action-btn edit" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="action-btn del btn-delete-maintenance"
                                        data-url="/admin/maintenance/delete/<?= $m['id'] ?>"
                                        data-nama="<?= esc(substr($m['deskripsi'], 0, 30)) ?>..."
                                        title="Hapus">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-delete-maintenance').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.dataset.url;
                const nama = this.dataset.nama;
                Swal.fire({
                    title: 'Hapus Laporan?',
                    text: `"${nama}" akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d51717',
                    cancelButtonColor: '#175fd4',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) window.location.href = url;
                });
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchInput = document.getElementById('searchMaintenance');
        const table = document.getElementById('tableMaintenance');
        const rows = table.querySelectorAll('tbody tr');

        searchInput.addEventListener('keyup', function() {

            const keyword = this.value.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();

                if (text.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

        });
    });
</script>

<?= $this->endSection() ?>