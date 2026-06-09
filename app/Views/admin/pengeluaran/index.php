<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Data Pengeluaran</span>
</div>

<!-- Filter Box -->
<div class="table-card mb-4" style="padding: 1.5rem;">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label fw-bold">Bulan</label>
            <select name="bulan" class="form-select">
                <?php foreach ($list_bulan as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $bulan == $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label fw-bold">Tahun</label>
            <input type="number" name="tahun" class="form-control" value="<?= $tahun ?>">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn-primary-custom w-100" style="height: 38px;">
                <i class="bi bi-filter"></i> Filter
            </button>
        </div>
        <div class="col-md-1">
            <a href="/admin/pengeluaran/rekap?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-cancel w-100 d-flex align-items-center justify-content-center" style="height: 38px; text-decoration: none;">
                <i class="bi bi-file-earmark-text me-1"></i> Rekap
            </a>
        </div>
    </form>
</div>

<!-- Summary Card -->
<div class="table-card mb-4" style="padding: 1.5rem; border-left: 5px solid #d51717;">
    <p class="text-muted mb-1">Total Pengeluaran <?= $list_bulan[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?> <?= $tahun ?></p>
    <h3 class="fw-bold text-danger mb-0">Rp <?= number_format($total, 0, ',', '.') ?></h3>
</div>

<div class="table-card">
    <!-- Header -->
    <div class="table-card-header">
        <div>
            <div class="table-card-title">List Transaksi Pengeluaran</div>
            <div class="table-card-sub">Daftar semua pengeluaran operasional</div>
        </div>
        <div class="toolbar">
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-lg"></i> Tambah Pengeluaran
            </button>
        </div>
    </div>

    <!-- TABLE -->
    <div class="tbl-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Keterangan</th>
                    <th>Kategori</th>
                    <th>PJ Terkait</th>
                    <th>Jumlah</th>
                    <th>Sumber</th>
                    <th>Tanggal Input</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pengeluaran)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: #6c757d;">
                            <i class="bi bi-info-circle" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                            Belum ada data pengeluaran di bulan ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pengeluaran as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-bold"><?= esc($p['keterangan']) ?></td>
                            <td>
                                <?php
                                $badge = match ($p['kategori']) {
                                    'maintenance' => 'info',
                                    'gaji'        => 'warning',
                                    'lainnya'     => 'secondary',
                                    default       => 'secondary',
                                };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst($p['kategori']) ?></span>
                            </td>
                            <td><?= esc($p['nama_pj'] ?? '-') ?></td>
                            <td class="fw-bold text-danger">Rp <?= number_format($p['jumlah'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($p['maintenance_id']): ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-cpu me-1"></i>Otomatis</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border"><i class="bi bi-pencil me-1"></i>Manual</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small><?= date('d/m/Y', strtotime($p['created_at'])) ?></small><br>
                                <small class="text-muted"><?= date('H:i', strtotime($p['created_at'])) ?> WIB</small>
                            </td>
                            <?php $bisaEdit = (!$p['maintenance_id'] && $p['kategori'] !== 'gaji'); ?>

                            <td>
                                <?php if ($bisaEdit): ?>
                                    <button class="action-btn edit" data-bs-toggle="modal" data-bs-target="#editModal<?= $p['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="action-btn del btn-delete-pengeluaran"
                                        data-url="/admin/pengeluaran/delete/<?= $p['id'] ?>"
                                        data-nama="<?= $p['keterangan'] ?>">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted small">🔒 Dikunci</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============= MODAL TAMBAH ============= -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title d-flex align-items-center">
                    <span class="modal-icon add"><i class="bi bi-plus-circle-fill"></i></span>
                    Tambah Pengeluaran
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/pengeluaran/store" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Bayar Listrik" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="lainnya" selected>Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah (Rp)</label>
                            <input type="number" name="jumlah" class="form-control" placeholder="0" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select">
                                <?php foreach ($list_bulan as $val => $label): ?>
                                    <option value="<?= $val ?>" <?= $bulan == $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="<?= $tahun ?>">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Penanggung Jawab (Opsional)</label>
                        <select name="pj_id" class="form-select">
                            <option value="">-- Tanpa PJ --</option>
                            <?php foreach ($pj_list as $pj): ?>
                                <option value="<?= $pj['id'] ?>"><?= esc($pj['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============= MODAL EDIT (FOREACH) ============= -->
<?php foreach ($pengeluaran as $p): ?>
    <?php if (!$p['maintenance_id']): ?>
        <div class="modal fade" id="editModal<?= $p['id'] ?>">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title d-flex align-items-center">
                            <span class="modal-icon add"><i class="bi bi-pencil-fill"></i></span>
                            Edit Pengeluaran
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="/admin/pengeluaran/update/<?= $p['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" value="<?= esc($p['keterangan']) ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select name="kategori" class="form-select" required>
                                        <option value="lainnya" <?= $p['kategori'] == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jumlah (Rp)</label>
                                    <input type="number" name="jumlah" class="form-control" value="<?= $p['jumlah'] ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bulan</label>
                                    <select name="bulan" class="form-select">
                                        <?php foreach ($list_bulan as $val => $label): ?>
                                            <option value="<?= $val ?>" <?= $p['bulan'] == $val ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tahun</label>
                                    <input type="number" name="tahun" class="form-control" value="<?= $p['tahun'] ?>">
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Penanggung Jawab (Opsional)</label>
                                <select name="pj_id" class="form-select">
                                    <option value="">-- Tanpa PJ --</option>
                                    <?php foreach ($pj_list as $pj): ?>
                                        <option value="<?= $pj['id'] ?>" <?= ($p['pj_id'] == $pj['id']) ? 'selected' : '' ?>><?= esc($pj['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert Delete
        const deleteButtons = document.querySelectorAll('.btn-delete-pengeluaran');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                const nama = this.getAttribute('data-nama');
                Swal.fire({
                    title: 'Hapus Pengeluaran?',
                    text: `"${nama}" akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d51717',
                    cancelButtonColor: '#175fd4',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    });
</script>

<?= $this->endSection() ?>