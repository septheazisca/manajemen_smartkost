<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">WhatsApp Center</h4>
            <p class="text-muted small mb-0">Kelola pengiriman notifikasi dan pengingat otomatis</p>
        </div>
        <a href="/admin/notifikasi/log" class="btn btn-light border p-2 px-3">
            <i class="bi bi-clock-history me-1"></i> Riwayat Pengiriman
        </a>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Kolom Kiri: Pesan Custom & Info Umum -->
        <div class="col-lg-7">
            <!-- Kirim Pesan Custom -->
            <div class="table-card mb-4">
                <div class="table-card-header bg-white border-bottom p-3">
                    <div class="fw-bold"><i class="bi bi-chat-dots-fill text-primary me-2"></i>Kirim Pesan Custom</div>
                </div>
                <div class="p-4">
                    <form action="/admin/notifikasi/kirim-custom" method="post">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Target Penerima</label>
                                <select name="target" class="form-select border-0 bg-light" id="selectTarget">
                                    <option value="semua">Semua Penyewa</option>
                                    <option value="individu">Individu Spesifik</option>
                                </select>
                            </div>
                            <div class="col-md-12" id="selectPenyewaWrap" style="display:none;">
                                <label class="form-label small fw-bold">Pilih Penyewa</label>
                                <select name="user_id" class="form-select border-0 bg-light">
                                    <option value="">-- Cari Nama/Kamar --</option>
                                    <?php foreach ($penyewa as $p): ?>
                                        <option value="<?= $p['user_id'] ?>">
                                            <?= esc($p['nama'] ?? $p['name'] ?? '-') ?> (Kamar <?= esc($p['nomor_kamar'] ?? '-') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Isi Pesan WhatsApp</label>
                                <textarea name="pesan" class="form-control border-0 bg-light" rows="4" required placeholder="Tulis pesan Anda di sini..."><?= old('pesan') ?></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-primary-custom w-100 py-2">
                                    <i class="bi bi-send me-1"></i> Kirim Sekarang
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kirim Info Umum -->
            <div class="table-card mb-4">
                <div class="table-card-header bg-white border-bottom p-3">
                    <div class="fw-bold"><i class="bi bi-megaphone-fill text-info me-2"></i>Broadcast Pengumuman</div>
                </div>
                <div class="p-4">
                    <form action="/admin/notifikasi/kirim-info" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Judul Pengumuman</label>
                            <input type="text" name="judul" class="form-control border-0 bg-light" value="<?= old('judul') ?>" required placeholder="Misal: Info Perbaikan Listrik">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Detail Informasi</label>
                            <textarea name="pesan" class="form-control border-0 bg-light" rows="3" required placeholder="Jelaskan detail pengumuman..."><?= old('pesan') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-info w-100 text-white py-2 shadow-sm">
                            <i class="bi bi-broadcast me-1"></i> Sebar ke Semua Penyewa
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Pengingat (Reminder) -->
        <div class="col-lg-5">
            <!-- Reminder Tagihan -->
            <div class="table-card mb-4 border-start border-warning border-2">
                <div class="p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                            <i class="bi bi-bell-fill text-warning fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Reminder Pembayaran</h6>
                            <small class="text-muted">Tagihan bulan berjalan</small>
                        </div>
                    </div>
                    <form action="/admin/notifikasi/kirim-reminder-tagihan" method="post">
                        <?= csrf_field() ?>
                        <div class="row g-2 mb-3">
                            <div class="col-7">
                                <select name="bulan" class="form-select border-0 bg-light">
                                    <?php
                                    $listBulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                                    foreach ($listBulan as $val => $label):
                                    ?>
                                        <option value="<?= $val ?>" <?= date('m') == $val ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-5">
                                <input type="number" name="tahun" class="form-control border-0 bg-light" value="<?= date('Y') ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold py-2" onclick="return confirm('Kirim reminder tagihan?')">
                            Kirim Reminder
                        </button>
                    </form>
                </div>
            </div>

            <!-- Reminder Tunggakan -->
            <div class="table-card mb-4 border-start border-danger border-2">
                <div class="p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 p-2 rounded me-3">
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Peringatan Tunggakan</h6>
                            <small class="text-muted">Untuk status 'Menunggak'</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">Kirim peringatan keras untuk penyewa yang melewati batas waktu pembayaran.</p>
                    <form action="/admin/notifikasi/kirim-reminder-tunggakan" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger w-100 fw-bold py-2 shadow-sm" onclick="return confirm('Kirim peringatan tunggakan?')">
                            Tagih Sekarang
                        </button>
                    </form>
                </div>
            </div>

            <!-- Log Terbaru Mini -->
            <div class="table-card">
                <div class="table-card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold small">Status Pengiriman Terakhir</span>
                    <a href="/admin/notifikasi/log" class="small text-decoration-none text-primary">Lihat Semua</a>
                </div>
                <div class="p-0">
                    <div class="list-group list-group-flush">
                        <?php if (empty($log)): ?>
                            <div class="p-3 text-center small text-muted">Belum ada aktivitas.</div>
                        <?php else: ?>
                            <?php foreach (array_slice($log, 0, 3) as $l): ?>
                                <div class="list-group-item bg-transparent py-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small fw-bold"><?= esc($l['no_hp']) ?></span>
                                        <span class="badge rounded-pill bg-<?= $l['status_kirim'] === 'terkirim' ? 'success' : 'danger' ?>" style="font-size: 0.6rem;">
                                            <?= ucfirst($l['status_kirim']) ?>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted" style="font-size: 0.7rem;"><?= ucfirst($l['jenis']) ?></small>
                                        <small class="text-muted" style="font-size: 0.7rem;"><?= date('H:i', strtotime($l['sent_at'])) ?> WIB</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('selectTarget').addEventListener('change', function() {
        const wrap = document.getElementById('selectPenyewaWrap');
        if (this.value === 'individu') {
            wrap.style.display = 'block';
            this.parentElement.classList.replace('col-md-5', 'col-md-5'); // Reset grid jika perlu
        } else {
            wrap.style.display = 'none';
        }
    });
</script>

<?= $this->endSection() ?>