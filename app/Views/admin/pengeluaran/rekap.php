<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <a href="/admin/pengeluaran">Data Pengeluaran</a>
    <i class="bi bi-chevron-right"></i>
    <span>Rekap Pengeluaran</span>
</div>

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Rekap Pengeluaran</h4>
        <p class="text-muted small mb-0">Analisis pengeluaran berdasarkan kategori dan periode</p>
    </div>
    <a href="/admin/pengeluaran" class="btn-cancel" style="text-decoration: none;">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- Filter Box -->
<div class="table-card mb-4" style="padding: 1.5rem;">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label fw-bold small text-uppercase">Bulan</label>
            <select name="bulan" class="form-select">
                <?php foreach ($list_bulan as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $bulan == $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label fw-bold small text-uppercase">Tahun</label>
            <input type="number" name="tahun" class="form-control" value="<?= $tahun ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-primary-custom w-100" style="height: 38px;">
                <i class="bi bi-filter"></i> Filter
            </button>
        </div>
    </form>
</div>

<!-- Summary Cards Premium Style -->
<div class="row mb-4 g-3">
    <div class="col-md-3">
        <div class="table-card h-100 p-3 border-start border-danger border-2">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-danger bg-opacity-10 py-1 px-2 me-2">
                    <i class="bi bi-cash-stack text-danger fs-5"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 small fw-bold text-uppercase">Total Pengeluaran</p>
                    <h4 class="fw-bold mb-0 text-danger">Rp <?= number_format($total_semua, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="table-card h-100 p-3 border-start border-info border-2">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-info bg-opacity-10 py-1 px-2 me-2">
                    <i class="bi bi-tools text-info fs-5"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 small fw-bold text-uppercase">Maintenance</p>
                    <h4 class="fw-bold mb-0 text-info">Rp <?= number_format($total_maintenance, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="table-card h-100 p-3 border-start border-warning border-2">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 py-1 px-2 me-2">
                    <i class="bi bi-person-badge text-warning fs-5"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 small fw-bold text-uppercase">Gaji Karyawan</p>
                    <h4 class="fw-bold mb-0 text-warning">Rp <?= number_format($total_gaji, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="table-card h-100 p-3 border-start border-secondary border-2">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-secondary bg-opacity-10 py-1 px-2 me-2">
                    <i class="bi bi-box text-secondary fs-5"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 small fw-bold text-uppercase">Lainnya</p>
                    <h4 class="fw-bold mb-0 text-secondary">Rp <?= number_format($total_lainnya, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="table-card">
    <div class="table-card-header">
        <div>
            <div class="table-card-title">Detail Pengeluaran Transaksi</div>
            <div class="table-card-sub">Periode: <?= $list_bulan[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?> <?= $tahun ?></div>
        </div>
        <div class="toolbar">
             <button onclick="window.print()" class="btn-cancel border">
                <i class="bi bi-printer"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <div class="tbl-wrap">
        <table class="data-table" id="tableRekap">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Keterangan</th>
                    <th>Kategori</th>
                    <th>Penanggung Jawab</th>
                    <th>Jumlah</th>
                    <th>Sumber</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
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
                                <span class="badge bg-light text-info border border-info"><i class="bi bi-cpu me-1"></i>Otomatis</span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border"><i class="bi bi-pencil me-1"></i>Manual</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small><?= date('d/m/Y', strtotime($p['created_at'])) ?></small><br>
                            <small class="text-muted"><?= date('H:i', strtotime($p['created_at'])) ?> WIB</small>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa;">
                    <td colspan="4" class="text-end fw-bold py-3">GRAND TOTAL :</td>
                    <td class="fw-bold text-danger py-3" style="font-size: 1.1rem;">Rp <?= number_format($total_semua, 0, ',', '.') ?></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tableRekap').DataTable({
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Data tidak ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Data tidak tersedia",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "search": "Cari:",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Lanjut",
                    "previous": "Kembali"
                }
            },
            "dom": '<"d-flex justify-content-between align-items-center p-3"<"length-wrap"l><"search-wrap"f>>rt<"d-flex justify-content-between align-items-center p-3"<"info-wrap"i><"paginate-wrap"p>>'
        });
    });
</script>

<?= $this->endSection() ?>