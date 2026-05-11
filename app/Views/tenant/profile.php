<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/tenant/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Profil Saya</span>
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
<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?php foreach (session()->getFlashdata('errors') as $e): ?>
            <div><?= esc($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="row g-4">

    <!-- Info Kamar -->
    <div class="col-md-4">
        <div class="table-card h-100" style="padding: 1.5rem;">
            <div class="mb-3">
                <div class="table-card-title">Info Kamar</div>
                <div class="table-card-sub">Detail kamar yang kamu tempati</div>
            </div>

            <!-- Avatar -->
            <div class="text-center mb-4">
                <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle"
                    style="width: 80px; height: 80px; background: linear-gradient(135deg, #C484F5, #7c3aed); font-size: 2rem; color: #fff; font-weight: bold;">
                    <?= strtoupper(substr($penyewa['nama'] ?? $penyewa['name'] ?? 'U', 0, 1)) ?>
                </div>
                <h6 class="fw-bold mt-3 mb-0"><?= esc($penyewa['nama'] ?? $penyewa['name'] ?? '-') ?></h6>
                <small class="text-muted">Penyewa</small>
            </div>

            <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">No. Kamar</span>
                    <span class="fw-bold"><?= esc($penyewa['nomor_kamar'] ?? '-') ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">Harga Sewa</span>
                    <span class="fw-bold">Rp <?= number_format($penyewa['harga'] ?? 0, 0, ',', '.') ?><small class="text-muted fw-normal">/bln</small></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small">Tanggal Masuk</span>
                    <span class="fw-bold"><?= $penyewa['tanggal_masuk'] ? date('d M Y', strtotime($penyewa['tanggal_masuk'])) : '-' ?></span>
                </div>
            </div>

            <div class="mt-3">
                <a href="/change-password" class="btn-cancel w-100 d-flex align-items-center justify-content-center" style="height: 38px; text-decoration: none;">
                    <i class="bi bi-key me-2"></i> Ganti Password
                </a>
            </div>
        </div>
    </div>

    <!-- Form Edit Profil -->
    <div class="col-md-8">
        <div class="table-card" style="padding: 1.5rem;">
            <div class="mb-4">
                <div class="table-card-title">Edit Profil</div>
                <div class="table-card-sub">Perbarui informasi data diri kamu</div>
            </div>

            <form action="/tenant/profile/update" method="post">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control"
                            value="<?= old('nama', $penyewa['nama'] ?? $penyewa['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                            value="<?= old('email', $penyewa['email'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control"
                            value="<?= old('phone', $penyewa['phone'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. KTP / Identitas</label>
                        <input type="text" name="no_ktp" class="form-control"
                            value="<?= old('no_ktp', $penyewa['no_ktp'] ?? '') ?>"
                            placeholder="Opsional">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Kontak Darurat</label>
                        <input type="text" name="kontak_darurat" class="form-control"
                            value="<?= old('kontak_darurat', $penyewa['kontak_darurat'] ?? '') ?>"
                            placeholder="Nama dan nomor HP keluarga/kerabat">
                    </div>
                    <div class="col-md-12 mt-2">
                        <button type="submit" class="btn-primary-custom w-100" style="height: 42px;">
                            <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<?= $this->endSection() ?>