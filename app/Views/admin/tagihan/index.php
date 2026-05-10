<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <h3 class="mb-3">Data Tagihan</h3>

    <!-- FILTER BULAN & TAHUN -->
    <form method="get" class="row mb-3">
        <div class="col-md-3">
            <select name="bulan" class="form-control">
                <?php foreach ($list_bulan as $key => $value): ?>
                    <option value="<?= $key ?>" <?= $bulan == $key ? 'selected' : '' ?>>
                        <?= $value ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <input type="number" name="tahun" value="<?= $tahun ?>" class="form-control">
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

    <!-- GENERATE TAGIHAN -->
    <form method="post" action="/admin/tagihan/generate" class="mb-3">
        <?= csrf_field() ?>
        <input type="hidden" name="bulan" value="<?= $bulan ?>">
        <input type="hidden" name="tahun" value="<?= $tahun ?>">

        <button class="btn btn-success"
            onclick="return confirm('Generate tagihan bulan ini?')">
            Generate Tagihan
        </button>
    </form>

    <!-- TABLE -->
    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Penyewa</th>
                        <th>Kamar</th>
                        <th>Bulan</th>
                        <th>Jumlah</th>
                        <th>Nominal Unik</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Jatuh Tempo</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($tagihan)): ?>
                        <?php foreach ($tagihan as $t): ?>
                            <tr>
                                <td><?= $t['nama'] ?? '-' ?></td>
                                <td><?= $t['nama_kamar'] ?? '-' ?></td>
                                <td><?= $list_bulan[$t['bulan']] . ' ' . $t['tahun'] ?></td>

                                <td>Rp <?= number_format($t['jumlah'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($t['nominal_unik'], 0, ',', '.') ?></td>

                                <td>
                                    <b>
                                        Rp <?= number_format($t['jumlah'] + $t['nominal_unik'], 0, ',', '.') ?>
                                    </b>
                                </td>

                                <td>
                                    <?php if ($t['status'] == 'lunas'): ?>
                                        <span class="badge bg-success">Lunas</span>
                                    <?php elseif ($t['status'] == 'pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php elseif ($t['status'] == 'menunggak'): ?>
                                        <span class="badge bg-danger">Menunggak</span>
                                    <?php else: ?>
                                        <span class="badge bg-info"><?= $t['status'] ?></span>
                                    <?php endif; ?>
                                </td>

                                <td><?= $t['jatuh_tempo'] ?></td>

                                <td>
                                    <a href="/admin/tagihan/<?= $t['id'] ?>"
                                        class="btn btn-sm btn-info">
                                        Detail
                                    </a>

                                    <?php if ($t['status'] != 'lunas'): ?>
                                        <form action="/admin/tagihan/tandai-menunggak/<?= $t['id'] ?>"
                                            method="post"
                                            style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Tandai menunggak?')">
                                                Menunggak
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">
                                Tidak ada data tagihan
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>

<?= $this->endSection() ?>