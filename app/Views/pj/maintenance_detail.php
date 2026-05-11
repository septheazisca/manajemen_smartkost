<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Detail Maintenance</h4>
        <a href="/pj/maintenance" class="btn btn-secondary">Kembali</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <?php foreach (session()->getFlashdata('errors') as $e): ?>
                <div><?= esc($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-header fw-bold">Informasi Laporan</div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="160">Penyewa</td>
                            <td>: <?= esc($maintenance['nama_penyewa'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td>No. Kamar</td>
                            <td>: <?= esc($maintenance['nomor_kamar'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td>Deskripsi</td>
                            <td>: <?= esc($maintenance['deskripsi']) ?></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:
                                <?php
                                $badge = match ($maintenance['status']) {
                                    'menunggu' => 'warning',
                                    'proses'   => 'info',
                                    'selesai'  => 'success',
                                    default    => 'secondary',
                                };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst($maintenance['status']) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Tanggal Lapor</td>
                            <td>: <?= date('d M Y H:i', strtotime($maintenance['created_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php if ($maintenance['foto']): ?>
                <div class="card mb-4">
                    <div class="card-header fw-bold">Foto Kerusakan</div>
                    <div class="card-body">
                        <img src="<?= base_url('uploads/maintenance/' . $maintenance['foto']) ?>"
                            class="img-fluid rounded" style="max-height: 350px;">
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-5">
            <?php if ($maintenance['status'] === 'proses'): ?>
                <div class="card">
                    <div class="card-header fw-bold">Tandai Selesai</div>
                    <div class="card-body">
                        <form action="/pj/maintenance/selesai/<?= $maintenance['id'] ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Catatan Penyelesaian <span class="text-danger">*</span></label>
                                <textarea name="catatan_pj" class="form-control" rows="4" required
                                    placeholder="Jelaskan apa yang sudah dikerjakan..."><?= old('catatan_pj') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Biaya (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="biaya" class="form-control"
                                    value="<?= old('biaya', 0) ?>" min="0" required>
                                <small class="text-muted">Isi 0 jika tidak ada biaya.</small>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Tandai Selesai</button>
                        </form>
                    </div>
                </div>
            <?php elseif ($maintenance['status'] === 'selesai'): ?>
                <div class="card">
                    <div class="card-header fw-bold">Hasil Pengerjaan</div>
                    <div class="card-body">
                        <p class="mb-1 fw-bold">Catatan:</p>
                        <p><?= esc($maintenance['catatan_pj'] ?? '-') ?></p>
                        <p class="mb-0">Biaya: <strong>Rp <?= number_format($maintenance['biaya'] ?? 0, 0, ',', '.') ?></strong></p>
                        <p class="mb-0 text-muted mt-2">Selesai: <?= date('d M Y H:i', strtotime($maintenance['selesai_at'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>