<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Riwayat Notifikasi WhatsApp</h4>
            <p class="text-muted small mb-0">Pantau semua aktivitas pengiriman pesan WhatsApp oleh sistem</p>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="table-card shadow-sm">
        <div class="p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tableLog" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-muted small" style="width: 50px;">#</th>
                            <th class="text-muted small">PENERIMA</th>
                            <th class="text-muted small">JENIS</th>
                            <th class="text-muted small">ISI PESAN</th>
                            <th class="text-muted small text-center">STATUS</th>
                            <th class="text-muted small">WAKTU KIRIM</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($log as $i => $l): ?>
                            <tr>
                                <td class="text-muted"><?= $i + 1 ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($l['name'] ?? 'Guest/Unknown') ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-whatsapp me-1"></i><?= esc($l['no_hp']) ?></div>
                                </td>
                                <td>
                                    <?php
                                    switch ($l['jenis']) {
                                        case 'approved':
                                            $badgeClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                            $badgeLabel = 'PEMBAYARAN DISETUJUI';
                                            break;
                                        case 'ditolak':
                                            $badgeClass = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                            $badgeLabel = 'PEMBAYARAN DITOLAK';
                                            break;
                                        case 'tunggakan':
                                            $badgeClass = 'bg-warning bg-opacity-10 text-warning border border-warning-subtle';
                                            $badgeLabel = 'REMINDER TUNGGAKAN';
                                            break;
                                        case 'upload_bukti':
                                            $badgeClass = 'bg-info bg-opacity-10 text-info border border-info border-opacity-25';
                                            $badgeLabel = 'BUKTI PEMBAYARAN MASUK';
                                            break;
                                        case 'tagihan':
                                            $badgeClass = 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25';
                                            $badgeLabel = 'REMINDER TAGIHAN';
                                            break;
                                        case 'custom':
                                            $badgeClass = 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
                                            $badgeLabel = 'PESAN CUSTOM';
                                            break;
                                        default:
                                            $badgeClass = 'bg-light text-dark border border-secondary border-opacity-10';
                                            $badgeLabel = strtoupper($l['jenis'] ?: 'LAINNYA');
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?> p-2 px-3 rounded-pill" style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.5px;">
                                        <?= esc($badgeLabel) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate text-muted" style="max-width: 250px; font-size: 0.85rem;" title="<?= esc($l['pesan']) ?>">
                                        <?= esc($l['pesan']) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if ($l['status_kirim'] === 'terkirim'): ?>
                                        <span class="text-success small fw-bold">
                                            <i class="bi bi-check2-all me-1"></i> Terkirim
                                        </span>
                                    <?php else: ?>
                                        <span class="text-danger small fw-bold">
                                            <i class="bi bi-exclamation-circle me-1"></i> Gagal
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="text-dark small fw-medium"><?= date('d M Y', strtotime($l['sent_at'])) ?></div>
                                    <div class="text-muted small" style="font-size: 0.7rem;"><?= date('H:i', strtotime($l['sent_at'])) ?> WIB</div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling khusus agar DataTable terlihat modern */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #0d6efd !important;
        color: white !important;
        border-radius: 5px;
        border: none;
    }

    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        padding: 5px 15px;
        border: 1px solid #dee2e6;
        background-color: #f8f9fa;
    }

    .table thead th {
        letter-spacing: 0.5px;
        border-bottom: 2px solid #f8f9fa;
    }
</style>

<script>
    $(document).ready(function() {
        $('#tableLog').DataTable({
            order: [
                [5, 'desc']
            ], // Berdasarkan Waktu Kirim (kolom index ke-5)
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari riwayat...",
                lengthMenu: "_MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ log",
                paginate: {
                    previous: "<i class='bi bi-chevron-left'></i>",
                    next: "<i class='bi bi-chevron-right'></i>"
                }
            }
        });
    });
</script>

<?= $this->endSection() ?>