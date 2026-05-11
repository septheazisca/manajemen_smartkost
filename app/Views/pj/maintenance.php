<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Tugas Maintenance Saya</h4>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover" id="tablePjMaintenance">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Penyewa</th>
                        <th>Kamar</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($maintenance as $i => $m): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($m['nama_penyewa'] ?? '-') ?></td>
                            <td><?= esc($m['nomor_kamar'] ?? '-') ?></td>
                            <td><?= esc(substr($m['deskripsi'], 0, 60)) ?>...</td>
                            <td>
                                <?php
                                $badge = match ($m['status']) {
                                    'menunggu' => 'warning',
                                    'proses'   => 'info',
                                    'selesai'  => 'success',
                                    default    => 'secondary',
                                };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst($m['status']) ?></span>
                            </td>
                            <td><?= date('d M Y', strtotime($m['created_at'])) ?></td>
                            <td>
                                <a href="/pj/maintenance/<?= $m['id'] ?>" class="btn btn-sm btn-primary">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tablePjMaintenance').DataTable();
    });
</script>

<?= $this->endSection() ?>