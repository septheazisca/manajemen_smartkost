<!DOCTYPE html>
<html>

<head>
    <title>Detail Tagihan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-4">

        <a href="<?= base_url('admin/tagihan') ?>" class="btn btn-secondary mb-3">
            ← Kembali
        </a>

        <h3>Detail Tagihan</h3>

        <div class="card mb-3">
            <div class="card-body">

                <p><strong>Penyewa:</strong>
                    <?= $tagihan['nama'] ?? '-' ?>
                </p>

                <p><strong>Kamar:</strong>
                    <?= $tagihan['nama_kamar'] ?? '-' ?>
                </p>

                <p><strong>Bulan:</strong>
                    <?= $tagihan['bulan'] . ' ' . $tagihan['tahun'] ?>
                </p>

                <p><strong>Jumlah:</strong>
                    Rp <?= number_format($tagihan['jumlah'], 0, ',', '.') ?>
                </p>

                <p><strong>Nominal Unik:</strong>
                    Rp <?= number_format($tagihan['nominal_unik'], 0, ',', '.') ?>
                </p>

                <p><strong>Total:</strong>
                    <b>
                        Rp <?= number_format($tagihan['jumlah'] + $tagihan['nominal_unik'], 0, ',', '.') ?>
                    </b>
                </p>

                <p><strong>Status:</strong>
                    <span class="badge bg-info">
                        <?= $tagihan['status'] ?>
                    </span>
                </p>

                <p><strong>Jatuh Tempo:</strong>
                    <?= $tagihan['jatuh_tempo'] ?>
                </p>

            </div>
        </div>

        <h5>Riwayat Pembayaran</h5>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jumlah Bayar</th>
                    <th>Status</th>
                    <th>Bukti</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pembayaran)) : ?>
                    <?php foreach ($pembayaran as $p) : ?>
                        <tr>
                            <td><?= $p['created_at'] ?></td>
                            <td>Rp <?= number_format($p['jumlah_bayar'], 0, ',', '.') ?></td>
                            <td><?= $p['status'] ?></td>
                            <td>
                                <?php if ($p['bukti_transfer']) : ?>
                                    <a href="<?= base_url('writable/uploads/bukti_transfer/' . $p['bukti_transfer']) ?>" target="_blank">
                                        Lihat Bukti
                                    </a>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4" class="text-center">Belum ada pembayaran</td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>

    </div>

</body>

</html>