<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/pj/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Tugas Maintenance</span>
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
    <div class="table-card-header">
        <div>
            <div class="table-card-title">Tugas Maintenance Saya</div>
            <div class="table-card-sub">Daftar pekerjaan yang di-assign ke kamu</div>
        </div>
    </div>

    <div class="tbl-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Penyewa</th>
                    <th>Kamar</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($maintenance)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: #6c757d;">
                            <i class="bi bi-wrench" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                            Belum ada tugas maintenance.
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
                            <td>
                                <strong><?= esc($m['nama_penyewa'] ?? '-') ?></strong>
                            </td>
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
                                <small><?= date('d/m/Y', strtotime($m['created_at'])) ?></small><br>
                                <small class="text-muted"><?= date('H:i', strtotime($m['created_at'])) ?> WIB</small>
                            </td>
                            <td>
                                <?php if ($m['pj_id'] === null): ?>
                                    <a href="/pj/maintenance/ambil/<?= $m['id'] ?>"
                                        class="action-btn edit btn-ambil"
                                        data-url="/pj/maintenance/ambil/<?= $m['id'] ?>"
                                        title="Ambil Tugas">
                                        <i class="bi bi-hand-index"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="/pj/maintenance/<?= $m['id'] ?>" class="action-btn edit" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-ambil').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.dataset.url;
            Swal.fire({
                title: 'Ambil Tugas?',
                text: 'Kamu akan bertanggung jawab mengerjakan laporan ini.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#175fd4',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ambil!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) window.location.href = url;
            });
        });
    });
</script>
<?= $this->endSection() ?>