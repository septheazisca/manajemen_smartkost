<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/tenant/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <a href="/tenant/maintenance">Laporan Kerusakan</a>
    <i class="bi bi-chevron-right"></i>
    <span>Detail</span>
</div>

<?php
$badgeCfg = match ($maintenance['status']) {
    'menunggu' => ['bg' => 'warning', 'label' => 'Menunggu'],
    'proses'   => ['bg' => 'info',    'label' => 'Diproses'],
    'selesai'  => ['bg' => 'success', 'label' => 'Selesai'],
    default    => ['bg' => 'secondary', 'label' => ucfirst($maintenance['status'])],
};
?>

<div class="row g-4">

    <!-- Kiri: Info + Foto -->
    <div class="col-md-7">
        <div class="table-card mb-4" style="padding: 1.5rem;">
            <div class="mb-3">
                <div class="table-card-title">Detail Laporan</div>
                <div class="table-card-sub">Informasi laporan kerusakan kamu</div>
            </div>

            <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">No. Kamar</span>
                    <span class="fw-bold">Kamar <?= esc($maintenance['nomor_kamar'] ?? '-') ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">Status</span>
                    <span class="badge bg-<?= $badgeCfg['bg'] ?>"><?= $badgeCfg['label'] ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">Tanggal Lapor</span>
                    <span><?= date('d M Y H:i', strtotime($maintenance['created_at'])) ?> WIB</span>
                </div>
                <?php if ($maintenance['assigned_at']): ?>
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                        <span class="text-muted small">Tanggal Diproses</span>
                        <span><?= date('d M Y H:i', strtotime($maintenance['assigned_at'])) ?> WIB</span>
                    </div>
                <?php endif; ?>
                <?php if ($maintenance['selesai_at']): ?>
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                        <span class="text-muted small">Tanggal Selesai</span>
                        <span><?= date('d M Y H:i', strtotime($maintenance['selesai_at'])) ?> WIB</span>
                    </div>
                <?php endif; ?>
                <div class="py-2">
                    <span class="text-muted small d-block mb-1">Deskripsi Kerusakan</span>
                    <p class="mb-0"><?= esc($maintenance['deskripsi']) ?></p>
                </div>
            </div>
        </div>

        <?php if ($maintenance['foto']): ?>
            <div class="table-card" style="padding: 1.5rem;">
                <div class="mb-3">
                    <div class="table-card-title">Foto Kerusakan</div>
                </div>
                <img src="<?= base_url('uploads/maintenance/' . $maintenance['foto']) ?>"
                    class="img-fluid rounded w-100" style="max-height: 350px; object-fit: cover;">
            </div>
        <?php endif; ?>
    </div>

    <!-- Kanan: Status & PJ -->
    <div class="col-md-5">

        <!-- Timeline Status -->
        <div class="table-card mb-4" style="padding: 1.5rem;">
            <div class="mb-3">
                <div class="table-card-title">Status Penanganan</div>
            </div>
            <div class="d-flex flex-column gap-3">
                <!-- Step 1 -->
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 36px; height: 36px; background: #e6fff4; flex-shrink: 0;">
                        <i class="bi bi-check" style="color: #10b981;"></i>
                    </div>
                    <div>
                        <p class="fw-bold mb-0 small">Laporan Diterima</p>
                        <small class="text-muted"><?= date('d M Y H:i', strtotime($maintenance['created_at'])) ?></small>
                    </div>
                </div>
                <!-- Step 2 -->
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 36px; height: 36px; background: <?= $maintenance['assigned_at'] ? '#e6f4ff' : '#f8f9fa' ?>; flex-shrink: 0;">
                        <i class="bi bi-person-check" style="color: <?= $maintenance['assigned_at'] ? '#3b82f6' : '#adb5bd' ?>;"></i>
                    </div>
                    <div>
                        <p class="fw-bold mb-0 small" style="color: <?= $maintenance['assigned_at'] ? 'inherit' : '#adb5bd' ?>">Ditugaskan ke PJ</p>
                        <small class="text-muted">
                            <?= $maintenance['assigned_at'] ? date('d M Y H:i', strtotime($maintenance['assigned_at'])) : 'Menunggu...' ?>
                        </small>
                    </div>
                </div>
                <!-- Step 3 -->
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 36px; height: 36px; background: <?= $maintenance['selesai_at'] ? '#e6fff4' : '#f8f9fa' ?>; flex-shrink: 0;">
                        <i class="bi bi-check-all" style="color: <?= $maintenance['selesai_at'] ? '#10b981' : '#adb5bd' ?>;"></i>
                    </div>
                    <div>
                        <p class="fw-bold mb-0 small" style="color: <?= $maintenance['selesai_at'] ? 'inherit' : '#adb5bd' ?>">Pekerjaan Selesai</p>
                        <small class="text-muted">
                            <?= $maintenance['selesai_at'] ? date('d M Y H:i', strtotime($maintenance['selesai_at'])) : 'Menunggu...' ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info PJ -->
        <?php if ($maintenance['nama_pj']): ?>
            <div class="table-card mb-4" style="padding: 1.5rem;">
                <div class="mb-3">
                    <div class="table-card-title">Penanggung Jawab</div>
                    <div class="table-card-sub">PJ yang menangani laporan ini</div>
                </div>
                <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                    <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid var(--border);">
                        <span class="text-muted small">Nama</span>
                        <span class="fw-bold"><?= esc($maintenance['nama_pj']) ?></span>
                    </div>
                    <?php if ($maintenance['catatan_pj']): ?>
                        <div class="py-2" style="border-bottom: 1px solid var(--border);">
                            <span class="text-muted small d-block mb-1">Catatan</span>
                            <p class="mb-0 fst-italic small">"<?= esc($maintenance['catatan_pj']) ?>"</p>
                        </div>
                    <?php endif; ?>
                    <?php if ($maintenance['biaya'] > 0): ?>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted small">Biaya</span>
                            <span class="fw-bold text-danger">Rp <?= number_format($maintenance['biaya'], 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <a href="/tenant/maintenance" class="btn-cancel w-100 d-flex align-items-center justify-content-center" style="height: 38px; text-decoration: none;">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar
        </a>
    </div>
</div>

<?= $this->endSection() ?>