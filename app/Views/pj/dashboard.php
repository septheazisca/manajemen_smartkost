<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="mb-4">
        <h4 class="fw-bold">Dashboard</h4>
        <p class="text-muted">Selamat datang, <?= esc($pj['nama']) ?>!</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-4">
                    <h2 class="fw-bold text-primary"><?= $total_tugas ?></h2>
                    <p class="text-muted mb-0">Total Tugas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-4">
                    <h2 class="fw-bold text-warning"><?= $tugas_proses ?></h2>
                    <p class="text-muted mb-0">Sedang Dikerjakan</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-4">
                    <h2 class="fw-bold text-success"><?= $tugas_selesai ?></h2>
                    <p class="text-muted mb-0">Selesai</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-bold d-flex justify-content-between">
            Riwayat Gaji
        </div>
        <div class="card-body">
            <?php if (empty($riwayat_gaji)): ?>
                <p class="text-muted text-center">Belum ada riwayat pembayaran gaji.</p>
            <?php else: ?>
            <?php
            $bulanList = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
            ?>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Jumlah</th>
                        <th>Tanggal Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riwayat_gaji as $i => $g): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= $bulanList[str_pad($g['bulan'], 2, '0', STR_PAD_LEFT)] ?? $g['bulan'] ?></td>
                        <td><?= $g['tahun'] ?></td>
                        <td>Rp <?= number_format($g['jumlah'], 0, ',', '.') ?></td>
                        <td><?= date('d M Y', strtotime($g['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>