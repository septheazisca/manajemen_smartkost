<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="breadcrumb-custom mb-3">
        <a href="/admin/dashboard">
            <i class="bi bi-house"></i> Dashboard
        </a>
        <i class="bi bi-chevron-right"></i>
        <span>WhatsApp Portal</span>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0" style="color: #1e293b;">WhatsApp Portal & Notifikasi</h4>
            <p class="text-muted small mb-0">Kirim pengingat tagihan manual, peringatan tunggakan, dan pesan kustom ke penyewa</p>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- LEFT COLUMN: SEND NOTIFICATION PANELS -->
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <!-- Header Card with Gradient -->
                <div class="p-4 text-white" style="background: linear-gradient(135deg, #C484F5, #7C3AED);">
                    <h5 class="fw-bold mb-1"><i class="bi bi-send-fill me-2"></i>Kirim Pesan Baru</h5>
                    <p class="small mb-0 text-white-50">Gunakan template dinamis atau kirim pesan massal</p>
                </div>
                
                <!-- Tab Headers -->
                <div class="bg-light border-bottom">
                    <ul class="nav nav-tabs border-0 px-3" id="notifTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active border-0 py-3 px-3 fw-bold small text-secondary" id="custom-tab" data-bs-toggle="tab" data-bs-target="#custom-pane" type="button" role="tab" style="background: transparent;">
                                <i class="bi bi-person-fill me-1"></i>Pesan Kustom
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link border-0 py-3 px-3 fw-bold small text-secondary" id="massal-tab" data-bs-toggle="tab" data-bs-target="#massal-pane" type="button" role="tab" style="background: transparent;">
                                <i class="bi bi-people-fill me-1"></i>Kirim Massal
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="card-body p-4">
                    <div class="tab-content" id="notifTabsContent">
                        
                        <!-- TAB 1: CUSTOM MESSAGE -->
                        <div class="tab-pane fade show active" id="custom-pane" role="tabpanel">
                            <form action="/admin/notifikasi/kirim-custom" method="post" id="formKirimCustom">
                                <?= csrf_field() ?>
                                
                                <!-- Select Tenant -->
                                <div class="mb-3">
                                    <label class="form-label">Pilih Penyewa <span class="text-danger">*</span></label>
                                    <select name="user_id" id="selectPenyewa" class="form-select shadow-sm" required>
                                        <option value="" disabled selected>-- Pilih Penyewa --</option>
                                        <?php foreach ($list_penyewa as $p): ?>
                                            <option value="<?= esc($p['user_id']) ?>" data-index="<?= esc($p['user_id']) ?>">
                                                <?= esc($p['name']) ?> (Kamar <?= esc($p['nomor_kamar']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text small text-muted">Hanya menampilkan penyewa aktif</div>
                                </div>

                                <!-- Dynamic Tenant Info Card -->
                                <div id="tenantInfoCard" class="p-3 mb-3 border rounded bg-light-subtle d-none" style="border-radius: 12px; border-left: 4px solid #7C3AED !important;">
                                    <div class="small fw-bold text-dark mb-1"><i class="bi bi-info-circle-fill me-1"></i>Informasi Tagihan Penyewa</div>
                                    <table class="w-100 small text-muted mt-2" style="border-collapse: separate; border-spacing: 0 4px;">
                                        <tr>
                                            <td width="90">No. HP</td>
                                            <td width="10">:</td>
                                            <td id="infoPhone" class="fw-medium text-dark">-</td>
                                        </tr>
                                        <tr>
                                            <td>Kamar</td>
                                            <td>:</td>
                                            <td id="infoKamar" class="fw-medium text-dark">-</td>
                                        </tr>
                                        <tr>
                                            <td>Tagihan Terakhir</td>
                                            <td>:</td>
                                            <td id="infoPeriode" class="fw-medium text-dark">-</td>
                                        </tr>
                                        <tr>
                                            <td>Total Tagihan</td>
                                            <td>:</td>
                                            <td id="infoTotal" class="fw-medium text-primary font-monospace fw-bold">-</td>
                                        </tr>
                                        <tr>
                                            <td>Jatuh Tempo</td>
                                            <td>:</td>
                                            <td id="infoJatuhTempo" class="fw-medium text-dark">-</td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <td>:</td>
                                            <td>
                                                <span id="infoStatus" class="badge rounded-pill fw-bold" style="font-size: 0.65rem;">-</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Select Template -->
                                <div class="mb-3">
                                    <label class="form-label">Gunakan Template Pesan</label>
                                    <select id="selectTemplate" class="form-select shadow-sm">
                                        <option value="kustom">-- Tulis Kustom --</option>
                                        <option value="tagihan">Template Pengingat Tagihan (Pending)</option>
                                        <option value="tunggakan">Template Peringatan Tunggakan (Menunggak)</option>
                                        <option value="lunas">Template Konfirmasi Lunas (Lunas)</option>
                                    </select>
                                </div>

                                <!-- Textarea Message Content -->
                                <div class="mb-4">
                                    <label class="form-label">Isi Pesan WhatsApp <span class="text-danger">*</span></label>
                                    <textarea name="message" id="textareaMessage" class="form-control shadow-sm font-monospace" rows="8" required style="font-size: 0.85rem;" placeholder="Tulis isi pesan WhatsApp di sini..."></textarea>
                                    <div class="form-text small text-muted">Anda dapat mengedit kembali pesan yang dihasilkan oleh template sebelum mengirim.</div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary-custom w-100 d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-whatsapp"></i> Kirim Notifikasi WhatsApp
                                </button>
                            </form>
                        </div>

                        <!-- TAB 2: BULK MESSAGE -->
                        <div class="tab-pane fade" id="massal-pane" role="tabpanel">
                            <form action="/admin/notifikasi/kirim-massal" method="post" id="formKirimMassal">
                                <?= csrf_field() ?>
                                
                                <div class="alert alert-warning border-0 small" style="border-radius: 12px;">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Fitur ini akan mengirimkan notifikasi WhatsApp secara massal ke <strong>semua penyewa</strong> yang memiliki tagihan aktif berstatus tertentu pada <strong>bulan berjalan (<?= $list_bulan[date('m')] ?> <?= date('Y') ?>)</strong>.
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Pilih Status Target Tagihan</label>
                                    <div class="d-flex flex-column gap-2 mt-2">
                                        <label class="p-3 border rounded d-flex align-items-center justify-content-between cursor-pointer" style="border-radius: 12px;" role="button">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="radio" name="status_tagihan" value="pending" checked class="form-check-input">
                                                <div>
                                                    <strong class="text-dark small">Belum Bayar (Pending)</strong>
                                                    <div class="text-muted small" style="font-size: 0.75rem;">Tagihan baru yang belum dibayar</div>
                                                </div>
                                            </div>
                                            <span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1 rounded-pill small">Pending</span>
                                        </label>

                                        <label class="p-3 border rounded d-flex align-items-center justify-content-between cursor-pointer" style="border-radius: 12px;" role="button">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="radio" name="status_tagihan" value="menunggak" class="form-check-input">
                                                <div>
                                                    <strong class="text-dark small">Menunggak</strong>
                                                    <div class="text-muted small" style="font-size: 0.75rem;">Tagihan melewati batas jatuh tempo</div>
                                                </div>
                                            </div>
                                            <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1 rounded-pill small">Menunggak</span>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary-custom w-100 fw-bold d-flex align-items-center justify-content-center gap-2" style="border-radius: var(--radius-sm); color: white;">
                                    <i class="bi bi-send-fill"></i> Kirim Notifikasi Massal
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: HISTORICAL LOGS -->
        <div class="col-xl-8 col-lg-7">
            <div class="table-card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="table-card-header bg-white p-4 border-bottom">
                    <h5 class="fw-bold mb-1" style="color: #1e293b;"><i class="bi bi-clock-history me-2"></i>Riwayat Notifikasi WhatsApp</h5>
                    <p class="text-muted small mb-0">Pantau seluruh status pesan keluar yang dipicu secara manual</p>
                </div>
                
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
                                        <td class="text-muted small"><?= $i + 1 ?></td>
                                        <td>
                                            <div class="fw-bold text-dark" style="font-size: 0.88rem;"><?= esc($l['name'] ?? 'Guest/Admin Umum') ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-whatsapp me-1 text-success"></i><?= esc($l['no_hp']) ?></div>
                                        </td>
                                        <td>
                                            <?php
                                            switch ($l['jenis']) {
                                                case 'approved':
                                                    $badgeClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                                    $badgeLabel = 'DISETUJUI';
                                                    break;
                                                case 'ditolak':
                                                    $badgeClass = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                                    $badgeLabel = 'DITOLAK';
                                                    break;
                                                case 'tunggakan':
                                                    $badgeClass = 'bg-warning bg-opacity-10 text-warning border border-warning-subtle';
                                                    $badgeLabel = 'TUNGGAKAN';
                                                    break;
                                                case 'upload_bukti':
                                                    $badgeClass = 'bg-info bg-opacity-10 text-info border border-info border-opacity-25';
                                                    $badgeLabel = 'BUKTI MASUK';
                                                    break;
                                                case 'tagihan':
                                                    $badgeClass = 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25';
                                                    $badgeLabel = 'TAGIHAN';
                                                    break;
                                                case 'custom':
                                                    $badgeClass = 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
                                                    $badgeLabel = 'PESAN CUSTOM';
                                                    break;
                                                case 'maintenance':
                                                    $badgeClass = 'bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25';
                                                    $badgeLabel = 'MAINTENANCE';
                                                    break;
                                                default:
                                                    $badgeClass = 'bg-light text-dark border border-secondary border-opacity-10';
                                                    $badgeLabel = strtoupper($l['jenis'] ?: 'LAINNYA');
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?> p-2 px-3 rounded-pill" style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.5px;">
                                                <?= esc($badgeLabel) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-truncate text-muted" style="max-width: 250px; font-size: 0.82rem;" title="<?= esc($l['pesan']) ?>">
                                                <?= esc($l['pesan']) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($l['status_kirim'] === 'teririm' || $l['status_kirim'] === 'terkirim'): ?>
                                                <span class="text-success small fw-bold" style="font-size: 0.8rem;">
                                                    <i class="bi bi-check2-all me-1"></i> Terkirim
                                                </span>
                                            <?php else: ?>
                                                <span class="text-danger small fw-bold" style="font-size: 0.8rem;">
                                                    <i class="bi bi-exclamation-circle me-1"></i> Gagal
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="text-dark small fw-medium" style="font-size: 0.8rem;"><?= date('d M Y', strtotime($l['sent_at'])) ?></div>
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
    </div>
</div>

<style>
    /* Styling khusus agar UI terlihat modern & premium */
    #notifTabs .nav-link.active {
        color: #7C3AED !important;
        border-bottom: 3px solid #7C3AED !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #7C3AED !important;
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
    // Data list penyewa untuk engine template JS
    const listPenyewa = <?= json_encode($list_penyewa) ?>;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Datatable
        $('#tableLog').DataTable({
            order: [
                [5, 'desc']
            ], 
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

        const selectPenyewa = document.getElementById('selectPenyewa');
        const selectTemplate = document.getElementById('selectTemplate');
        const textareaMessage = document.getElementById('textareaMessage');
        
        // Tenant info components
        const tenantInfoCard = document.getElementById('tenantInfoCard');
        const infoPhone = document.getElementById('infoPhone');
        const infoKamar = document.getElementById('infoKamar');
        const infoPeriode = document.getElementById('infoPeriode');
        const infoTotal = document.getElementById('infoTotal');
        const infoJatuhTempo = document.getElementById('infoJatuhTempo');
        const infoStatus = document.getElementById('infoStatus');

        // Event listener selection penyewa
        selectPenyewa.addEventListener('change', function() {
            const selectedUserId = this.value;
            const penyewa = listPenyewa.find(p => p.user_id == selectedUserId);

            if (penyewa) {
                // Populate Tenant Info Card
                infoPhone.innerText = penyewa.phone || '-';
                infoKamar.innerText = 'Kamar ' + penyewa.nomor_kamar;
                infoPeriode.innerText = penyewa.latest_bill.periode || '-';
                infoTotal.innerText = 'Rp ' + penyewa.latest_bill.total;
                infoJatuhTempo.innerText = penyewa.latest_bill.jatuh_tempo || '-';
                
                // Status Badge styling
                infoStatus.innerText = (penyewa.latest_bill.status || 'pending').toUpperCase();
                if (penyewa.latest_bill.status === 'menunggak') {
                    infoStatus.className = 'badge bg-danger rounded-pill fw-bold';
                } else {
                    infoStatus.className = 'badge bg-warning text-dark rounded-pill fw-bold';
                }
                
                tenantInfoCard.classList.remove('d-none');
                
                // Trigger template regeneration if a template is selected
                generateMessage();
            } else {
                tenantInfoCard.classList.add('d-none');
            }
        });

        // Event listener selection template
        selectTemplate.addEventListener('change', generateMessage);

        function generateMessage() {
            const selectedUserId = selectPenyewa.value;
            const templateType = selectTemplate.value;

            if (!selectedUserId) {
                return;
            }

            const penyewa = listPenyewa.find(p => p.user_id == selectedUserId);
            if (!penyewa) return;

            const name = penyewa.name;
            const room = 'Kamar ' + penyewa.nomor_kamar;
            const period = penyewa.latest_bill.periode;
            const total = 'Rp ' + penyewa.latest_bill.total;
            const dueDate = penyewa.latest_bill.jatuh_tempo;

            let message = '';

            switch (templateType) {
                case 'tagihan':
                    message = `Halo *${name}*,\n\n📢 Ini adalah pengingat tagihan sewa kamar *${room}* untuk periode *${period}*.\n\nTotal tagihan: *${total}*\nJatuh Tempo: *${dueDate}*\n\nMohon lakukan pembayaran dan unggah bukti transfer melalui aplikasi SmartKost. Terima kasih! 🙏`;
                    break;
                case 'tunggakan':
                    message = `Halo *${name}*,\n\n⚠️ Tagihan sewa kamar *${room}* periode *${period}* sebesar *${total}* telah *MELEWATI JATUH TEMPO* (${dueDate}).\n\nStatus tagihan saat ini: *MENUNGGAK*.\n\nMohon segera lakukan pelunasan pembayaran dan unggah bukti transfer melalui aplikasi SmartKost. Terima kasih atas pengertiannya. 🙏`;
                    break;
                case 'lunas':
                    message = `Halo *${name}*,\n\n🎉 Terima kasih! Pembayaran sewa kamar *${room}* untuk periode *${period}* sebesar *${total}* telah kami terima dan berstatus *LUNAS*.\n\nTerima kasih atas kerja samanya! Semoga betah tinggal di SmartKost. 😊`;
                    break;
                case 'kustom':
                default:
                    message = '';
                    break;
            }

            textareaMessage.value = message;
        }
    });
</script>

<?= $this->endSection() ?>