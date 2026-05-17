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
                    <?php
                    $name = session()->get('name');
                    // Bersihkan spasi dan cek apakah benar-benar ada isinya
                    $initial = (!empty(trim($name))) ? strtoupper(substr(trim($name), 0, 1)) : 'U';
                    echo $initial;
                    ?>
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
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control"
                            value="<?= esc($penyewa['name'] ?? '-') ?>" disabled>
                        <small class="text-muted">Hubungi admin untuk mengubah nama.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control"
                            value="<?= esc($penyewa['email'] ?? '-') ?>" disabled>
                        <small class="text-muted">Hubungi admin untuk mengubah email.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. HP <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control"
                            value="<?= old('phone', $penyewa['phone'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Asal Kota</label>
                        <input type="text" name="asal_kota" class="form-control"
                            value="<?= old('asal_kota', $penyewa['asal_kota'] ?? '') ?>"
                            placeholder="Contoh: Jakarta">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="2"
                            placeholder="Alamat asal kamu"><?= old('alamat', $penyewa['alamat'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status Pekerjaan</label>
                        <select name="status_pekerjaan" class="form-select">
                            <option value="">-- Pilih --</option>
                            <option value="bekerja" <?= ($penyewa['status_pekerjaan'] ?? '') == 'bekerja' ? 'selected' : '' ?>>Bekerja</option>
                            <option value="pelajar/mahasiswa" <?= ($penyewa['status_pekerjaan'] ?? '') == 'pelajar/mahasiswa' ? 'selected' : '' ?>>Pelajar/Mahasiswa</option>
                            <option value="lainnya" <?= ($penyewa['status_pekerjaan'] ?? '') == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status Pernikahan</label>
                        <select name="status_pernikahan" class="form-select">
                            <option value="">-- Pilih --</option>
                            <option value="belum menikah" <?= ($penyewa['status_pernikahan'] ?? '') == 'belum menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                            <option value="menikah" <?= ($penyewa['status_pernikahan'] ?? '') == 'menikah' ? 'selected' : '' ?>>Menikah</option>
                            <option value="lainnya" <?= ($penyewa['status_pernikahan'] ?? '') == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Nomor Darurat</label>
                        <input type="text" name="nomor_darurat" class="form-control"
                            value="<?= old('nomor_darurat', $penyewa['nomor_darurat'] ?? '') ?>"
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