<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/pj/dashboard"><i class="bi bi-house"></i> Dashboard</a>
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

    <!-- Info Akun -->
    <div class="col-md-4">
        <div class="table-card h-100" style="padding: 1.5rem;">
            <div class="mb-3">
                <div class="table-card-title">Info Akun</div>
                <div class="table-card-sub">Detail akun penanggung jawab kamu</div>
            </div>

            <!-- Avatar -->
            <div class="text-center mb-4">
                <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle"
                    style="width: 80px; height: 80px; background: linear-gradient(135deg, #FF9A9E, #FECFEF); font-size: 2rem; color: #fff; font-weight: bold;">
                    <?php
                    $name = session()->get('name') ?? $pj['nama'] ?? 'P';
                    $initial = (!empty(trim($name))) ? strtoupper(substr(trim($name), 0, 1)) : 'P';
                    echo $initial;
                    ?>
                </div>
                <h6 class="fw-bold mt-3 mb-0"><?= esc($name) ?></h6>
                <small class="text-muted">Penanggung Jawab</small>
            </div>

            <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">Spesialisasi</span>
                    <span class="fw-bold text-end"><?= esc($pj['spesialisasi'] ?: 'Umum') ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">Gaji Bulanan</span>
                    <span class="fw-bold text-end">Rp <?= number_format($pj['gaji_bulanan'] ?? 0, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small">Bergabung Sejak</span>
                    <span class="fw-bold text-end"><?= date('d M Y', strtotime($pj['created_at'])) ?></span>
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
                <div class="table-card-sub">Perbarui informasi kontak kamu</div>
            </div>

            <form action="/pj/profile/update" method="post">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control"
                            value="<?= esc($pj['nama'] ?? '-') ?>" disabled>
                        <small class="text-muted">Hubungi admin untuk mengubah nama.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control"
                            value="<?= esc($pj['email'] ?? '-') ?>" disabled>
                        <small class="text-muted">Hubungi admin untuk mengubah email.</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control"
                            value="<?= old('phone', $pj['phone'] ?? '') ?>" required>
                        <small class="text-muted">Nomor ini juga digunakan untuk login.</small>
                    </div>
                    
                    <div class="col-md-12 mt-4">
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
