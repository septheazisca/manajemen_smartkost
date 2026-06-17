<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Detail Kost</span>
</div>

<div class="table-card p-4">
    <!-- Header -->
    <div class="table-card-header pb-3 mb-4 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <div class="table-card-title fw-bold" style="font-size: 1.25rem; color: #1e293b;">
                <i class="bi bi-info-circle text-primary me-2"></i>Pengaturan Detail Kost
            </div>
            <div class="table-card-sub text-muted small">Kelola data informasi kost yang akan ditampilkan di bagian footer halaman landing dan detail.</div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form action="/admin/detail-kost/update" method="POST">
        <?= csrf_field() ?>

        <div class="row g-4">
            <!-- SECTION 1: INFORMASI UMUM -->
            <div class="col-12">
                <div class="fw-bold mb-3 text-secondary" style="font-size: .95rem; border-left: 3px solid var(--primary); padding-left: .5rem;">
                    Informasi Umum Kost
                </div>
            </div>

            <!-- Detail / Deskripsi Kost -->
            <div class="col-12">
                <label for="detail_kost" class="form-label fw-semibold text-dark small">Deskripsi Singkat Kost</label>
                <textarea name="detail_kost" id="detail_kost" class="form-control" rows="4" placeholder="Tulis deskripsi atau detail kost..." required style="border-radius: var(--radius-sm); border: 1.5px solid var(--border); font-size: .88rem;"><?= esc($kost_details['detail_kost'] ?? '') ?></textarea>
                <div class="form-text text-muted" style="font-size: .75rem;">Deskripsi ini akan tampil di bagian penjelasan kost di footer kiri.</div>
            </div>

            <!-- Alamat Lengkap -->
            <div class="col-md-6">
                <label for="alamat" class="form-label fw-semibold text-dark small">Alamat Lengkap</label>
                <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Jl. Margonda Raya No. 42..." style="border-radius: var(--radius-sm); border: 1.5px solid var(--border); font-size: .88rem;"><?= esc($kost_details['alamat'] ?? '') ?></textarea>
            </div>

            <!-- Jam Operasional -->
            <div class="col-md-6">
                <label for="jam_operasi" class="form-label fw-semibold text-dark small">Jam Operasional</label>
                <textarea name="jam_operasi" id="jam_operasi" class="form-control" rows="3" placeholder="Senin – Sabtu: 08.00 – 20.00 WIB&#10;Minggu: 09.00 – 17.00 WIB" style="border-radius: var(--radius-sm); border: 1.5px solid var(--border); font-size: .88rem;"><?= esc($kost_details['jam_operasi'] ?? '') ?></textarea>
            </div>

            <!-- SECTION 2: KONTAK & LINK -->
            <div class="col-12 mt-5">
                <div class="fw-bold mb-3 text-secondary" style="font-size: .95rem; border-left: 3px solid var(--primary); padding-left: .5rem;">
                    Hubungi Kami & Sosial Media
                </div>
            </div>

            <!-- Nomor Telepon -->
            <div class="col-md-4">
                <label for="no_telepon" class="form-label fw-semibold text-dark small">Nomor Telepon</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border: 1.5px solid var(--border); border-radius: var(--radius-sm) 0 0 var(--radius-sm);"><i class="bi bi-telephone text-muted"></i></span>
                    <input type="text" name="no_telepon" id="no_telepon" class="form-control" value="<?= esc($kost_details['no_telepon'] ?? '') ?>" placeholder="+62 812-3456-7890" style="border: 1.5px solid var(--border); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; font-size: .88rem;">
                </div>
            </div>

            <!-- Email -->
            <div class="col-md-4">
                <label for="email" class="form-label fw-semibold text-dark small">Alamat Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border: 1.5px solid var(--border); border-radius: var(--radius-sm) 0 0 var(--radius-sm);"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" name="email" id="email" class="form-control" value="<?= esc($kost_details['email'] ?? '') ?>" placeholder="halo@smartkost.id" style="border: 1.5px solid var(--border); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; font-size: .88rem;">
                </div>
            </div>

            <!-- WhatsApp Link -->
            <div class="col-md-4">
                <label for="link_whatsapp" class="form-label fw-semibold text-dark small">Link/No WhatsApp</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border: 1.5px solid var(--border); border-radius: var(--radius-sm) 0 0 var(--radius-sm);"><i class="bi bi-whatsapp text-muted"></i></span>
                    <input type="url" name="link_whatsapp" id="link_whatsapp" class="form-control" value="<?= esc($kost_details['link_whatsapp'] ?? '') ?>" placeholder="https://wa.me/..." style="border: 1.5px solid var(--border); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; font-size: .88rem;">
                </div>
            </div>

            <!-- Instagram -->
            <div class="col-md-4">
                <label for="link_instagram" class="form-label fw-semibold text-dark small">Link Instagram</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border: 1.5px solid var(--border); border-radius: var(--radius-sm) 0 0 var(--radius-sm);"><i class="bi bi-instagram text-muted"></i></span>
                    <input type="url" name="link_instagram" id="link_instagram" class="form-control" value="<?= esc($kost_details['link_instagram'] ?? '') ?>" placeholder="https://instagram.com/..." style="border: 1.5px solid var(--border); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; font-size: .88rem;">
                </div>
            </div>

            <!-- TikTok -->
            <div class="col-md-4">
                <label for="link_tiktok" class="form-label fw-semibold text-dark small">Link TikTok</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border: 1.5px solid var(--border); border-radius: var(--radius-sm) 0 0 var(--radius-sm);"><i class="bi bi-tiktok text-muted"></i></span>
                    <input type="url" name="link_tiktok" id="link_tiktok" class="form-control" value="<?= esc($kost_details['link_tiktok'] ?? '') ?>" placeholder="https://tiktok.com/@..." style="border: 1.5px solid var(--border); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; font-size: .88rem;">
                </div>
            </div>

            <!-- Twitter / X -->
            <div class="col-md-4">
                <label for="link_twitter" class="form-label fw-semibold text-dark small">Link Twitter / X</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border: 1.5px solid var(--border); border-radius: var(--radius-sm) 0 0 var(--radius-sm);"><i class="bi bi-twitter text-muted"></i></span>
                    <input type="url" name="link_twitter" id="link_twitter" class="form-control" value="<?= esc($kost_details['link_twitter'] ?? '') ?>" placeholder="https://twitter.com/..." style="border: 1.5px solid var(--border); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; font-size: .88rem;">
                </div>
            </div>
        </div>

        <div class="mt-5 border-top pt-4 text-end">
            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, var(--primary), var(--accent)); border: none; padding: .65rem 1.8rem; border-radius: var(--radius-sm); font-weight: 600; box-shadow: 0 4px 14px rgba(196,132,245,.25);">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
