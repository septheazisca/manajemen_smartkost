<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <i class="bi bi-house"></i> Dashboard
</div>

<!-- STAT CARDS ROW 1 -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #f0e6ff;">
                <i class="bi bi-door-open" style="color: #C484F5;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Kamar</div>
                <div class="stat-value"><?= $total_kamar ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e6f4ff;">
                <i class="bi bi-people" style="color: #3b82f6;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Penyewa</div>
                <div class="stat-value"><?= $total_penyewa ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff7e6;">
                <i class="bi bi-receipt" style="color: #f59e0b;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Tagihan Pending</div>
                <div class="stat-value"><?= $tagihan_pending ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff0f0;">
                <i class="bi bi-tools" style="color: #ef4444;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Maintenance</div>
                <div class="stat-value"><?= $maintenance_pending ?></div>
            </div>
        </div>
    </div>
</div>

<!-- STAT CARDS ROW 2 -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e6fff4;">
                <i class="bi bi-check-circle" style="color: #10b981;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Kamar Terisi</div>
                <div class="stat-value"><?= $kamar_terisi ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #f0e6ff;">
                <i class="bi bi-door-closed" style="color: #C484F5;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Kamar Kosong</div>
                <div class="stat-value"><?= $kamar_kosong ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff0f0;">
                <i class="bi bi-exclamation-triangle" style="color: #ef4444;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Menunggak</div>
                <div class="stat-value"><?= $tagihan_menunggak ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: #e6fff4;">
                <i class="bi bi-cash-stack" style="color: #10b981;"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Pemasukan Bulan Ini</div>
                <div class="stat-value" style="font-size: 16px;">
                    Rp <?= number_format($pemasukan_bulan_ini, 0, ',', '.') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CHART ROW 1 -->
<div class="row g-3 mb-4">

    <!-- Chart Pemasukan vs Pengeluaran -->
    <div class="col-md-9">
        <div class="table-card" style="padding: 1.5rem;">
            <div class="table-card-header" style="padding: 0 0 1rem 0;">
                <div>
                    <div class="table-card-title">Pemasukan vs Pengeluaran</div>
                    <div class="table-card-sub">6 bulan terakhir</div>
                </div>
            </div>
            <canvas id="chartKeuangan" height="120"></canvas>
        </div>
    </div>

    <!-- Chart Pie Status Tagihan -->
    <div class="col-md-3">
        <div class="table-card" style="padding: 1.5rem;">
            <div class="table-card-header mb-3" style="padding: 0 0 1rem 0;">
                <div>
                    <div class="table-card-title">Status Tagihan</div>
                    <div class="table-card-sub">Bulan <?= date('F Y') ?></div>
                </div>
            </div>
            <canvas id="chartStatusTagihan" height="200"></canvas>
            <div class="mt-3">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Lunas</small>
                    <small class="fw-bold text-success"><?= $status_tagihan['lunas'] ?></small>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Pending</small>
                    <small class="fw-bold text-warning"><?= $status_tagihan['pending'] ?></small>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Menunggu Konfirmasi</small>
                    <small class="fw-bold text-info"><?= $status_tagihan['menunggu_konfirmasi'] ?></small>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Menunggak</small>
                    <small class="fw-bold text-danger"><?= $status_tagihan['menunggak'] ?></small>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- CHART ROW 2 + TABEL -->
<div class="row g-3 mb-4">

    <!-- Chart Occupancy Kamar -->
    <div class="col-md-2">
        <div class="table-card" style="padding: 1.5rem;">
            <div class="table-card-header mb-3" style="padding: 0 0 1rem 0;">
                <div>
                    <div class="table-card-title">Occupancy Kamar</div>
                    <div class="table-card-sub">Terisi vs Kosong</div>
                </div>
            </div>
            <canvas id="chartKamar" height="200"></canvas>
            <div class="mt-3">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Terisi</small>
                    <small class="fw-bold"><?= $kamar_terisi ?> kamar</small>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Kosong</small>
                    <small class="fw-bold"><?= $kamar_kosong ?> kamar</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Penyewa Sering Menunggak -->
    <div class="col-md-10">
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">Penyewa Sering Menunggak</div>
                    <div class="table-card-sub">Berdasarkan jumlah tunggakan</div>
                </div>
                <a href="/admin/laporan/tagihan" class="btn-add" style="font-size: 12px; padding: 6px 12px;">
                    Lihat Laporan
                </a>
            </div>
            <div class="tbl-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Penyewa</th>
                            <th>Kamar</th>
                            <th>Jumlah Tunggakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sering_menunggak)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">
                                    <i class="bi bi-check-circle text-success"></i> Tidak ada penyewa yang menunggak
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sering_menunggak as $i => $p): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td class="fw-bold"><?= esc($p['name']) ?></td>
                                    <td>Kamar <?= esc($p['nomor_kamar']) ?></td>
                                    <td>
                                        <span class="badge bg-danger"><?= $p['jumlah_tunggakan'] ?> kali</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- TABEL ROW BOTTOM -->
<div class="row g-3">

    <!-- Tagihan Menunggu Konfirmasi -->
    <div class="col-md-6">
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">Menunggu Konfirmasi</div>
                    <div class="table-card-sub">Pembayaran yang perlu diverifikasi</div>
                </div>
                <a href="/admin/tagihan" class="btn-add" style="font-size: 12px; padding: 6px 12px;">
                    Lihat Semua
                </a>
            </div>
            <div class="tbl-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Penyewa</th>
                            <th>Kamar</th>
                            <th>Bulan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pembayaran_pending)): ?>
                            <?php foreach ($pembayaran_pending as $p): ?>
                                <tr>
                                    <td><?= esc($p['name']) ?></td>
                                    <td>Kamar <?= esc($p['nomor_kamar']) ?></td>
                                    <td><?= esc($p['bulan']) ?>/<?= esc($p['tahun']) ?></td>
                                    <td>
                                        <a href="/admin/tagihan/<?= $p['tagihan_id'] ?>" class="action-btn edit">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada pembayaran pending</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Maintenance Terbaru -->
    <div class="col-md-6">
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">Laporan Maintenance</div>
                    <div class="table-card-sub">Komplain terbaru dari penyewa</div>
                </div>
                <a href="/admin/maintenance" class="btn-add" style="font-size: 12px; padding: 6px 12px;">
                    Lihat Semua
                </a>
            </div>
            <div class="tbl-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Penyewa</th>
                            <th>Kamar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($maintenance_terbaru)): ?>
                            <?php foreach (array_slice($maintenance_terbaru, 0, 5) as $m): ?>
                                <tr>
                                    <td><?= esc($m['nama_penyewa']) ?></td>
                                    <td>Kamar <?= esc($m['nomor_kamar']) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = match ($m['status']) {
                                            'menunggu' => 'warning',
                                            'proses'   => 'info',
                                            'selesai'  => 'success',
                                            default    => 'secondary',
                                        };
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>">
                                            <?= ucfirst($m['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/admin/maintenance/<?= $m['id'] ?>" class="action-btn edit">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada laporan maintenance</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const isDark = document.documentElement.classList.contains('dark') ||
        window.matchMedia('(prefers-color-scheme: dark)').matches;

    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#a0a0a0' : '#6c757d';

    // =====================
    // Chart Pemasukan vs Pengeluaran
    // =====================
    new Chart(document.getElementById('chartKeuangan'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($label_bulan) ?>,
            datasets: [{
                    label: 'Pemasukan',
                    data: <?= json_encode($pemasukan_bulanan) ?>,
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 6,
                },
                {
                    label: 'Pengeluaran',
                    data: <?= json_encode($pengeluaran_bulanan) ?>,
                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    borderColor: '#ef4444',
                    borderWidth: 1,
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: textColor
                    }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: textColor
                    },
                    grid: {
                        color: gridColor
                    }
                },
                y: {
                    ticks: {
                        color: textColor,
                        callback: val => 'Rp ' + (val / 1000000).toFixed(1) + 'jt'
                    },
                    grid: {
                        color: gridColor
                    }
                }
            }
        }
    });

    // =====================
    // Chart Pie Status Tagihan
    // =====================
    new Chart(document.getElementById('chartStatusTagihan'), {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Pending', 'Menunggu Konfirmasi', 'Menunggak'],
            datasets: [{
                data: [
                    <?= $status_tagihan['lunas'] ?>,
                    <?= $status_tagihan['pending'] ?>,
                    <?= $status_tagihan['menunggu_konfirmasi'] ?>,
                    <?= $status_tagihan['menunggak'] ?>
                ],
                backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // =====================
    // Chart Donut Occupancy Kamar
    // =====================
    new Chart(document.getElementById('chartKamar'), {
        type: 'doughnut',
        data: {
            labels: ['Terisi', 'Kosong'],
            datasets: [{
                data: [<?= $kamar_terisi ?>, <?= $kamar_kosong ?>],
                backgroundColor: ['#C484F5', '#e5e7eb'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>

<?= $this->endSection() ?>