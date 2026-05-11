<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Detail Maintenance</h4>
        <a href="/admin/maintenance" class="btn btn-secondary">Kembali</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
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
                        <?php if ($maintenance['assigned_at']): ?>
                            <tr>
                                <td>Tanggal Assign</td>
                                <td>: <?= date('d M Y H:i', strtotime($maintenance['assigned_at'])) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($maintenance['selesai_at']): ?>
                            <tr>
                                <td>Tanggal Selesai</td>
                                <td>: <?= date('d M Y H:i', strtotime($maintenance['selesai_at'])) ?></td>
                            </tr>
                        <?php endif; ?>
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
            <?php if ($maintenance['nama_pj']): ?>
                <div class="card mb-4">
                    <div class="card-header fw-bold">Penanggung Jawab</div>
                    <div class="card-body">
                        <p class="mb-1"><strong><?= esc($maintenance['nama_pj']) ?></strong></p>
                        <p class="text-muted mb-0"><?= esc($maintenance['phone_pj'] ?? '-') ?></p>
                        <?php if ($maintenance['catatan_pj']): ?>
                            <hr>
                            <p class="mb-1 fw-bold">Catatan PJ:</p>
                            <p><?= esc($maintenance['catatan_pj']) ?></p>
                        <?php endif; ?>
                        <?php if ($maintenance['biaya']): ?>
                            <p class="mb-0">Biaya: <strong>Rp <?= number_format($maintenance['biaya'], 0, ',', '.') ?></strong></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($maintenance['status'] === 'menunggu'): ?>
                <div class="card">
                    <div class="card-header fw-bold">Assign ke Penanggung Jawab</div>
                    <div class="card-body">
                        <form action="/admin/maintenance/assign/<?= $maintenance['id'] ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Pilih PJ</label>
                                <select name="pj_id" class="form-select" required>
                                    <option value="">-- Pilih PJ --</option>
                                    <?php foreach ($pj_list as $pj): ?>
                                        <option value="<?= $pj['id'] ?>"><?= esc($pj['nama']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Assign</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>