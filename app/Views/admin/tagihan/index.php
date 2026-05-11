<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

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
    <div class="table-card-header">
        <div>
            <div class="table-card-title">Manajemen Tagihan</div>
            <div class="table-card-sub">Total <?= count($tagihan) ?> tagihan</div>
        </div>
        <div class="toolbar">
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#generateModal">
                <i class="bi bi-plus-lg"></i> Generate Tagihan
            </button>
        </div>
    </div>

    <!-- FILTER -->
    <div class="px-3 py-2 d-flex gap-2 align-items-center flex-wrap">
        <form method="get" action="/admin/tagihan" class="d-flex gap-2 align-items-center flex-wrap">
            <select name="bulan" class="form-select form-select-sm" style="width: auto;">
                <?php foreach ($list_bulan as $k => $v) : ?>
                    <option value="<?= $k ?>" <?= $bulan == $k ? 'selected' : '' ?>>
                        <?= $v ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="tahun" class="form-select form-select-sm" style="width: auto;">
                <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--) : ?>
                    <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>>
                        <?= $y ?>
                    </option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-filter"></i> Filter
            </button>
        </form>
    </div>

    <!-- ALERT -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success mx-3">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger mx-3">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- STAT MINI -->
    <div class="d-flex gap-3 px-3 py-2 flex-wrap">
        <div class="stat-mini">
            <span class="stat-mini-label">Lunas</span>
            <span class="stat-mini-value text-success">
                <?= count(array_filter($tagihan, fn($t) => $t['status'] === 'lunas')) ?>
            </span>
        </div>
        <div class="stat-mini">
            <span class="stat-mini-label">Pending</span>
            <span class="stat-mini-value text-warning">
                <?= count(array_filter($tagihan, fn($t) => $t['status'] === 'pending')) ?>
            </span>
        </div>
        <div class="stat-mini">
            <span class="stat-mini-label">Menunggu Konfirmasi</span>
            <span class="stat-mini-value text-info">
                <?= count(array_filter($tagihan, fn($t) => $t['status'] === 'menunggu_konfirmasi')) ?>
            </span>
        </div>
        <div class="stat-mini">
            <span class="stat-mini-label">Menunggak</span>
            <span class="stat-mini-value text-danger">
                <?= count(array_filter($tagihan, fn($t) => $t['status'] === 'menunggak')) ?>
            </span>
        </div>
    </div>

    <!-- TABLE -->
    <div class="tbl-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Penyewa</th>
                    <th>Kamar</th>
                    <th>Periode</th>
                    <th>Jumlah</th>
                    <th>Kode Unik</th>
                    <th>Total Bayar</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tagihan)) : ?>
                    <?php $no = 1; ?>
                    <?php foreach ($tagihan as $t) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($t['nama']) ?></td>
                            <td>Kamar <?= esc($t['nama_kamar']) ?></td>
                            <td><?= $list_bulan[$t['bulan']] ?? $t['bulan'] ?> <?= $t['tahun'] ?></td>
                            <td>Rp <?= number_format($t['jumlah'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-secondary">
                                    +<?= str_pad($t['nominal_unik'], 3, '0', STR_PAD_LEFT) ?>
                                </span>
                            </td>
                            <td>
                                <strong>
                                    Rp <?= number_format($t['jumlah'] + $t['nominal_unik'], 0, ',', '.') ?>
                                </strong>
                            </td>
                            <td>
                                <?= date('d M Y', strtotime($t['jatuh_tempo'])) ?>
                            </td>
                            <td>
                                <?php
                                $statusConfig = [
                                    'pending'              => ['class' => 'bg-warning text-dark', 'label' => 'Pending'],
                                    'menunggu_konfirmasi'  => ['class' => 'bg-info text-dark',    'label' => 'Menunggu Konfirmasi'],
                                    'lunas'                => ['class' => 'bg-success',            'label' => 'Lunas'],
                                    'menunggak'            => ['class' => 'bg-danger',             'label' => 'Menunggak'],
                                ];
                                $cfg = $statusConfig[$t['status']] ?? ['class' => 'bg-secondary', 'label' => $t['status']];
                                ?>
                                <span class="badge <?= $cfg['class'] ?>">
                                    <?= $cfg['label'] ?>
                                </span>
                            </td>
                            <td>
                                <!-- DETAIL -->
                                <a href="/admin/tagihan/<?= $t['id'] ?>"
                                    class="action-btn edit"
                                    title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- APPROVE (kalau menunggu konfirmasi) -->
                                <?php if ($t['status'] === 'menunggu_konfirmasi') : ?>
                                    <button class="action-btn edit"
                                        title="Approve"
                                        data-bs-toggle="modal"
                                        data-bs-target="#approveModal<?= $t['id'] ?>">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="action-btn del"
                                        title="Tolak"
                                        data-bs-toggle="modal"
                                        data-bs-target="#tolakModal<?= $t['id'] ?>">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                <?php endif; ?>

                                <!-- TANDAI MENUNGGAK (kalau masih pending) -->
                                <?php if ($t['status'] === 'pending') : ?>
                                    <button class="action-btn del"
                                        title="Tandai Menunggak"
                                        data-bs-toggle="modal"
                                        data-bs-target="#menunggakModal<?= $t['id'] ?>">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="10" class="text-center">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
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

<?= $this->endSection() ?>