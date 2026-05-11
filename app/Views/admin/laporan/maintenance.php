<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <a href="/admin/laporan">Laporan</a>
    <i class="bi bi-chevron-right"></i>
    <span>Maintenance</span>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Laporan Maintenance</h4>
        <p class="text-muted small mb-0">Rekapitulasi perbaikan dan pemeliharaan fasilitas</p>
    </div>
    <a href="/admin/laporan" class="btn-cancel border p-2 px-3 text-dark" style="text-decoration: none;">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- Summary Cards -->
<div class="row mb-4 g-3">
    <div class="col-md-3">
        <div class="table-card p-3 border-start border-warning border-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0 small fw-bold">MENUNGGU</p>
                    <h3 class="fw-bold mb-0"><?= $total_menunggu ?></h3>
                </div>
                <i class="bi bi-clock-history fs-3 text-warning opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="table-card p-3 border-start border-info border-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0 small fw-bold">PROSES</p>
                    <h3 class="fw-bold mb-0"><?= $total_proses ?></h3>
                </div>
                <i class="bi bi-gear-wide-connected fs-3 text-info opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="table-card p-3 border-start border-success border-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0 small fw-bold">SELESAI</p>
                    <h3 class="fw-bold mb-0"><?= $total_selesai ?></h3>
                </div>
                <i class="bi bi-check2-circle fs-3 text-success opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="table-card p-3 border-start border-danger border-2 bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0 small fw-bold">TOTAL BIAYA</p>
                    <h4 class="fw-bold mb-0 text-danger">Rp <?= number_format($total_biaya, 0, ',', '.') ?></h4>
                </div>
                <i class="bi bi-cash-stack fs-3 text-danger opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Table Data -->
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">Riwayat Maintenance</div>
        <div class="table-card-sub">Daftar seluruh pengajuan perbaikan dari penghuni</div>
    </div>
    <div class="tbl-wrap">
        <table class="data-table" id="tableLaporanMaintenance">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Detail Lokasi</th>
                    <th>Deskripsi Kendala</th>
                    <th>Penanggung Jawab</th>
                    <th>Biaya</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($maintenance as $i => $m): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-bold"><?= esc($m['nama_penyewa'] ?? '-') ?></div>
                            <div class="text-muted small">Kamar: <?= esc($m['nomor_kamar'] ?? '-') ?></div>
                        </td>
                        <td>
                            <div class="small text-wrap" style="max-width: 250px;">
                                <?= esc($m['deskripsi']) ?>
                            </div>
                        </td>
                        <td>
                            <span class="text-dark small"><i class="bi bi-person-badge me-1"></i><?= esc($m['nama_pj'] ?? '-') ?></span>
                        </td>
                        <td class="fw-bold text-dark">
                            <?= $m['biaya'] ? 'Rp ' . number_format($m['biaya'], 0, ',', '.') : '<span class="text-muted fw-normal">-</span>' ?>
                        </td>
                        <td>
                            <?php
                            $badge = match ($m['status']) {
                                'menunggu' => 'bg-warning',
                                'proses'   => 'bg-info',
                                'selesai'  => 'bg-success',
                                default    => 'bg-secondary',
                            };
                            ?>
                            <span class="badge <?= $badge ?> text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                <?= ucfirst($m['status']) ?>
                            </span>
                        </td>
                        <td class="small">
                            <div class="text-dark"><?= date('d/m/Y', strtotime($m['created_at'])) ?></div>
                            <div class="text-muted" style="font-size: 10px;"><?= date('H:i', strtotime($m['created_at'])) ?> WIB</div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tableLaporanMaintenance').DataTable({
            "language": {
                "search": "Cari Laporan:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Tidak ada data ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Data tidak tersedia",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": '<i class="bi bi-chevron-right"></i>',
                    "previous": '<i class="bi bi-chevron-left"></i>'
                },
            },
            "dom": '<"d-flex justify-content-between align-items-center p-3"<"small"l><"small"f>>rt<"d-flex justify-content-between align-items-center p-3"<"small"i><"small"p>>'
        });
    });
</script>

<?= $this->endSection() ?>