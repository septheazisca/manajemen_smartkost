<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<!-- Header Welcome -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Dashboard</h4>
        <p class="text-muted small">Selamat datang kembali, <span class="fw-bold text-dark"><?= esc($penyewa['nama'] ?? $penyewa['name'] ?? '-') ?></span>!</p>
    </div>
    <div class="d-none d-md-block text-end">
        <div class="fw-bold small"><?= date('l, d M Y') ?></div>
        <div class="text-muted small" id="clock"></div>
    </div>
</div>

<div class="row g-4">
    <!-- Info Kamar Utama (Highlight) -->
    <div class="col-12 col-xl-8">
        <div class="table-card overflow-hidden" style="border-radius: 15px;">
            <div class="row g-0 h-100">
                <div class="col-md-4 btn-primary-custom d-flex align-items-center justify-content-center py-4">
                    <div class="text-center text-white">
                        <i class="bi bi-door-open" style="font-size: 3.5rem;"></i>
                        <h3 class="fw-bold mb-0 mt-2">Kamar <?= esc($penyewa['nomor_kamar'] ?? '-') ?></h3>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="text-muted small d-block">Harga Sewa</label>
                                <span class="fw-bold">Rp <?= number_format($penyewa['harga'] ?? 0, 0, ',', '.') ?> <small class="fw-normal">/bln</small></span>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="text-muted small d-block">Mulai Sewa</label>
                                <span class="fw-bold"><?= $penyewa['tanggal_masuk'] ? date('d M Y', strtotime($penyewa['tanggal_masuk'])) : '-' ?></span>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="p-3 rounded-3 bg-light-subtle border d-flex align-items-center justify-content-between">
                                    <div class="small"><i class="bi bi-info-circle me-2 text-primary"></i>Status Masa Sewa Aktif</div>
                                    <a href="/tenant/profile" class="btn btn-sm btn-white border shadow-sm px-3">Lihat Profil</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Summary Stats -->
    <div class="col-12 col-xl-4">
        <div class="row g-3">
            <div class="col-6 col-xl-12">
                <div class="table-card d-flex align-items-center justify-content-between p-3" style="border-left: 5px solid #DE6B00;">
                    <div>
                        <div class="text-muted small fw-bold">BELUM LUNAS</div>
                        <h4 class="fw-bold mb-0"><?= count($tagihan_aktif) ?></h4>
                    </div>
                    <div class="rounded-circle bg-warning-subtle text-warning p-2">
                        <i class="bi bi-receipt fs-5"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-12">
                <div class="table-card d-flex align-items-center justify-content-between p-3" style="border-left: 5px solid #00BCDE;">
                    <div>
                        <div class="text-muted small fw-bold">MAINTENANCE</div>
                        <h4 class="fw-bold mb-0"><?= $maintenance_proses ?></h4>
                    </div>
                    <div class="rounded-circle bg-info-subtle text-info p-2">
                        <i class="bi bi-tools fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-card mt-4">
    <!-- Header -->
    <div class="table-card-header d-flax justify-content-between">
        <div class="">
            <div class="table-card-title">Tagihan Perlu Dibayar</div>
            <div class="table-card-sub small">Daftar tagihan yang belum terselesaikan</div>
        </div>
        <a href="/tenant/tagihan" class="btn rounded-pill" style="border: 2px solid var(--primary); font-size: 12px; color: var(--primary);">
            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <!-- TABLE -->
    <div class="tbl-wrap">
        <table class="data-table mb-0">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Total Bayar</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tagihan_aktif)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="mb-2 text-success fs-1"><i class="bi bi-check2-circle"></i></div>
                            <h6 class="fw-bold">Luar Biasa!</h6>
                            <p class="text-muted small">Semua tagihan Anda sudah lunas. Terima kasih!</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $bulanList = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                    foreach ($tagihan_aktif as $t):
                        $statusData = match ($t['status']) {
                            'pending'             => ['class' => 'bg-warning-subtle text-warning border-warning', 'label' => 'Pending'],
                            'menunggu_konfirmasi' => ['class' => 'bg-info-subtle text-info border-info',       'label' => 'Menunggu Konfirmasi'],
                            'menunggak'           => ['class' => 'bg-danger-subtle text-danger border-danger',   'label' => 'Menunggak'],
                            default               => ['class' => 'bg-secondary-subtle text-secondary border-secondary', 'label' => $t['status']],
                        };
                    ?>
                        <tr class="align-middle">
                            <td class="fw-bold">
                                <?= $bulanList[str_pad($t['bulan'], 2, '0', STR_PAD_LEFT)] ?? $t['bulan'] ?> <?= $t['tahun'] ?>
                            </td>
                            <td>
                                <span class="text-primary fw-bold">Rp <?= number_format($t['jumlah'] + $t['nominal_unik'], 0, ',', '.') ?></span>
                                <div class="text-muted" style="font-size: 10px;">Inc. Kode Unik</div>
                            </td>
                            <td>
                                <span class="<?= (strtotime($t['jatuh_tempo']) < time()) ? 'text-danger fw-bold' : '' ?>">
                                    <?= date('d M Y', strtotime($t['jatuh_tempo'])) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $statusData['class'] ?> border px-3 py-2 fw-normal">
                                    <?= $statusData['label'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($t['status'] === 'pending' || $t['status'] === 'menunggak'): ?>
                                    <a href="/tenant/tagihan" class="btn btn-primary-custom btn-sm shadow-sm" style="font-size: 12px;">
                                        Bayar Sekarang
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted fw-normal"><i class="bi bi-hourglass-split me-1"></i>Diproses Admin</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<script>
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('clock').textContent = timeStr + ' WIB';
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

<?= $this->endSection() ?>