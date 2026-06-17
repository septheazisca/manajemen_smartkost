<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Profil Admin</span>
</div>

<!-- Flash Messages -->
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

    <!-- Info Card -->
    <div class="col-md-4">
        <div class="table-card h-100" style="padding: 1.5rem;">
            <div class="mb-3">
                <div class="table-card-title">Info Admin</div>
                <div class="table-card-sub">Detail akun administrator</div>
            </div>

            <!-- Avatar -->
            <div class="text-center mb-4">
                <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle"
                    style="width: 80px; height: 80px; background: linear-gradient(135deg, #6366f1, #4f46e5); font-size: 2rem; color: #fff; font-weight: bold;">
                    <?php
                    $name = session()->get('name');
                    $initial = (!empty(trim($name))) ? strtoupper(substr(trim($name), 0, 1)) : 'A';
                    echo $initial;
                    ?>
                </div>
                <h6 class="fw-bold mt-3 mb-0"><?= esc($user['name'] ?? '-') ?></h6>
                <small class="text-muted">Administrator</small>
            </div>

            <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">Email</span>
                    <span class="fw-bold small"><?= esc($user['email'] ?? '-') ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">No. HP</span>
                    <span class="fw-bold"><?= esc($user['phone'] ?? '-') ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small">Terdaftar</span>
                    <span class="fw-bold small"><?= isset($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-' ?></span>
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
                <div class="table-card-sub">Perbarui informasi akun admin Anda</div>
            </div>

            <form action="/admin/profile/update" method="post">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                            value="<?= old('name', $user['name'] ?? '') ?>" required
                            placeholder="Nama lengkap admin">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
                            value="<?= old('email', $user['email'] ?? '') ?>" required
                            placeholder="email@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control"
                            value="<?= old('phone', $user['phone'] ?? '') ?>" required
                            placeholder="08xxxxxxxxxx">
                        <small class="text-muted">Nomor ini digunakan untuk notifikasi WhatsApp admin.</small>
                    </div>
                    <div class="col-md-12 mt-2">
                        <button type="submit" class="btn-primary-custom w-100" style="height: 42px;">
                            <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Info Section -->
        <div class="table-card mt-4" style="padding: 1.5rem;">
            <div class="mb-3">
                <div class="table-card-title"><i class="bi bi-info-circle me-2"></i>Informasi Penting</div>
            </div>
            <div class="d-flex align-items-start mb-3">
                <div class="me-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 36px; height: 36px; background: rgba(99, 102, 241, 0.1);">
                        <i class="bi bi-phone text-primary" style="font-size: 1rem;"></i>
                    </div>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold small">Nomor HP untuk Notifikasi</h6>
                    <p class="text-muted small mb-0">Nomor HP admin digunakan sebagai tujuan notifikasi saat penyewa mengupload bukti pembayaran. Pastikan nomor yang terdaftar aktif dan terhubung dengan WhatsApp.</p>
                </div>
            </div>
            <div class="d-flex align-items-start">
                <div class="me-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 36px; height: 36px; background: rgba(234, 179, 8, 0.1);">
                        <i class="bi bi-shield-lock" style="font-size: 1rem; color: #eab308;"></i>
                    </div>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold small">Keamanan Akun</h6>
                    <p class="text-muted small mb-0">Untuk mengganti password, gunakan menu <strong>Ganti Password</strong> di sidebar atau klik tombol di kartu profil di samping.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
