<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SmartKost — Kost Modern, Hidup Lebih Mudah</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="<?= base_url('assets/css/stylelanding.css') ?>" />
</head>
<body class="landing-page">

  <!-- NAVBAR -->
  <nav class="navbar-custom">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between w-100">
        <a href="<?= base_url() ?>" class="navbar-brand-custom">
          <div class="brand-icon-nav"><i class="fas fa-house-chimney"></i></div>
          Smart<span>Kost</span>
        </a>
        <div class="d-none d-md-flex align-items-center gap-1">
          <a href="#kamar" class="nav-link-c">Kamar</a>
          <a href="#fasilitas" class="nav-link-c">Fasilitas</a>
          <a href="#testimoni" class="nav-link-c">Testimoni</a>
          <a href="#kontak" class="nav-link-c">Kontak</a>
        </div>
        <a href="<?= base_url('login') ?>" class="btn-cta-nav d-none d-md-inline-flex align-items-center gap-2">
          <i class="fas fa-sign-in-alt" style="font-size:.8rem"></i> Login
        </a>
        <!-- Mobile burger -->
        <button class="btn d-md-none" style="color:var(--text-dark);font-size:1.2rem;padding:.3rem .5rem;" id="navToggle">
          <i class="fas fa-bars"></i>
        </button>
      </div>
      <!-- Mobile menu -->
      <div id="mobileMenu" style="display:none; padding:1rem 0 .5rem; border-top:1px solid var(--border); margin-top:.8rem;">
        <a href="#kamar" class="d-block nav-link-c mb-1">Kamar</a>
        <a href="#fasilitas" class="d-block nav-link-c mb-1">Fasilitas</a>
        <a href="#testimoni" class="d-block nav-link-c mb-1">Testimoni</a>
        <a href="#kontak" class="d-block nav-link-c mb-2">Kontak</a>
        <a href="<?= base_url('login') ?>" class="btn-cta-nav d-inline-flex align-items-center gap-2">
          <i class="fas fa-sign-in-alt" style="font-size:.8rem"></i> Login
        </a>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-blob hero-blob-1"></div>
    <div class="hero-blob hero-blob-2"></div>
    <div class="container position-relative">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <div class="hero-eyebrow">
            <i class="fas fa-star"></i>
            Kost Terpercaya
          </div>
          <h1 class="hero-title">
            Kost Modern,<br>
            Nyaman & <span class="highlight">Terjangkau</span>
          </h1>
          <p class="hero-desc">
            Temukan kamar kost impianmu dengan fasilitas lengkap, keamanan 24 jam, dan lokasi strategis dekat kampus dan pusat kota.
          </p>
          <div class="hero-actions">
            <a href="#kamar" class="btn-hero-primary">
              <i class="fas fa-search"></i> Lihat Kamar Tersedia
            </a>
            <a href="#testimoni" class="btn-hero-ghost">
              <i class="fas fa-play-circle" style="color:var(--primary)"></i> Ulasan Penghuni
            </a>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="hero-img-wrap position-relative">
            <!-- Placeholder hero image using a gradient box -->
            <div style="width:100%;height:380px;background:linear-gradient(135deg,#e9d0fc 0%,#f8f0ff 40%,#ede9f6 100%);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:1rem;">
              <div style="width:80px;height:80px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;color:#fff;box-shadow:0 8px 30px rgba(196,132,245,.4)">
                <i class="fas fa-house-chimney"></i>
              </div>
              <div style="text-align:center">
                <div style="font-size:1rem;font-weight:800;color:var(--text-dark)">SmartKost Residence</div>
                <div style="font-size:.82rem;color:var(--text-muted)">Hunian nyaman untuk generasi muda</div>
              </div>
            </div>
            <!-- Floating badges -->
            <div class="hero-float-badge tl">
              <div class="badge-dot"></div>
              <span style="color:var(--text-dark)"><?= esc($kamarTersedia) ?> Kamar Tersedia</span>
            </div>
            <div class="hero-float-badge br" style="max-width:180px">
              <div class="badge-icon-wrap"><i class="fas fa-bolt"></i></div>
              <div>
                <div style="font-size:.72rem;font-weight:700;color:var(--text-dark)">Check-in Cepat</div>
                <div style="font-size:.68rem;color:var(--text-muted)">Proses mudah & digital</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- AVAILABILITY STATS -->
  <section class="avail-section" id="fasilitas">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-6 col-md-3 text-center">
          <div class="avail-icon-wrap mx-auto"><i class="fas fa-bed"></i></div>
          <div class="avail-number mb-3"><?= esc($totalKamar) ?></div>
          <div class="avail-label">Total Kamar</div>
          <div class="avail-desc">Berbagai tipe pilihan</div>
        </div>
        <div class="col-6 col-md-3 text-center">
          <div class="avail-icon-wrap mx-auto" style="background:#ecfdf5;color:var(--success)"><i class="fas fa-circle-check"></i></div>
          <div class="avail-number mb-3" style="background:linear-gradient(135deg,var(--success),#00a83d);-webkit-background-clip:text;-webkit-text-fill-color:transparent"><?= esc($kamarTersedia) ?></div>
          <div class="avail-label">Kamar Tersedia</div>
          <div class="avail-desc">Siap huni sekarang</div>
        </div>
        <div class="col-6 col-md-3 text-center">
          <div class="avail-icon-wrap mx-auto" style="background:#fffbeb;color:var(--warning)"><i class="fas fa-users"></i></div>
          <div class="avail-number mb-3" style="background:linear-gradient(135deg,#ff9829,#e07800);-webkit-background-clip:text;-webkit-text-fill-color:transparent"><?= esc($penghuniAktif) ?></div>
          <div class="avail-label">Penghuni Aktif</div>
          <div class="avail-desc">Komunitas yang solid</div>
        </div>
        <div class="col-6 col-md-3 text-center">
          <div class="avail-icon-wrap mx-auto" style="background:#eff6ff;color:var(--info,#116cff)"><i class="fas fa-star"></i></div>
          <div class="avail-number mb-3" style="background:linear-gradient(135deg,#f59e0b,#d97706);-webkit-background-clip:text;-webkit-text-fill-color:#116cff"><?= esc($avgRating) ?></div>
          <div class="avail-label">Rating Penghuni</div>
          <div class="avail-desc">Dari <?= esc($totalUlasan) ?> ulasan</div>
        </div>
      </div>
    </div>
  </section>

  <!-- GENERAL FACILITIES -->
  <style>
      .facility-card {
          background: #ffffff;
          border: 1px solid var(--border);
          border-radius: 16px;
          box-shadow: var(--shadow-sm);
          transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s;
          cursor: pointer;
      }
      .facility-card:hover {
          transform: translateY(-6px);
          box-shadow: var(--shadow-md);
          border-color: var(--primary);
      }
  </style>
  <section class="shared-facilities-section" style="padding: 5rem 0; background: var(--bg-soft);" id="fasilitas">
      <div class="container">
          <div class="text-center mb-5">
              <span class="section-eyebrow">Fasilitas Bersama</span>
              <h2 class="section-title">Kenyamanan <span style="background:linear-gradient(135deg,var(--primary),var(--accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent">Untuk Semua</span></h2>
              <p class="section-sub mx-auto">SmartKost didukung oleh fasilitas umum premium untuk menunjang produktivitas dan keseharian Anda.</p>
          </div>

          <div class="row g-4 justify-content-center">
              <?php if (!empty($shared_facilities)): ?>
                  <?php foreach ($shared_facilities as $sf): ?>
                      <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center">
                          <div class="facility-card p-4 h-100 d-flex flex-column align-items-center justify-content-center">
                              <div class="facility-icon-wrap mb-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px; border-radius: 50%; background: var(--primary-xlight); color: var(--accent); font-size: 1.5rem;">
                                  <i class="fas <?= esc($sf['icon'] ?? 'fa-circle-check') ?>"></i>
                              </div>
                              <h6 class="fw-bold small mb-0 text-dark" style="font-size: 0.82rem; line-height: 1.4;"><?= esc($sf['nama_fasilitas']) ?></h6>
                          </div>
                      </div>
                  <?php endforeach; ?>
              <?php else: ?>
                  <div class="col-12 text-center text-muted">
                      <span>Fasilitas bersama belum tersedia.</span>
                  </div>
              <?php endif; ?>
          </div>
      </div>
  </section>

  <!-- ROOM CARDS -->
  <section class="rooms-section" id="kamar">
    <div class="container">
      <div class="text-center mb-5">
        <span class="section-eyebrow">Pilihan Kamar</span>
        <h2 class="section-title">Temukan Kamar <span style="background:linear-gradient(135deg,var(--primary),var(--accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent">Impianmu</span></h2>
        <p class="section-sub mx-auto">Semua kamar dilengkapi fasilitas modern dengan harga yang bersahabat.</p>
      </div>

      <div class="row g-4">
        <?php if (!empty($rooms)): ?>
          <?php foreach ($rooms as $room): ?>
            <div class="col-md-6 col-lg-4">
              <div class="room-card">
                <div class="room-img-wrap">
                  <?php if (!empty($room['foto']) && file_exists(FCPATH . 'uploads/kamar/' . $room['foto'])): ?>
                    <img src="<?= base_url('uploads/kamar/' . $room['foto']) ?>" alt="Kamar <?= esc($room['nomor_kamar']) ?>" />
                  <?php else: ?>
                    <?php 
                      // Custom gradient backgrounds for aesthetic fallbacks
                      $gradient = 'linear-gradient(135deg,#e9d0fc,#c4b5f7)';
                      $icon = 'fa-door-open';
                      if (strtolower($room['tipe'] ?? '') === 'deluxe') {
                        $gradient = 'linear-gradient(135deg,#a78bfa,#7C3AED)';
                        $icon = 'fa-star';
                      } elseif (strtolower($room['tipe'] ?? '') === 'premium') {
                        $gradient = 'linear-gradient(135deg,#ddd6fe,#a78bfa)';
                        $icon = 'fa-crown';
                      }
                    ?>
                    <div style="width:100%;height:210px;background:<?= $gradient ?>;display:flex;align-items:center;justify-content:center;">
                      <i class="fas <?= $icon ?>" style="font-size:3.5rem;color:#7C3AED;opacity:.4"></i>
                    </div>
                  <?php endif; ?>
                  <span class="room-type-badge">Tipe <?= esc($room['tipe'] ?? 'Standard') ?></span>
                  <span class="room-status-badge available">Tersedia</span>
                </div>
                <div class="room-body">
                  <div class="room-name">Kamar <?= esc($room['nomor_kamar']) ?></div>
                  <div class="room-address"><i class="fas fa-location-dot"></i>Lantai <?= esc($room['lantai']) ?></div>
                  <div class="room-features">
                    <?php if (!empty($room['fasilitas'])): ?>
                      <?php foreach ($room['fasilitas'] as $fas): ?>
                        <span class="room-feat-chip">
                          <i class="fas <?= esc($fas['icon'] ?? 'fa-circle-check') ?>"></i>
                          <?= esc($fas['nama_fasilitas']) ?>
                        </span>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <span class="room-feat-chip"><i class="fas fa-circle-check"></i>Tersedia Beberapa Fasilitas</span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="room-footer">
                  <div>
                    <div class="room-price">Rp <?= number_format($room['harga'], 0, ',', '.') ?> <small>/ bulan</small></div>
                  </div>
                  <a href="<?= base_url('kamar/' . $room['id']) ?>" class="btn-room">Lihat Detail</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12 text-center py-5">
            <div style="font-size: 3rem; color: var(--text-muted);" class="mb-3"><i class="fas fa-door-closed"></i></div>
            <h4>Belum ada kamar tersedia</h4>
            <p class="text-muted">Semua kamar saat ini sudah terisi. Silakan hubungi admin untuk informasi lebih lanjut.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section class="testi-section" id="testimoni">
    <div class="container">
      <div class="text-center mb-5">
        <span class="section-eyebrow">Ulasan Penghuni</span>
        <h2 class="section-title">Cerita dari <span style="background:linear-gradient(135deg,var(--primary),var(--accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent">Penghuni Kita</span></h2>
        <p class="section-sub mx-auto">Dengar langsung dari mereka yang sudah merasakan kenyamanan SmartKost.</p>
      </div>

      <div class="row g-4">
        <?php if (!empty($testimonials)): ?>
          <?php foreach ($testimonials as $t): ?>
            <div class="col-md-6 col-lg-4">
              <div class="testi-card">
                <span class="testi-quote-icon">"</span>
                <div class="testi-stars">
                  <?php for($i=1; $i<=5; $i++): ?>
                    <i class="fas fa-star" style="color: <?= ($i <= ($t['rating'] ?? 5)) ? '#f59e0b' : '#e5e7eb' ?>"></i>
                  <?php endfor; ?>
                </div>
                <p class="testi-text"><?= esc($t['testimoni']) ?></p>
                <div class="d-flex align-items-center gap-3 mt-auto">
                  <?php
                    $initial = '';
                    $nameParts = explode(' ', $t['name'] ?? 'P');
                    $initial .= strtoupper(substr($nameParts[0], 0, 1));
                    if (count($nameParts) > 1) {
                      $initial .= strtoupper(substr($nameParts[1], 0, 1));
                    } else {
                      $initial .= strtoupper(substr($nameParts[0], 1, 1));
                    }
                  ?>
                  <div class="testi-avatar"><?= esc(substr($initial, 0, 2)) ?></div>
                  <div>
                    <div class="testi-name"><?= esc($t['name']) ?></div>
                    <div class="testi-role">Penghuni Kamar <?= esc($t['nomor_kamar'] ?? '-') ?> · <?= esc($t['asal_kota'] ?? 'Luar Kota') ?></div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Static fallback ulasan if DB testimonials are empty -->
          <div class="col-md-6 col-lg-4">
            <div class="testi-card">
              <span class="testi-quote-icon">"</span>
              <div class="testi-stars">★★★★★</div>
              <p class="testi-text">Kamarnya bersih banget dan fasilitas lengkap. WiFi kenceng, AC dingin, dan lokasinya super strategis dekat kampus. Paling suka suasananya yang tenang buat belajar.</p>
              <div class="d-flex align-items-center gap-3 mt-auto">
                <div class="testi-avatar">AR</div>
                <div>
                  <div class="testi-name">Aisyah Rahmawati</div>
                  <div class="testi-role">Mahasiswa UI · 1 tahun</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="testi-card" style="background:linear-gradient(135deg,var(--primary-xlight),#fff);border-color:var(--primary-light)">
              <span class="testi-quote-icon">"</span>
              <div class="testi-stars">★★★★★</div>
              <p class="testi-text">Pindah ke SmartKost adalah keputusan terbaik. Pemiliknya ramah, responsif, dan pengurusannya serba digital. Pembayaran lewat app, laporan kerusakan juga gampang.</p>
              <div class="d-flex align-items-center gap-3 mt-auto">
                <div class="testi-avatar" style="background:linear-gradient(135deg,var(--accent),#5b21b6)">BN</div>
                <div>
                  <div class="testi-name">Bagas Nugroho</div>
                  <div class="testi-role">Karyawan Swasta · 8 bulan</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="testi-card">
              <span class="testi-quote-icon">"</span>
              <div class="testi-stars">★★★★★</div>
              <p class="testi-text">Harga terjangkau tapi kualitas premium! Kamar Premium Suite worth it banget. Ada area bersama yang nyaman juga buat kerja dari kost. Highly recommended!</p>
              <div class="d-flex align-items-center gap-3 mt-auto">
                <div class="testi-avatar" style="background:linear-gradient(135deg,#a855e8,var(--primary))">DW</div>
                <div>
                  <div class="testi-name">Dwi Wahyuni</div>
                  <div class="testi-role">Freelancer · 1.5 tahun</div>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer-section" id="kontak">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4 col-md-6">
          <div class="footer-brand">
            <div class="footer-brand-icon"><i class="fas fa-house-chimney"></i></div>
            Smart<span>Kost</span>
          </div>
          <p class="footer-desc">Hunian modern & nyaman untuk mahasiswa dan profesional muda. Fasilitas lengkap, harga bersahabat.</p>
          <div class="mt-3">
            <a href="#" class="footer-social"><i class="fab fa-instagram"></i></a>
            <a href="#" class="footer-social"><i class="fab fa-whatsapp"></i></a>
            <a href="#" class="footer-social"><i class="fab fa-tiktok"></i></a>
            <a href="#" class="footer-social"><i class="fab fa-twitter"></i></a>
          </div>
        </div>
        <div class="col-lg-2 col-md-6 col-6">
          <div class="footer-heading">Menu</div>
          <a href="#" class="footer-link">Beranda</a>
          <a href="#kamar" class="footer-link">Kamar</a>
          <a href="#fasilitas" class="footer-link">Fasilitas</a>
          <a href="#testimoni" class="footer-link">Testimoni</a>
        </div>
        <div class="col-lg-2 col-md-6 col-6">
          <div class="footer-heading">Info</div>
          <a href="#" class="footer-link">Cara Sewa</a>
          <a href="#" class="footer-link">Syarat & Ketentuan</a>
          <a href="#" class="footer-link">Kebijakan Privasi</a>
          <a href="#" class="footer-link">FAQ</a>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="footer-heading">Hubungi Kami</div>
          <div class="d-flex align-items-start gap-2 mb-2">
            <i class="fas fa-location-dot mt-1" style="color:var(--primary);font-size:.85rem;flex-shrink:0"></i>
            <span style="font-size:.83rem">Jl. Margonda Raya No. 42, Depok, Jawa Barat</span>
          </div>
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fas fa-phone" style="color:var(--primary);font-size:.85rem"></i>
            <span style="font-size:.83rem">+62 812-3456-7890</span>
          </div>
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fas fa-envelope" style="color:var(--primary);font-size:.85rem"></i>
            <span style="font-size:.83rem">halo@smartkost.id</span>
          </div>
          <div style="background:rgba(196,132,245,.12);border:1px solid rgba(196,132,245,.2);border-radius:var(--radius-sm);padding:.85rem 1rem;">
            <div style="font-size:.78rem;font-weight:700;color:#fff;margin-bottom:.3rem">Jam Operasional</div>
            <div style="font-size:.78rem">Senin – Sabtu: 08.00 – 20.00 WIB</div>
            <div style="font-size:.78rem">Minggu: 09.00 – 17.00 WIB</div>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <div>© 2025 SmartKost. Hak cipta dilindungi.</div>
        <div class="footer-bottom-links">
          <a href="#">Privasi</a>
          <a href="#">Ketentuan</a>
          <a href="#">Bantuan</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
  <script>
    // Mobile nav toggle
    document.getElementById('navToggle').addEventListener('click', function() {
      const menu = document.getElementById('mobileMenu');
      menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    });
    // Close mobile menu on link click
    document.querySelectorAll('#mobileMenu a').forEach(link => {
      link.addEventListener('click', () => {
        document.getElementById('mobileMenu').style.display = 'none';
      });
    });
  </script>
</body>
</html>
