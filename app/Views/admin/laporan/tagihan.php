<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <a href="/admin/laporan">Laporan</a>
    <i class="bi bi-chevron-right"></i>
    <span>Laporan Tagihan</span>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Laporan Tagihan</h4>
        <p class="text-muted small mb-0">Monitoring status pembayaran penghuni periode <?= $bulan ?>/<?= $tahun ?></p>
    </div>
    <a href="/admin/laporan" class="btn-cancel border p-2 px-3 text-dark" style="text-decoration: none;">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- Filter Tools -->
<div class="table-card mb-4" style="padding: 1.5rem;">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label fw-bold small text-uppercase text-muted">Bulan</label>
            <select name="bulan" class="form-select border-0 bg-light">
                <?php foreach ($list_bulan as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $bulan == $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label fw-bold small text-uppercase text-muted">Tahun</label>
            <input type="number" name="tahun" class="form-control border-0 bg-light" value="<?= $tahun ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-primary-custom w-100" style="height: 40px;">
                <i class="bi bi-funnel"></i> Terapkan
            </button>
        </div>
    </form>
</div>

<!-- Summary Status -->
<div class="row mb-4 g-3">
    <div class="col-md-4">
        <div class="table-card p-4 text-center border-bottom border-success border-2">
            <p class="text-muted mb-1 small fw-bold">TOTAL LUNAS</p>
            <h2 class="fw-bold text-success mb-0"><?= $total_lunas ?></h2>
            <div class="text-muted small mt-1">Transaksi Berhasil</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card p-4 text-center border-bottom border-warning border-2">
            <p class="text-muted mb-1 small fw-bold">PENDING / KONFIRMASI</p>
            <h2 class="fw-bold text-warning mb-0"><?= $total_pending ?></h2>
            <div class="text-muted small mt-1">Perlu Verifikasi</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="table-card p-4 text-center border-bottom border-danger border-2">
            <p class="text-muted mb-1 small fw-bold">MENUNGGAK</p>
            <h2 class="fw-bold text-danger mb-0"><?= $total_menunggak ?></h2>
            <div class="text-muted small mt-1">Belum Bayar</div>
        </div>
    </div>
</div>

<!-- Main Table -->
<div class="table-card">
    <div class="table-card-header">
        <div>
            <div class="table-card-title">Daftar Tagihan Kamar</div>
            <div class="table-card-sub">Data tagihan per periode berdasarkan jatuh tempo</div>
        </div>
        <div class="toolbar">
            <a href="/admin/tagihan/export-excel?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
                class="btn-cancel border">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>
    </div>
    <div class="tbl-wrap">
        <table class="data-table" id="tableLaporanTagihan">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Penyewa & Kamar</th>
                    <th>Nominal Tagihan</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tagihan as $i => $t): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?= esc($t['nama'] ?? '-') ?></div>
                            <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">Kamar: <?= esc($t['nama_kamar'] ?? '-') ?></span>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">Rp <?= number_format($t['jumlah'], 0, ',', '.') ?></div>
                        </td>
                        <td>
                            <div class="small"><i class="bi bi-calendar-event me-1 text-muted"></i><?= date('d M Y', strtotime($t['jatuh_tempo'])) ?></div>
                        </td>
                        <td>
                            <span class="badge <?= esc($t['badge_class']) ?>">
                                <i class="bi <?= esc($t['icon']) ?> me-1"></i><?= esc(ucwords(str_replace('_', ' ', $t['status']))) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tableLaporanTagihan').DataTable({
            "pageLength": 10,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Cari nama atau kamar...",
                "lengthMenu": "_MENU_",
                "paginate": {
                    "next": '<i class="bi bi-chevron-right"></i>',
                    "previous": '<i class="bi bi-chevron-left"></i>'
                }
            },
            "dom": '<"d-flex justify-content-between align-items-center p-3"<"small"l><"small"f>>rt<"d-flex justify-content-between align-items-center p-3"<"small"i><"small"p>>'
        });
    });
</script>

<?= $this->endSection() ?>