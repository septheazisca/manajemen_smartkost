<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <a href="/admin/pj">Data PJ</a>
    <i class="bi bi-chevron-right"></i>
    <span>Riwayat Gaji: <?= esc($pj['nama']) ?></span>
</div>

<!-- Header Info Data Diri -->
<div class="row mb-4">
    <div class="col-12">
        <div class="table-card" style="padding: 1.5rem;">
            <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-1 fw-bold text-dark"><?= esc($pj['nama']) ?></h4>
                        <span class="badge bg-<?= $pj['is_active'] ? 'success' : 'secondary' ?>">
                            <?= $pj['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3 mb-2">
                            <small class="text-muted d-block">Email</small>
                            <span class="fw-semibold"><?= esc($pj['email'] ?? '-') ?></span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <small class="text-muted d-block">No. WhatsApp</small>
                            <span class="fw-semibold"><?= esc($pj['phone']) ?></span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <small class="text-muted d-block">Spesialisasi</small>
                            <span class="badge bg-light text-dark border fw-medium"><?= esc($pj['spesialisasi'] ?? 'Umum') ?></span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <small class="text-muted d-block">Gaji Pokok / Bulan</small>
                            <span class="fw-bold text-primary">Rp <?= number_format($pj['gaji_bulanan'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-card-header">
        <div>
            <div class="table-card-title">Log Pembayaran Gaji</div>
            <div class="table-card-sub">Menampilkan histori pengiriman gaji per bulan</div>
        </div>
        <!-- <div class="toolbar">
            <a href="/admin/pj" class="btn-cancel text-decoration-none border shadow-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
        </div> -->
    </div>

    <div class="tbl-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Bulan & Tahun</th>
                    <th>Tanggal Pembayaran</th>
                    <th>Nominal Diterima</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($riwayat)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: #6c757d;">
                            <i class="bi bi-info-circle d-block mb-2" style="font-size: 1.5rem;"></i>
                            Belum ada riwayat pembayaran gaji ditemukan untuk periode ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($riwayat as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= $r['bulan'] ?> / <?= $r['tahun'] ?></div>
                            </td>
                            <td>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y', strtotime($r['created_at'])) ?>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-clock me-1"></i> <?= date('H:i', strtotime($r['created_at'])) ?> WIB
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-success">Rp <?= number_format($r['jumlah'], 0, ',', '.') ?></span>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-success-light text-success" style="background: #e1f6e5; border: 1px solid #28a74533; padding: 0.5em 1em;">
                                    <i class="bi bi-check2-circle me-1"></i> Lunas
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .bg-success-light {
        background-color: #d1e7dd;
        color: #0f5132;
    }
</style>

<?= $this->endSection() ?>