<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 3px double #444;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 13px;
            color: #666;
        }

        /* Styling Section */
        .section-title {
            background-color: #f4f4f4;
            border-left: 5px solid #333;
            padding: 8px 12px;
            margin: 20px 0 10px 0;
            font-weight: bold;
            font-size: 12px;
            color: #000;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #333;
            color: #ffffff;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        /* Summary Styling */
        .summary-box {
            width: 100%;
            border: 1px solid #ddd;
            background-color: #fafafa;
        }

        .summary-box td {
            border: none;
            padding: 5px 15px;
        }

        .label {
            width: 180px;
            color: #555;
        }

        .value {
            font-family: 'Courier', monospace;
            font-weight: bold;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        /* Total & Colors */
        .total-row {
            background-color: #f9f9f9;
        }

        .total-row td {
            font-weight: bold;
            border-top: 2px solid #333;
            font-size: 12px;
        }

        .saldo-positif {
            color: #1a7a1a;
        }

        .saldo-negatif {
            color: #c00;
        }

        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 9px;
            background: #eee;
            color: #444;
            border: 0.5px solid #ccc;
        }

        .footer {
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-style: italic;
            font-size: 9px;
            color: #999;
        }

        /* Memberikan space untuk tanda tangan jika diperlukan */
        .signature-wrapper {
            margin-top: 40px;
            width: 100%;
        }

        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>SmarKost Financial Report</h2>
        <p>Laporan Bulanan: <?= $list_bulan[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?> <?= $tahun ?></p>
    </div>

    <!-- Ringkasan Keuangan -->
    <div class="section-title">Ringkasan Eksekutif</div>
    <table class="summary-box">
        <tr>
            <td class="label">Total Pendapatan</td>
            <td class="value">: Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label">Total Pengeluaran Operasional</td>
            <td class="value">: (Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?>)</td>
        </tr>
        <tr>
            <td colspan="2">
                <hr style="border: 0; border-top: 1px solid #ccc;">
            </td>
        </tr>
        <tr>
            <td class="label" style="font-size: 13px; color: #000;"><strong>Saldo Bersih (Profit)</strong></td>
            <td class="value" style="font-size: 14px;">:
                <span class="<?= $saldo_bersih >= 0 ? 'saldo-positif' : 'saldo-negatif' ?>">
                    Rp <?= number_format($saldo_bersih, 0, ',', '.') ?>
                </span>
            </td>
        </tr>
    </table>

    <!-- Detail Pemasukan -->
    <div class="section-title">Rincian Pemasukan Kamar</div>
    <?php if (empty($detail_pemasukan)): ?>
        <p style="text-align: center; padding: 20px; color: #999;">Tidak ada catatan transaksi masuk untuk periode ini.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th>Penyewa</th>
                    <th>Unit</th>
                    <th>Periode Tagihan</th>
                    <th class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detail_pemasukan as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= esc($p['name']) ?></strong></td>
                        <td><span class="badge">Kamar <?= esc($p['nomor_kamar']) ?></span></td>
                        <td><?= $p['bulan'] ?> / <?= $p['tahun'] ?></td>
                        <td class="text-right">Rp <?= number_format($p['jumlah'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right">Subtotal Pemasukan</td>
                    <td class="text-right">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <!-- Detail Pengeluaran -->
    <div class="section-title">Rincian Biaya & Pengeluaran</div>
    <?php if (empty($detail_pengeluaran)): ?>
        <p style="text-align: center; padding: 20px; color: #999;">Tidak ada catatan pengeluaran untuk periode ini.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th>Deskripsi Kebutuhan</th>
                    <th>Kategori</th>
                    <th class="text-right">Biaya</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detail_pengeluaran as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($p['keterangan']) ?></td>
                        <td><span class="badge"><?= ucfirst($p['kategori']) ?></span></td>
                        <td class="text-right text-negatif">Rp <?= number_format($p['jumlah'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-right">Subtotal Pengeluaran</td>
                    <td class="text-right">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>

    <div class="signature-wrapper">
        <div class="signature-box">
            <p>Dicetak pada: <?= $generated ?></p>
            <br><br><br>
            <p><strong>__________________________</strong></p>
            <p>Administrator SmarKost</p>
        </div>
    </div>

    <div class="footer">
        * Dokumen ini dihasilkan secara otomatis oleh Sistem Manajemen SmarKost dan merupakan bukti laporan keuangan yang sah.
    </div>

</body>

</html>