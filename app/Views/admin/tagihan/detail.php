<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <a href="/admin/tagihan">Tagihan</a>
    <i class="bi bi-chevron-right"></i>
    <span>Detail Tagihan</span>
</div>

<div class="row g-3">

    <!-- INFO TAGIHAN -->
    <div class="col-md-5">
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">Info Tagihan</div>
                </div>
            </div>
            <div class="p-3">
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">Penyewa</td>
                        <td><strong><?= esc($tagihan['nama']) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">No HP</td>
                        <td><?= esc($tagihan['phone']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kamar</td>
                        <td>Kamar <?= esc($tagihan['nama_kamar']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Periode</td>
                        <td><?= esc($tagihan['bulan']) ?>/<?= esc($tagihan['tahun']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jumlah Sewa</td>
                        <td>Rp <?= number_format($tagihan['jumlah'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kode Unik</td>
                        <td>
                            +<?= str_pad($tagihan['nominal_unik'], 3, '0', STR_PAD_LEFT) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Bayar</td>
                        <td>
                            <strong class="text-primary" style="font-size: 16px;">
                                Rp <?= number_format($tagihan['jumlah'] + $tagihan['nominal_unik'], 0, ',', '.') ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jatuh Tempo</td>
                        <td><?= date('d M Y', strtotime($tagihan['jatuh_tempo'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <?php
                            $statusConfig = [
                                'pending'             => ['class' => 'bg-warning text-dark', 'label' => 'Pending'],
                                'menunggu_konfirmasi' => ['class' => 'bg-info text-dark',    'label' => 'Menunggu Konfirmasi'],
                                'lunas'               => ['class' => 'bg-success',            'label' => 'Lunas'],
                                'menunggak'           => ['class' => 'bg-danger',             'label' => 'Menunggak'],
                            ];
                            $cfg = $statusConfig[$tagihan['status']] ?? ['class' => 'bg-secondary', 'label' => $tagihan['status']];
                            ?>
                            <span class="badge <?= $cfg['class'] ?>">
                                <?= $cfg['label'] ?>
                            </span>
                        </td>
                    </tr>
                </table>

                <a href="/admin/tagihan" class="btn btn-secondary btn-sm mt-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- RIWAYAT PEMBAYARAN -->
    <div class="col-md-7">
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">Riwayat Pembayaran</div>
                    <div class="table-card-sub">
                        <?= count($pembayaran) ?> riwayat ditemukan
                    </div>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success mx-3">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="tbl-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pembayaran)) : ?>
                            <?php foreach ($pembayaran as $p) : ?>
                                <tr>
                                    <td><?= date('d M Y H:i', strtotime($p['created_at'])) ?></td>
                                    <td>Rp <?= number_format($p['jumlah_bayar'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php if ($p['bukti_transfer']) : ?>
                                            <a href="/uploads/bukti_transfer/<?= $p['bukti_transfer'] ?>"
                                                target="_blank"
                                                class="action-btn edit"
                                                title="Lihat Bukti">
                                                <i class="bi bi-image"></i>
                                            </a>
                                        <?php else : ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $stConfig = [
                                            'pending'  => ['class' => 'bg-warning text-dark', 'label' => 'Pending'],
                                            'approved' => ['class' => 'bg-success',            'label' => 'Approved'],
                                            'ditolak'  => ['class' => 'bg-danger',             'label' => 'Ditolak'],
                                        ];
                                        $sc = $stConfig[$p['status']] ?? ['class' => 'bg-secondary', 'label' => $p['status']];
                                        ?>
                                        <span class="badge <?= $sc['class'] ?>">
                                            <?= $sc['label'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $p['catatan_admin'] ? esc($p['catatan_admin']) : '-' ?>
                                    </td>
                                    <td>
                                        <?php if ($p['status'] === 'pending') : ?>
                                            <button class="action-btn edit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#approveModal<?= $p['id'] ?>"
                                                title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="action-btn del"
                                                data-bs-toggle="modal"
                                                data-bs-target="#tolakModal<?= $p['id'] ?>"
                                                title="Tolak">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        <?php else : ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    Belum ada riwayat pembayaran
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- APPROVE & TOLAK MODAL -->
<?php foreach ($pembayaran as $p) : ?>
    <?php if ($p['status'] === 'pending') : ?>

        <!-- APPROVE -->
        <div class="modal fade" id="approveModal<?= $p['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="/admin/tagihan/approve/<?= $p['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="modal-header">
                            <h5 class="modal-title">Approve Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Konfirmasi pembayaran sebesar
                                <strong>Rp <?= number_format($p['jumlah_bayar'], 0, ',', '.') ?></strong>?
                            </p>
                            <?php if ($p['bukti_transfer']) : ?>
                                <div class="mb-3 text-center">
                                    <img src="/uploads/bukti_transfer/<?= $p['bukti_transfer'] ?>"
                                        class="img-fluid rounded"
                                        style="max-height: 200px;"
                                        alt="Bukti Transfer">
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label>Catatan (opsional)</label>
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

        <!-- TOLAK -->
        <div class="modal fade" id="tolakModal<?= $p['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="/admin/tagihan/tolak/<?= $p['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="modal-header">
                            <h5 class="modal-title">Tolak Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                Penyewa perlu upload ulang bukti transfer setelah ditolak.
                            </div>
                            <div class="mb-3">
                                <label>Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea name="catatan_admin" class="form-control" rows="3"
                                    required
                                    placeholder="Contoh: Bukti tidak jelas, nominal tidak sesuai"></textarea>
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
<?php endforeach; ?>

<?= $this->endSection() ?>