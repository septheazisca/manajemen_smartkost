<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<!-- Header Welcome -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Dashboard</h4>
        <p class="text-muted small">Selamat datang kembali, <span class="fw-bold text-dark"><?= esc($penyewa['nama'] ?? $penyewa['name'] ?? '-') ?></span>!</p>
    </div>
    <div class="d-none d-md-block text-end">
        <div class="fw-bold small"><?= date('l, d M Y') ?></div>
        <div class="text-muted small" id="clock"></div>
    </div>
</div>

<div class="row g-4">
    <!-- Info Kamar Utama (Highlight) -->
    <div class="col-12 col-xl-8">
        <div class="table-card overflow-hidden" style="border-radius: 15px;">
            <div class="row g-0 h-100">
                <div class="col-md-4 btn-primary-custom d-flex align-items-center justify-content-center py-4">
                    <div class="text-center text-white">
                        <i class="bi bi-door-open" style="font-size: 3.5rem;"></i>
                        <h3 class="fw-bold mb-0 mt-2">Kamar <?= esc($penyewa['nomor_kamar'] ?? '-') ?></h3>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="text-muted small d-block">Harga Sewa</label>
                                <span class="fw-bold">Rp <?= number_format($penyewa['harga'] ?? 0, 0, ',', '.') ?> <small class="fw-normal">/bln</small></span>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="text-muted small d-block">Mulai Sewa</label>
                                <span class="fw-bold"><?= $penyewa['tanggal_masuk'] ? date('d M Y', strtotime($penyewa['tanggal_masuk'])) : '-' ?></span>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="p-3 rounded-3 bg-light-subtle border d-flex align-items-center justify-content-between">
                                    <div class="small"><i class="bi bi-info-circle me-2 text-primary"></i>Status Masa Sewa Aktif</div>
                                    <a href="/tenant/profile" class="btn btn-sm btn-white border shadow-sm px-3">Lihat Profil</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Summary Stats -->
    <div class="col-12 col-xl-4">
        <div class="row g-3">
            <div class="col-6 col-xl-12">
                <div class="table-card d-flex align-items-center justify-content-between p-3" style="border-left: 5px solid #DE6B00;">
                    <div>
                        <div class="text-muted small fw-bold">BELUM LUNAS</div>
                        <h4 class="fw-bold mb-0"><?= count($tagihan_aktif) ?></h4>
                    </div>
                    <div class="rounded-circle bg-warning-subtle text-warning p-2">
                        <i class="bi bi-receipt fs-5"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-12">
                <div class="table-card d-flex align-items-center justify-content-between p-3" style="border-left: 5px solid #00BCDE;">
                    <div>
                        <div class="text-muted small fw-bold">MAINTENANCE</div>
                        <h4 class="fw-bold mb-0"><?= $maintenance_proses ?></h4>
                    </div>
                    <div class="rounded-circle bg-info-subtle text-info p-2">
                        <i class="bi bi-tools fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- LEFT: Tagihan Perlu Dibayar -->
    <div class="col-12 col-lg-7">
        <div class="table-card h-100">
            <!-- Header -->
            <div class="table-card-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="table-card-title">Tagihan Perlu Dibayar</div>
                    <div class="table-card-sub small">Daftar tagihan yang belum terselesaikan</div>
                </div>
                <a href="/tenant/tagihan" class="btn rounded-pill btn-sm" style="border: 2px solid var(--primary); font-size: 11px; color: var(--primary); font-weight: 600;">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <!-- TABLE -->
            <div class="tbl-wrap">
                <table class="data-table mb-0">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th>Total Bayar</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tagihan_aktif)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="mb-2 text-success fs-1"><i class="bi bi-check2-circle"></i></div>
                                    <h6 class="fw-bold">Luar Biasa!</h6>
                                    <p class="text-muted small">Semua tagihan Anda sudah lunas. Terima kasih!</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $bulanList = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                            foreach ($tagihan_aktif as $t):
                            ?>
                                <tr class="align-middle">
                                     <td class="fw-bold">
                                         <?= $bulanList[str_pad($t['bulan'], 2, '0', STR_PAD_LEFT)] ?? $t['bulan'] ?> <?= $t['tahun'] ?>
                                     </td>
                                     <td>
                                         <span class="text-primary fw-bold">Rp <?= number_format($t['jumlah'] + $t['nominal_unik'], 0, ',', '.') ?></span>
                                         <div class="text-muted" style="font-size: 10px;">Inc. Kode Unik</div>
                                     </td>
                                     <td>
                                         <span class="<?= (strtotime($t['jatuh_tempo']) < time()) ? 'text-danger fw-bold' : '' ?>">
                                             <?= date('d M Y', strtotime($t['jatuh_tempo'])) ?>
                                         </span>
                                     </td>
                                     <td class="text-center">
                                         <span class="badge <?= esc($t['badge_class']) ?>">
                                             <i class="bi <?= esc($t['icon']) ?> me-1"></i><?= esc(ucwords(str_replace('_', ' ', $t['status']))) ?>
                                         </span>
                                     </td>
                                    <td class="text-center">
                                        <?php if ($t['status'] === 'pending' || $t['status'] === 'menunggak'): ?>
                                            <a href="/tenant/tagihan" class="btn btn-primary-custom btn-sm shadow-sm" style="font-size: 11px; padding: 4px 10px;">
                                                Bayar
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted fw-normal" style="font-size: 11px;"><i class="bi bi-hourglass-split me-1"></i>Proses</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- RIGHT: Rating & Testimoni Kost -->
    <div class="col-12 col-lg-5">
        <div class="table-card h-100">
            <div class="table-card-header">
                <div>
                    <div class="table-card-title">Penilaian Kost</div>
                    <div class="table-card-sub small font-weight-bold">Bagikan ulasan Anda untuk SmartKost</div>
                </div>
            </div>
            
            <div class="card-body p-4">
                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('success_rating')): ?>
                    <div class="alert alert-success border-0 small py-2 shadow-sm mb-3" style="border-radius: 8px;">
                        <i class="bi bi-check-circle-fill me-1"></i><?= session()->getFlashdata('success_rating') ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('errors_rating')): ?>
                    <div class="alert alert-danger border-0 small py-2 shadow-sm mb-3" style="border-radius: 8px;">
                        <ul class="mb-0 ps-3">
                            <?php foreach (session()->getFlashdata('errors_rating') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php 
                    $hasRating = !empty($penyewa['rating']);
                ?>

                <!-- VIEW MODE -->
                <div id="ratingViewMode" style="display: <?= $hasRating ? 'block' : 'none' ?>;">
                    <?php if ($hasRating): ?>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex gap-1" style="font-size: 1.25rem;">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="bi bi-star-fill" style="color: <?= ($i <= $penyewa['rating']) ? '#ff9829' : '#e2e8f0' ?>;"></i>
                                <?php endfor; ?>
                            </div>
                            <div>
                                <?php if (($penyewa['tampilkan_testimoni'] ?? 1) == 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-normal"><i class="bi bi-eye me-1"></i>Tampil</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-normal"><i class="bi bi-eye-slash me-1"></i>Sembunyi</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="p-3 rounded-3 bg-light border mb-3 position-relative" style="min-height: 80px;">
                            <span class="position-absolute text-muted opacity-25" style="font-size: 3rem; top: -10px; left: 10px; font-family: Georgia, serif; user-select: none;">“</span>
                            <p class="mb-0 text-dark ps-4 small" style="line-height: 1.5; font-style: italic;">
                                <?= esc($penyewa['testimoni']) ?>
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary border-primary-custom px-3" onclick="enableEditMode()">
                                <i class="bi bi-pencil-square me-1"></i>Edit
                            </button>
                            <a href="/tenant/rating/toggle" class="btn btn-sm btn-outline-secondary px-3">
                                <?php if (($penyewa['tampilkan_testimoni'] ?? 1) == 1): ?>
                                    <i class="bi bi-eye-slash me-1"></i>Sembunyikan
                                <?php else: ?>
                                    <i class="bi bi-eye me-1"></i>Tampilkan
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- FORM / EDIT MODE -->
                <div id="ratingFormMode" style="display: <?= $hasRating ? 'none' : 'block' ?>;">
                    <form action="/tenant/rating/save" method="post" onsubmit="return validateRatingForm()">
                        <?= csrf_field() ?>
                        <label class="form-label mb-1">Berikan Bintang Anda</label>
                        <div class="star-rating mb-3 d-flex gap-2" style="font-size: 1.8rem; color: #e2e8f0; cursor: pointer; user-select: none;">
                            <i class="bi bi-star-fill star-item" data-value="1"></i>
                            <i class="bi bi-star-fill star-item" data-value="2"></i>
                            <i class="bi bi-star-fill star-item" data-value="3"></i>
                            <i class="bi bi-star-fill star-item" data-value="4"></i>
                            <i class="bi bi-star-fill star-item" data-value="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="rating_input" value="<?= $penyewa['rating'] ?? 0 ?>">

                        <div class="mb-3">
                            <label class="form-label">Tulis Testimoni</label>
                            <textarea name="testimoni" class="form-control" rows="3" placeholder="Bagikan kesan kebersihan, kenyamanan, atau pelayanan kost secara umum..." required><?= esc($penyewa['testimoni'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary-custom btn-sm px-4">Kirim Penilaian</button>
                            <?php if ($hasRating): ?>
                                <button type="button" class="btn btn-cancel btn-sm px-3" onclick="disableEditMode()">Batal</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('clock').textContent = timeStr + ' WIB';
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Edit Mode Toggle
    function enableEditMode() {
        document.getElementById('ratingViewMode').style.display = 'none';
        document.getElementById('ratingFormMode').style.display = 'block';
    }

    function disableEditMode() {
        document.getElementById('ratingViewMode').style.display = 'block';
        document.getElementById('ratingFormMode').style.display = 'none';
    }

    // Dynamic Star Rating Logic
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star-item');
        const input = document.getElementById('rating_input');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const val = this.getAttribute('data-value');
                input.value = val;
                highlightStars(val);
            });
            
            star.addEventListener('mouseover', function() {
                const val = this.getAttribute('data-value');
                highlightStars(val);
            });
            
            star.addEventListener('mouseout', function() {
                highlightStars(input.value);
            });
        });
        
        function highlightStars(count) {
            stars.forEach(star => {
                const val = star.getAttribute('data-value');
                if (val <= count) {
                    star.style.color = '#ff9829';
                } else {
                    star.style.color = '#e2e8f0';
                }
            });
        }
        
        // Initial highlight if exists
        if (input && input.value > 0) {
            highlightStars(input.value);
        }
    });

    function validateRatingForm() {
        const rating = document.getElementById('rating_input').value;
        if (rating == 0) {
            alert('Mohon berikan penilaian bintang terlebih dahulu.');
            return false;
        }
        return true;
    }
</script>

<?= $this->endSection() ?>