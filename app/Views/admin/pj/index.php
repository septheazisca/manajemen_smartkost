<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Penanggung Jawab</span>
</div>

<div class="table-card">
    <!-- Header -->
     <div class="table-card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 w-100">
        <div class="text-nowrap">
            <div class="table-card-title fw-bold" style="font-size: 1.15rem; color: #1e293b;">Data Penanggung Jawab</div>
            <div class="table-card-sub text-muted small">Total <?= count($pj_list) ?> penanggung jawab</div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end" style="max-width: 500px;">
            <div class="input-group flex-grow-1" style="max-width: 260px;">
                <input type="text" id="searchPj" class="form-control" placeholder="Cari nama penanggung jawab...">
                <span class="input-group-text bg-light text-muted">
                    <i class="bi bi-search"></i>
                </span>
            </div>

            <button class="btn btn-primary btn-add text-nowrap" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Fasilitas
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger mx-4 mt-3"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger mx-4 mt-3">
            <?php foreach (session()->getFlashdata('errors') as $e): ?>
                <div><?= esc($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- ALERT -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success m-2">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <!-- TABLE -->
    <div class="tbl-wrap">
        <table class="data-table" id="tablePj">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama & Email</th>
                    <th>No. HP</th>
                    <th>Spesialisasi</th>
                    <th>Gaji Bulanan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pj_list)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: #6c757d;">
                            Data PJ tidak ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pj_list as $i => $pj): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= esc($pj['nama']) ?></strong><br>
                                <small class="text-muted"><?= esc($pj['email'] ?? '-') ?></small>
                            </td>
                            <td><?= esc($pj['phone']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= esc($pj['spesialisasi'] ?? '-') ?></span></td>
                            <td>Rp <?= number_format($pj['gaji_bulanan'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-<?= $pj['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $pj['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; gap:.35rem;">
                                    <!-- Tombol Edit -->
                                    <button class="action-btn edit" data-bs-toggle="modal" data-bs-target="#modalEditPj<?= $pj['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <!-- Tombol Bayar Gaji -->
                                    <button class="action-btn edit" data-bs-toggle="modal" data-bs-target="#modalBayarGaji<?= $pj['id'] ?>">
                                        <i class="bi bi-cash-stack"></i>
                                    </button>

                                    <!-- Tombol Riwayat Gaji -->
                                    <a href="/admin/pj/riwayat-gaji/<?= $pj['id'] ?>" class="action-btn edit" title="Lihat Riwayat Gaji">
                                        <i class="bi bi-clock-history"></i>
                                    </a>

                                    <!-- Toggle Status -->
                                    <a href="/admin/pj/toggle-status/<?= $pj['id'] ?>"
                                        class="action-btn <?= $pj['is_active'] ? 'del' : 'edit' ?>"
                                        onclick="return confirm('Ubah status PJ ini?')">
                                        <i class="bi bi-power"></i>
                                    </a>
                                    <!-- RESET PASSWORD -->
                                    <a href="/admin/pj/reset-password/<?= $pj['id'] ?>"
                                        class="action-btn"
                                        onclick="return confirm('Reset password PJ?')">
                                        <i class="bi bi-key"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============= MODAL TAMBAH ============= -->
<div class="modal fade" id="modalTambahPj" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title d-flex align-items-center">
                    <span class="modal-icon add"><i class="bi bi-person-plus-fill"></i></span>
                    Tambah PJ Baru
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/pj/store" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Spesialisasi</label>
                        <input type="text" name="spesialisasi" class="form-control" placeholder="Contoh: AC, Listrik">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gaji Bulanan</label>
                        <input type="number" name="gaji_bulanan" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-primary-custom">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============= MODAL EDIT (FOREACH) ============= -->
<?php foreach ($pj_list as $pj): ?>
    <div class="modal fade" id="modalEditPj<?= $pj['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title d-flex align-items-center">
                        <span class="modal-icon add" style=""><i class="bi bi-pencil-fill"></i></span>
                        Edit Penanggung Jawab
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/admin/pj/update/<?= $pj['id'] ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= esc($pj['nama']) ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= esc($pj['email'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="phone" class="form-control" value="<?= esc($pj['phone']) ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Spesialisasi</label>
                            <input type="text" name="spesialisasi" class="form-control" value="<?= esc($pj['spesialisasi'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gaji Bulanan</label>
                            <input type="number" name="gaji_bulanan" class="form-control" value="<?= $pj['gaji_bulanan'] ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-primary-custom" style="">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============= MODAL BAYAR GAJI (FOREACH) ============= -->
    <div class="modal fade" id="modalBayarGaji<?= $pj['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title d-flex align-items-center">
                        <span class="modal-icon add" style=""><i class="bi bi-cash-coin"></i></span>
                        Bayar Gaji: <?= esc($pj['nama']) ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/admin/pj/bayar-gaji/<?= $pj['id'] ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bulan</label>
                                <select name="bulan" class="form-select">
                                    <?php
                                    $bulanList = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                                    foreach ($bulanList as $val => $label): ?>
                                        <option value="<?= $val ?>" <?= date('m') == $val ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun</label>
                                <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah (Rp)</label>
                            <input type="number" name="jumlah" class="form-control" value="<?= $pj['gaji_bulanan'] ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-primary-custom" style="">Konfirmasi Bayar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchInput = document.getElementById('searchPj');
        const table = document.getElementById('tablePj');
        const rows = table.querySelectorAll('tbody tr');

        searchInput.addEventListener('keyup', function() {

            const keyword = this.value.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();

                if (text.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

        });
    });
</script>
<?= $this->endSection() ?>