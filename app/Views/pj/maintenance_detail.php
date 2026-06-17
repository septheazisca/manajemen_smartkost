<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/pj/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <a href="/pj/maintenance">Tugas Maintenance</a>
    <i class="bi bi-chevron-right"></i>
    <span>Detail</span>
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

    <!-- Kiri: Info Laporan + Foto -->
    <div class="col-md-7">

        <!-- Info Laporan -->
        <div class="table-card mb-4" style="padding: 1.5rem;">
            <div class="mb-3">
                <div class="table-card-title">Informasi Laporan</div>
                <div class="table-card-sub">Detail kerusakan yang dilaporkan penyewa</div>
            </div>

            <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">Penyewa</span>
                    <span class="fw-bold"><?= esc($maintenance['nama_penyewa'] ?? '-') ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">No. Kamar</span>
                    <span class="fw-bold">Kamar <?= esc($maintenance['nomor_kamar'] ?? '-') ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">Status</span>
                    <span class="badge bg-<?= esc($maintenance['badge_class'] ?? 'secondary') ?>"><?= esc(ucfirst($maintenance['status'])) ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">Tanggal Lapor</span>
                    <span><?= date('d M Y H:i', strtotime($maintenance['created_at'])) ?> WIB</span>
                </div>
                <div class="py-2">
                    <span class="text-muted small d-block mb-1">Deskripsi Kerusakan</span>
                    <p class="mb-0"><?= esc($maintenance['deskripsi']) ?></p>
                </div>
            </div>
        </div>

        <!-- Foto -->
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

    <!-- Kanan: Form Selesai / Hasil -->
    <div class="col-md-5">

        <?php if ($maintenance['status'] === 'proses'): ?>
            <div class="table-card" style="padding: 1.5rem;">
                <div class="mb-4">
                    <div class="table-card-title">Tandai Selesai</div>
                    <div class="table-card-sub">Isi catatan dan biaya setelah pekerjaan selesai</div>
                </div>
                <form action="/pj/maintenance/selesai/<?= $maintenance['id'] ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Catatan Penyelesaian <span class="text-danger">*</span></label>
                        <textarea name="catatan_pj" class="form-control" rows="5" required
                            placeholder="Jelaskan apa yang sudah dikerjakan..."><?= old('catatan_pj') ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Biaya (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="biaya" class="form-control"
                            value="<?= old('biaya', 0) ?>" min="0" required>
                        <small class="text-muted">Isi 0 jika tidak ada biaya.</small>
                    </div>
                    <button type="submit" class="btn-primary-custom w-100" style="height: 42px;">
                        <i class="bi bi-check-lg me-1"></i> Tandai Selesai
                    </button>
                </form>
            </div>

        <?php elseif ($maintenance['status'] === 'selesai'): ?>
            <div class="table-card" style="padding: 1.5rem;">
                <div class="mb-3">
                    <div class="table-card-title">Hasil Pengerjaan</div>
                    <div class="table-card-sub">Ringkasan penyelesaian pekerjaan</div>
                </div>

                <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                    <div class="py-2" style="border-bottom: 1px solid var(--border);">
                        <span class="text-muted small d-block mb-1">Catatan</span>
                        <p class="mb-0 fst-italic">"<?= esc($maintenance['catatan_pj'] ?? '-') ?>"</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                        <span class="text-muted small">Biaya</span>
                        <span class="fw-bold text-danger">Rp <?= number_format($maintenance['biaya'] ?? 0, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">Tanggal Selesai</span>
                        <span><?= $maintenance['selesai_at'] ? date('d M Y H:i', strtotime($maintenance['selesai_at'])) : '-' ?> WIB</span>
                    </div>
                </div>

                <div class="mt-3 p-3 text-center" style="background: #e6fff4; border-radius: 12px;">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                    <p class="mb-0 fw-bold text-success mt-1">Pekerjaan Selesai</p>
                </div>
            </div>

        <?php else: ?>
            <div class="table-card" style="padding: 1.5rem;">
                <div class="text-center py-3">
                    <i class="bi bi-hourglass-split text-warning" style="font-size: 2rem;"></i>
                    <p class="fw-bold mt-2 mb-1">Menunggu Penugasan</p>
                    <p class="text-muted small mb-0">Laporan ini belum di-assign ke kamu secara resmi.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tombol Kembali -->
        <div class="mt-3">
            <a href="/pj/maintenance" class="btn-cancel w-100 d-flex align-items-center justify-content-center" style="height: 38px; text-decoration: none;">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>