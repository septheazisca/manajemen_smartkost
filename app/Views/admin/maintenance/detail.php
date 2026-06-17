<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <a href="/admin/maintenance">Maintenance</a>
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

<div class="row g-4">

    <!-- Kiri: Info + Foto -->
    <div class="col-md-7">

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
                    <span class="badge bg-<?= esc($maintenance['badge_class']) ?>">
                        <i class="bi <?= esc($maintenance['icon']) ?> me-1"></i><?= esc(ucwords($maintenance['status'])) ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                    <span class="text-muted small">Tanggal Lapor</span>
                    <span><?= date('d M Y H:i', strtotime($maintenance['created_at'])) ?> WIB</span>
                </div>
                <?php if ($maintenance['assigned_at']): ?>
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                        <span class="text-muted small">Tanggal Assign</span>
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

    <!-- Kanan: PJ + Assign -->
    <div class="col-md-5">

        <?php if ($maintenance['nama_pj']): ?>
            <div class="table-card mb-4" style="padding: 1.5rem;">
                <div class="mb-3">
                    <div class="table-card-title">Penanggung Jawab</div>
                    <div class="table-card-sub">PJ yang menangani laporan ini</div>
                </div>

                <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                        <span class="text-muted small">Nama</span>
                        <span class="fw-bold"><?= esc($maintenance['nama_pj']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--border);">
                        <span class="text-muted small">No. HP</span>
                        <span><?= esc($maintenance['phone_pj'] ?? '-') ?></span>
                    </div>
                    <?php if ($maintenance['catatan_pj']): ?>
                        <div class="py-2" style="border-bottom: 1px solid var(--border);">
                            <span class="text-muted small d-block mb-1">Catatan PJ</span>
                            <p class="mb-0 fst-italic">"<?= esc($maintenance['catatan_pj']) ?>"</p>
                        </div>
                    <?php endif; ?>
                    <?php if ($maintenance['biaya']): ?>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted small">Biaya</span>
                            <span class="fw-bold text-danger">Rp <?= number_format($maintenance['biaya'], 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($maintenance['status'] === 'selesai'): ?>
                    <div class="mt-3 p-3 text-center" style="background: #e6fff4; border-radius: 12px;">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                        <p class="mb-0 fw-bold text-success mt-1">Pekerjaan Selesai</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($maintenance['status'] === 'menunggu'): ?>
            <div class="table-card" style="padding: 1.5rem;">
                <div class="mb-4">
                    <div class="table-card-title">Assign ke PJ</div>
                    <div class="table-card-sub">Pilih penanggung jawab untuk menangani laporan ini</div>
                </div>
                <form action="/admin/maintenance/assign/<?= $maintenance['id'] ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label class="form-label">Pilih PJ <span class="text-danger">*</span></label>
                        <select name="pj_id" class="form-select" required>
                            <option value="">-- Pilih PJ --</option>
                            <?php foreach ($pj_list as $pj): ?>
                                <option value="<?= $pj['id'] ?>"><?= esc($pj['nama']) ?>
                                    <?php if ($pj['spesialisasi']): ?>
                                        - <?= esc($pj['spesialisasi']) ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary-custom w-100" style="height: 42px;">
                        <i class="bi bi-person-check me-1"></i> Assign PJ
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="/admin/maintenance" class="btn-cancel w-100 d-flex align-items-center justify-content-center" style="height: 38px; text-decoration: none;">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>