<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Keamanan</span>
    <i class="bi bi-chevron-right"></i>
    <span>Ganti Password</span>
</div>

<div class="row justify-content-center">
    <div class="col-xl-12">
        <div class="table-card border-0 shadow-sm">
            <div class="table-card-header bg-white text-center d-block py-4">
                <div class="mb-2">
                    <div class="d-inline-flex align-items-center justify-content-center btn-primary-custom text-light rounded-circle" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-lock fs-2"></i>
                    </div>
                </div>
                <div class="table-card-title fs-5">Perbarui Password</div>
                <p class="text-muted small mb-0">Pastikan password Anda kuat dan sulit ditebak</p>
            </div>

            <div class="p-4">
                <!-- Alert Messages -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div><?= session()->getFlashdata('success') ?></div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('info')): ?>
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <div><?= session()->getFlashdata('info') ?></div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <div class="fw-bold mb-1 small"><i class="bi bi-exclamation-triangle-fill me-2"></i> Terjadi Kesalahan:</div>
                        <ul class="mb-0 small ps-3">
                            <?php foreach (session()->getFlashdata('errors') as $e): ?>
                                <li><?= esc($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="/change-password" method="post" id="formPassword">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                            <input type="password" name="password_baru" class="form-control form-control border-start-0 ps-2" required minlength="8">
                            <button class="btn btn-outline-light border text-muted" type="button" onclick="togglePassword('password_baru')">
                                <i class="bi bi-eye" id="icon-password_baru"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-check2-circle"></i></span>
                            <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control border-start-0 ps-2" placeholder="Ulangi password baru" required minlength="8" required shadow-none>
                            <button class="btn btn-outline-light border text-muted" type="button" onclick="togglePassword('konfirmasi_password')">
                                <i class="bi bi-eye" id="icon-konfirmasi_password"></i>
                            </button>
                        </div>
                    </div>

                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka.</small>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn-primary-custom py-2 fw-bold">
                            <i class="bi bi-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = "password";
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>

<?= $this->endSection() ?>