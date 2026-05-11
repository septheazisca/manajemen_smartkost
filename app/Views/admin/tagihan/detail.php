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

<div class="row g-4">

    <!-- INFO TAGIHAN & PENYEWA -->
    <div class="col-xl-4 col-md-5">
        <div class="table-card border-0 shadow-sm">
            <div class="table-card-header bg-white">
                <div class="table-card-title">Ringkasan Tagihan</div>
                <div class="badge rounded-pill bg-primary-subtle text-primary px-3">
                    ID #<?= $tagihan['id'] ?>
                </div>
            </div>

            <!-- Status Visual Besar -->
            <div class="p-4 text-center border-bottom bg-light-subtle">
                <?php
                $statusConfig = [
                    'pending'             => ['class' => 'bg-warning', 'text' => 'text-warning', 'label' => 'Menunggu Pembayaran', 'icon' => 'bi-clock'],
                    'menunggu_konfirmasi' => ['class' => 'bg-info',    'text' => 'text-info',    'label' => 'Perlu Konfirmasi',   'icon' => 'bi-shield-exclamation'],
                    'lunas'               => ['class' => 'bg-success', 'text' => 'text-success', 'label' => 'Sudah Lunas',        'icon' => 'bi-check-circle-fill'],
                    'menunggak'           => ['class' => 'bg-danger',  'text' => 'text-danger',  'label' => 'Terlambat/Menunggak', 'icon' => 'bi-exclamation-octagon-fill'],
                ];
                $cfg = $statusConfig[$tagihan['status']] ?? ['class' => 'bg-secondary', 'text' => 'text-secondary', 'label' => $tagihan['status'], 'icon' => 'bi-info-circle'];
                ?>
                <div class="display-6 <?= $cfg['text'] ?> mb-2"><i class="bi <?= $cfg['icon'] ?>"></i></div>
                <h5 class="fw-bold mb-1"><?= $cfg['label'] ?></h5>
                <p class="text-muted small mb-0">Periode <?= esc($tagihan['bulan']) ?>/<?= esc($tagihan['tahun']) ?></p>
            </div>

            <div class="p-3">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <span class="text-muted"><i class="bi bi-person me-2"></i>Penyewa</span>
                        <span class="fw-bold"><?= esc($tagihan['nama']) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <span class="text-muted"><i class="bi bi-door-closed me-2"></i>Kamar</span>
                        <span class="fw-semibold">Kamar <?= esc($tagihan['nama_kamar']) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <span class="text-muted"><i class="bi bi-whatsapp me-2"></i>WhatsApp</span>
                        <a href="https://wa.me/<?= $tagihan['phone'] ?>" target="_blank" class="text-decoration-none"><?= esc($tagihan['phone']) ?></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <span class="text-muted"><i class="bi bi-calendar-event me-2"></i>Jatuh Tempo</span>
                        <span class="<?= (strtotime($tagihan['jatuh_tempo']) < time() && $tagihan['status'] !== 'lunas') ? 'text-danger fw-bold' : 'fw-semibold' ?>">
                            <?= date('d M Y', strtotime($tagihan['jatuh_tempo'])) ?>
                        </span>
                    </li>
                </ul>

                <!-- Total Box -->
                <div class="mt-3 p-3 rounded bg-primary text-white shadow-sm">
                    <div class="small opacity-75">Total Tagihan:</div>
                    <div class="d-flex justify-content-between align-items-end">
                        <h4 class="mb-0 fw-bold">Rp <?= number_format($tagihan['jumlah'] + $tagihan['nominal_unik'], 0, ',', '.') ?></h4>
                        <div style="font-size: 11px;" class="text-white-50">Incl. unik <?= str_pad($tagihan['nominal_unik'], 3, '0', STR_PAD_LEFT) ?></div>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <a href="/admin/tagihan" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- RIWAYAT PEMBAYARAN -->
    <div class="col-xl-8 col-md-7">
        <div class="table-card border-0 shadow-sm h-100">
            <div class="table-card-header bg-white">
                <div>
                    <div class="table-card-title">Riwayat Transaksi</div>
                    <div class="table-card-sub"><?= count($pembayaran) ?> pembayaran tercatat</div>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success border-0 mx-3 mt-3">
                    <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="tbl-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Waktu Bayar</th>
                            <th>Nominal</th>
                            <th class="text-center">Bukti</th>
                            <th class="text-center">Status</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pembayaran)) : ?>
                            <?php foreach ($pembayaran as $p) : ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold small"><?= date('d M Y', strtotime($p['created_at'])) ?></div>
                                        <div class="text-muted" style="font-size: 11px;"><?= date('H:i', strtotime($p['created_at'])) ?> WIB</div>
                                    </td>
                                    <td>
                                        <span class="text-primary fw-bold">Rp <?= number_format($p['jumlah_bayar'], 0, ',', '.') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($p['bukti_transfer']) : ?>
                                            <a href="/uploads/bukti_transfer/<?= $p['bukti_transfer'] ?>"
                                                target="_blank"
                                                class="btn btn-sm btn-light border"
                                                title="Lihat Bukti">
                                                <i class="bi bi-image text-primary"></i>
                                            </a>
                                        <?php else : ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $stConfig = [
                                            'pending'  => ['class' => 'bg-warning-subtle text-warning border-warning', 'label' => 'Pending'],
                                            'approved' => ['class' => 'bg-success-subtle text-success border-success', 'label' => 'Approved'],
                                            'ditolak'  => ['class' => 'bg-danger-subtle text-danger border-danger',   'label' => 'Ditolak'],
                                        ];
                                        $sc = $stConfig[$p['status']] ?? ['class' => 'bg-secondary-subtle text-secondary', 'label' => $p['status']];
                                        ?>
                                        <span class="badge <?= $sc['class'] ?> border px-2 fw-normal">
                                            <?= $sc['label'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-muted small" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?= $p['catatan_admin'] ? esc($p['catatan_admin']) : '<span class="opacity-50 italic">Tidak ada catatan</span>' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <?php if ($p['status'] === 'pending') : ?>
                                                <button class="action-btn edit bg-success text-white border-0"
                                                    data-bs-toggle="modal" data-bs-target="#approveModal<?= $p['id'] ?>">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button class="action-btn del"
                                                    data-bs-toggle="modal" data-bs-target="#tolakModal<?= $p['id'] ?>">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            <?php else : ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-cash-stack fs-1 opacity-25"></i>
                                        <p class="mt-2">Belum ada riwayat pembayaran untuk tagihan ini.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- MODAL (Isi Tetap Sama, Hanya Sedikit Styling di Header) -->
<?php foreach ($pembayaran as $p) : ?>
    <?php if ($p['status'] === 'pending') : ?>
        <!-- APPROVE -->
        <div class="modal fade" id="approveModal<?= $p['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <form action="/admin/tagihan/approve/<?= $p['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title fs-6"><i class="bi bi-shield-check me-2"></i>Konfirmasi Pembayaran</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="text-center mb-4">
                                <p class="text-muted mb-1">Penyewa membayar sebesar</p>
                                <h3 class="fw-bold text-success">Rp <?= number_format($p['jumlah_bayar'], 0, ',', '.') ?></h3>
                            </div>

                            <?php if ($p['bukti_transfer']) : ?>
                                <label class="small text-muted mb-2">Bukti Transfer:</label>
                                <div class="mb-3 text-center p-2 border rounded bg-light">
                                    <img src="/uploads/bukti_transfer/<?= $p['bukti_transfer'] ?>"
                                        class="img-fluid rounded shadow-sm" style="max-height: 250px;" alt="Bukti">
                                </div>
                            <?php endif; ?>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted">Catatan Admin (Opsional)</label>
                                <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Contoh: Pembayaran sesuai nominal..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-success px-4">Terima Pembayaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- TOLAK (Disesuaikan agar seimbang dengan Approve) -->
        <div class="modal fade" id="tolakModal<?= $p['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <form action="/admin/tagihan/tolak/<?= $p['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fs-6"><i class="bi bi-x-octagon me-2"></i>Tolak Pembayaran</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="alert alert-danger border-0 small">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Penolakan akan menghapus status pembayaran ini dan penyewa harus mengirim ulang bukti yang benar.
                            </div>
                            <div class="mb-0 mt-3">
                                <label class="form-label small fw-bold text-muted text-danger">Alasan Penolakan *</label>
                                <textarea name="catatan_admin" class="form-control" rows="3" required
                                    placeholder="Sebutkan alasan agar penyewa paham (misal: nominal kurang, bukti blur)"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-danger px-4">Tolak Sekarang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?= $this->endSection() ?>