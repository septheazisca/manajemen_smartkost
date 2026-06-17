<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .custom-filter-group {
        border-radius: 8px !important;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease-in-out;
    }

    .custom-filter-group:focus-within {
        border-color: #C484F5;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .custom-filter-group .input-group-text,
    .custom-filter-group .form-select,
    .custom-filter-group .btn {
        border: none !important;
        box-shadow: none !important;
    }

    .custom-filter-group .form-select:not(:last-child) {
        border-right: 1px solid #f1f5f9 !important;
    }

    .custom-filter-group .input-group-text {
        color: #64748b;
        padding-right: 0.5rem;
    }

    .custom-filter-group .form-select {
        color: #334155;
        font-weight: 500;
        cursor: pointer;
    }
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard">
        <i class="bi bi-house"></i> Dashboard
    </a>
    <i class="bi bi-chevron-right"></i>
    <span>Tagihan</span>
</div>

<!-- CARD -->
<div class="table-card">

    <!-- HEADER -->

    <div class="table-card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 w-100">
        <div class="text-nowrap">
            <div class="table-card-title fw-bold" style="font-size: 1.15rem; color: #1e293b;">Manajemen Tagihan</div>
            <div class="table-card-sub text-muted small">Periode: <?= $list_bulan[$bulan] ?> <?= $tahun ?> — Total <?= count($tagihan) ?> data</div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end" style="max-width: 500px;">
            <div class="input-group flex-grow-1" style="max-width: 260px;">
                <input type="text" id="searchTagihan" class="form-control" placeholder="Cari tagihan...">
                <span class="input-group-text bg-light text-muted">
                    <i class="bi bi-search"></i>
                </span>
            </div>

            <button class="btn btn-primary btn-add text-nowrap" data-bs-toggle="modal" data-bs-target="#generateModal">
                <i class="bi bi-plus-lg me-1"></i> Generate Tagihan
            </button>
        </div>
    </div>

    <!-- FILTER & STATS -->
    <div class="px-3 py-4 border-bottom bg-light-subtle">
        <div class="row g-3 align-items-center">

            <!-- Kode HTML Anda yang sudah disesuaikan kelasnya -->
            <div class="col-12 col-xl-4">
                <form method="get" action="/admin/tagihan" class="d-flex gap-2">
                    <div class="input-group input-group-sm custom-filter-group shadow-sm bg-white">
                        <span class="input-group-text bg-white"><i class="bi bi-calendar3"></i></span>

                        <select name="bulan" class="form-select p-3" style="">
                            <?php foreach ($list_bulan as $k => $v) : ?>
                                <option value="<?= $k ?>" <?= $bulan == $k ? 'selected' : '' ?>>
                                    <?= $v ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="tahun" class="form-select">
                            <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--) : ?>
                                <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
                        </select>

                        <button type="submit" class="btn px-3 d-flex align-items-center" style="background-color: #C484F5 !important; color: white;">
                            <i class="bi bi-funnel-fill me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Stat Cards (Kanan) -->
            <div class="col-12 col-xl-8">
                <div class="row g-2 justify-content-xl-end">

                    <!-- Lunas -->
                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="p-2 border rounded bg-white shadow-sm d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-check2-all"></i>
                            </div>
                            <div>
                                <div class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Lunas</div>
                                <div class="fw-bold lh-1"><?= count(array_filter($tagihan, fn($t) => $t['status'] === 'lunas')) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="p-2 border rounded bg-white shadow-sm d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <div class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Pending</div>
                                <div class="fw-bold lh-1"><?= count(array_filter($tagihan, fn($t) => $t['status'] === 'pending')) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Konfirmasi -->
                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="p-2 border rounded bg-white shadow-sm d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div>
                                <div class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Konf.</div>
                                <div class="fw-bold lh-1"><?= count(array_filter($tagihan, fn($t) => $t['status'] === 'menunggu_konfirmasi')) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Menunggak -->
                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="p-2 border rounded bg-white shadow-sm d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-exclamation-circle"></i>
                            </div>
                            <div>
                                <div class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Tunggakan</div>
                                <div class="fw-bold lh-1"><?= count(array_filter($tagihan, fn($t) => $t['status'] === 'menunggak')) ?></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- ALERT -->
    <?php if (session()->getFlashdata('success') || session()->getFlashdata('error')) : ?>
        <div class="p-3">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success border-0 mb-0">
                    <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger border-0 mb-0">
                    <i class="bi bi-exclamation-octagon me-2"></i><?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- TABLE -->
    <div class="tbl-wrap">
        <table class="data-table" id="tableTagihan">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Penyewa & Kamar</th>
                    <th>Periode</th>
                    <th>Detail Biaya</th>
                    <th>Total Bayar</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tagihan)) : ?>
                    <?php $no = 1; ?>
                    <?php foreach ($tagihan as $t) : ?>
                        <tr>
                            <td class="text-muted small"><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold"><?= esc($t['nama']) ?></div>
                                <div class="text-muted small">Kamar <?= esc($t['nama_kamar']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark fw-normal">
                                    <?= $list_bulan[$t['bulan']] ?? $t['bulan'] ?> <?= $t['tahun'] ?>
                                </span>
                            </td>
                            <td class="small">
                                Rp <?= number_format($t['jumlah'], 0, ',', '.') ?> <br>
                                <span class="text-muted">Unik: +<?= str_pad($t['nominal_unik'], 3, '0', STR_PAD_LEFT) ?></span>
                            </td>
                            <td>
                                <strong class="text-primary">
                                    Rp <?= number_format($t['jumlah'] + $t['nominal_unik'], 0, ',', '.') ?>
                                </strong>
                            </td>
                            <td>
                                <div class="<?= (strtotime($t['jatuh_tempo']) < time() && $t['status'] !== 'lunas') ? 'text-danger fw-bold' : '' ?>">
                                    <?= date('d M Y', strtotime($t['jatuh_tempo'])) ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= esc($t['badge_class']) ?> shadow-sm">
                                    <i class="bi <?= esc($t['icon']) ?> me-1"></i><?= esc(ucwords(str_replace('_', ' ', $t['status']))) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="/admin/tagihan/<?= $t['id'] ?>" class="action-btn edit" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <?php if ($t['status'] === 'menunggu_konfirmasi') : ?>
                                        <!-- <button class="action-btn edit bg-success text-white border-0" title="Approve" data-bs-toggle="modal" data-bs-target="#approveModal<?= $t['id'] ?>">
                                            <i class="bi bi-check-lg"></i>
                                        </button> -->
                                        <!-- <button class="action-btn del" title="Tolak" data-bs-toggle="modal" data-bs-target="#tolakModal<?= $t['id'] ?>">
                                            <i class="bi bi-x-lg"></i>
                                        </button> -->
                                    <?php endif; ?>

                                    <?php if ($t['status'] === 'pending') : ?>
                                        <button class="action-btn warning" title="Tandai Menunggak" data-bs-toggle="modal" data-bs-target="#menunggakModal<?= $t['id'] ?>">
                                            <i class="bi bi-exclamation-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox d-block fs-2 mb-2"></i>
                            Belum ada tagihan untuk periode ini
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- ========================= -->
<!-- GENERATE MODAL -->
<!-- ========================= -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/tagihan/generate" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Generate Tagihan Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Tagihan akan digenerate untuk semua penyewa aktif.
                        Penyewa yang sudah memiliki tagihan di periode ini akan dilewati.
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Bulan</label>
                            <select name="bulan" class="form-select" required>
                                <?php foreach ($list_bulan as $k => $v) : ?>
                                    <option value="<?= $k ?>"
                                        <?= date('m') == $k ? 'selected' : '' ?>>
                                        <?= $v ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tahun</label>
                            <select name="tahun" class="form-select" required>
                                <?php for ($y = date('Y'); $y >= date('Y') - 2; $y--) : ?>
                                    <option value="<?= $y ?>"
                                        <?= date('Y') == $y ? 'selected' : '' ?>>
                                        <?= $y ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn text-nowrap" style="background-color: #e0e0e0;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-add text-nowrap">
                        <i class="bi bi-lightning"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================= -->
<!-- APPROVE & TOLAK MODAL -->
<!-- ========================= -->
<?php foreach ($tagihan as $t) : ?>

    <?php if ($t['status'] === 'menunggu_konfirmasi') : ?>

        <!-- APPROVE MODAL -->
        <div class="modal fade" id="approveModal<?= $t['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="/admin/tagihan/approve/<?= $t['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Konfirmasi pembayaran tagihan:</p>
                            <table class="table table-sm">
                                <tr>
                                    <td>Penyewa</td>
                                    <td><strong><?= esc($t['nama']) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Kamar</td>
                                    <td>Kamar <?= esc($t['nama_kamar']) ?></td>
                                </tr>
                                <tr>
                                    <td>Periode</td>
                                    <td><?= $list_bulan[$t['bulan']] ?? $t['bulan'] ?> <?= $t['tahun'] ?></td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>
                                        <strong>
                                            Rp <?= number_format($t['jumlah'] + $t['nominal_unik'], 0, ',', '.') ?>
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                            <div class="mb-3">
                                <label>Catatan Admin (opsional)</label>
                                <textarea name="catatan_admin" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- TOLAK MODAL -->
        <div class="modal fade" id="tolakModal<?= $t['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="/admin/tagihan/tolak/<?= $t['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="modal-header">
                            <h5 class="modal-title">Tolak Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                Pembayaran akan ditolak dan penyewa perlu upload ulang bukti transfer.
                            </div>
                            <div class="mb-3">
                                <label>Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea name="catatan_admin" class="form-control" rows="3" required
                                    placeholder="Contoh: Bukti transfer tidak jelas, nominal tidak sesuai, dll"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-lg"></i> Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <!-- MENUNGGAK MODAL -->
    <?php if ($t['status'] === 'pending') : ?>
        <div class="modal fade" id="menunggakModal<?= $t['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="/admin/tagihan/tandai-menunggak/<?= $t['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="modal-header">
                            <h5 class="modal-title">Tandai Menunggak</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                Tagihan <strong><?= esc($t['nama']) ?></strong> periode
                                <?= $list_bulan[$t['bulan']] ?? $t['bulan'] ?> <?= $t['tahun'] ?>
                                akan ditandai sebagai menunggak.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-exclamation-triangle"></i> Tandai Menunggak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php endforeach; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchInput = document.getElementById('searchTagihan');
        const table = document.getElementById('tableTagihan');
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