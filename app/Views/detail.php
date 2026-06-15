<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kamar <?= esc($kamar['tipe'] ?? 'Standard') ?> <?= esc($kamar['nomor_kamar']) ?> — SmartKost</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="<?= base_url('assets/css/stylelanding.css') ?>" />
</head>
<body class="detail-page">

  <!-- NAVBAR -->
  <nav class="navbar-custom">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between w-100">
        <a href="<?= base_url() ?>" class="navbar-brand-custom">
          <div class="brand-icon-nav"><i class="fas fa-house-chimney"></i></div>
          Smart<span>Kost</span>
        </a>
        <div class="d-none d-md-flex align-items-center gap-1">
          <a href="<?= base_url() ?>#kamar" class="nav-link-c">Kamar</a>
          <a href="<?= base_url() ?>#fasilitas" class="nav-link-c">Fasilitas</a>
          <a href="<?= base_url() ?>#testimoni" class="nav-link-c">Testimoni</a>
          <a href="<?= base_url() ?>#kontak" class="nav-link-c">Kontak</a>
        </div>
        <a href="<?= base_url('login') ?>" class="btn-cta-nav d-none d-md-inline-flex align-items-center gap-2">
          <i class="fas fa-sign-in-alt" style="font-size:.8rem"></i> Login
        </a>
      </div>
    </div>
  </nav>

  <!-- BREADCRUMB -->
  <div class="breadcrumb-bar">
    <div class="container">
      <nav class="breadcrumb-custom">
        <a href="<?= base_url() ?>">Beranda</a>
        <i class="fas fa-chevron-right"></i>
        <a href="<?= base_url() ?>#kamar">Kamar</a>
        <i class="fas fa-chevron-right"></i>
        <span>Kamar <?= esc($kamar['tipe'] ?? 'Standard') ?> <?= esc($kamar['nomor_kamar']) ?></span>
      </nav>
    </div>
  </div>

  <!-- GALLERY -->
  <div class="gallery-section">
    <div class="container">
      <?php 
        // Build gallery gradient background color base
        $gColor1 = '#a78bfa';
        $gColor2 = '#7C3AED';
        $iconClass = 'fa-door-open';
        if (strtolower($kamar['tipe'] ?? '') === 'deluxe') {
          $gColor1 = '#c4b5f7';
          $gColor2 = '#7C3AED';
          $iconClass = 'fa-star';
        } elseif (strtolower($kamar['tipe'] ?? '') === 'premium') {
          $gColor1 = '#ddd6fe';
          $gColor2 = '#a78bfa';
          $iconClass = 'fa-crown';
        }
        $mainStyle = "background: linear-gradient(135deg, $gColor1, $gColor2);";
      ?>
      <div class="gallery-main" id="galleryMain" style="<?= $mainStyle ?>">
        <img src="<?= base_url('uploads/kamar/' . $kamar['foto']) ?>" alt="Kamar <?= esc($kamar['nomor_kamar']) ?>" style="width:100%;height:100%;object-fit:cover;display:block;" id="mainGalleryImage" />
      </div>
    </div>
  </div>

  <!-- MAIN DETAIL -->
  <section class="detail-section">
    <div class="container">
      <div class="row g-4 align-items-start">

        <!-- LEFT COLUMN -->
        <div class="col-lg-8">

          <!-- Info Utama -->
          <div class="info-main">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
              <span class="room-tag"><i class="fas fa-star"></i> Tipe <?= esc($kamar['tipe'] ?? 'Standard') ?></span>
              <span class="status-pill available"><span class="status-dot"></span>Tersedia</span>
            </div>
            <h1 class="room-detail-title">Kamar <?= esc($kamar['tipe'] ?? 'Standard') ?> <?= esc($kamar['nomor_kamar']) ?></h1>
            <div class="room-meta">
              <span><i class="fas fa-location-dot"></i>Lantai <?= esc($kamar['lantai'] ?? '1') ?></span>
              <span><i class="fas fa-ruler-combined"></i><?= esc($kamar['luas'] ?? '3x4') ?> m²</span>
            </div>  

            <hr class="divider-line">

            <div class="features-title">Fasilitas Kamar</div>
            <div class="features-grid">
              <?php if (!empty($kamar['fasilitas'])): ?>
                <?php foreach ($kamar['fasilitas'] as $fas): ?>
                  <div class="feat-item">
                    <div class="feat-icon"><i class="fas <?= esc($fas['icon'] ?? 'fa-circle-check') ?>"></i></div>
                    <?= esc($fas['nama_fasilitas']) ?>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="feat-item"><div class="feat-icon"><i class="fas fa-wifi"></i></div>WiFi 100 Mbps</div>
                <div class="feat-item"><div class="feat-icon"><i class="fas fa-snowflake"></i></div>AC Split 1 PK</div>
                <div class="feat-item"><div class="feat-icon"><i class="fas fa-toilet"></i></div>Kamar Mandi Dalam</div>
                <div class="feat-item"><div class="feat-icon"><i class="fas fa-door-closed"></i></div>Lemari Pakaian</div>
                <div class="feat-item"><div class="feat-icon"><i class="fas fa-couch"></i></div>Meja & Kursi Belajar</div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Deskripsi -->
          <div class="desc-card">
            <div class="card-section-title"><i class="fas fa-circle-info"></i>Deskripsi Kamar</div>
            <p class="desc-text">
              <?= !empty($kamar['deskripsi']) ? nl2br(esc($kamar['deskripsi'])) : 'Kamar kost modern minimalis dengan fasilitas lengkap, sirkulasi udara baik, pencahayaan alami, serta didesain khusus untuk menunjang kenyamanan belajar dan istirahat Anda.' ?>
            </p>
          </div>
        </div>

        <!-- RIGHT COLUMN — BOOKING CARD -->
        <div class="col-lg-4">
          <div class="booking-card">
            <div class="booking-card-header">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <div class="booking-price-big">Rp <?= number_format($kamar['harga'], 0, ',', '.') ?> <small>/ bulan</small></div>
              </div>
            </div>
            <div class="booking-card-body">
              <div class="mb-3">
                <div class="form-label-b">Durasi Sewa</div>
                <select class="form-select-b" id="durasiSelect" onchange="hitungHarga()">
                  <option value="1">1 Bulan</option>
                  <option value="3" selected>3 Bulan</option>
                  <option value="6">6 Bulan (hemat 5%)</option>
                  <option value="12">12 Bulan (hemat 10%)</option>
                </select>
              </div>

              <div class="price-breakdown">
                <div class="price-row">
                  <span class="label">Harga kamar</span>
                  <span class="value">Rp <?= number_format($kamar['harga'], 0, ',', '.') ?></span>
                </div>
                <div class="price-row">
                  <span class="label" id="durasiLabel">× 3 bulan</span>
                  <span class="value" id="subtotalVal">Rp <?= number_format($kamar['harga'] * 3, 0, ',', '.') ?></span>
                </div>
                <div class="price-row" id="diskonRow" style="display:none">
                  <span class="label" id="diskonLabel" style="color:var(--success)">Diskon</span>
                  <span class="value" id="diskonVal" style="color:var(--success)"></span>
                </div>
                <div class="price-row total">
                  <span class="label">Total Bayar</span>
                  <span class="value" id="totalVal">Rp <?= number_format(($kamar['harga'] * 3) + 500000, 0, ',', '.') ?></span>
                </div>
              </div>
              <button class="btn-wa" onclick="waOnly()">
                <i class="fab fa-whatsapp"></i> Tanya via WhatsApp
              </button>

              <div class="booking-guarantee">
                <i class="fas fa-shield-halved"></i>
                <span>Gratis survei kamar · Tanpa DP dahulu</span>
              </div>
            </div>
          </div>
        </div>
      </div>

                <!-- Fasilitas Bersama -->
          <?php if (!empty($shared_facilities)): ?>
            <div class="desc-card mt-3">
              <div class="card-section-title"><i class="fas fa-building"></i>Fasilitas Bersama</div>
              <div class="row g-2">
                <?php foreach ($shared_facilities as $sf): ?>
                  <div class="col-6 col-sm-4">
                    <div class="feat-item" style="flex-direction:column;text-align:center;padding:.85rem .5rem;gap:.45rem; border: 1px solid var(--border); border-radius: 10px; background: var(--bg-soft); display: flex;">
                      <div class="feat-icon mx-auto" style="width: 36px; height: 36px; background: var(--primary-xlight); color: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="fas <?= esc($sf['icon'] ?? 'fa-circle-check') ?>"></i></div>
                      <span style="font-size:.78rem; font-weight: 600; color: var(--text-dark);"><?= esc($sf['nama_fasilitas']) ?></span>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
    </div>
  </section>

  <!-- FOOTER MINI -->
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
    const HARGA = <?= $kamar['harga'] ?>;
    const DEPOSIT = 500000;
    const nomorKamar = "<?= esc($kamar['nomor_kamar']) ?>";
    const tipeKamar = "<?= esc($kamar['tipe'] ?? 'Standard') ?>";

    function hitungHarga() {
      const sel = document.getElementById('durasiSelect');
      const bulan = parseInt(sel.value);
      const subtotal = HARGA * bulan;
      let diskon = 0;
      let pct = 0;

      if (bulan === 6)  { pct = 0.05; }
      if (bulan === 12) { pct = 0.10; }
      diskon = Math.round(subtotal * pct);

      document.getElementById('durasiLabel').textContent = `× ${bulan} bulan`;
      document.getElementById('subtotalVal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');

      const diskonRow = document.getElementById('diskonRow');
      if (diskon > 0) {
        diskonRow.style.display = 'flex';
        document.getElementById('diskonLabel').textContent = `Diskon ${pct*100}%`;
        document.getElementById('diskonVal').textContent = '−Rp ' + diskon.toLocaleString('id-ID');
      } else {
        diskonRow.style.display = 'none';
      }

      const total = subtotal - diskon + DEPOSIT;
      document.getElementById('totalVal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function pesan() {
      const nama = document.getElementById('bookingNama').value;
      const hp   = document.getElementById('bookingHp').value;
      const tgl  = document.getElementById('bookingTgl').value;
      const durasi = document.getElementById('durasiSelect').value;
      const total = document.getElementById('totalVal').textContent;

      if (!nama || !hp || !tgl) {
        alert('Mohon lengkapi Nama, Nomor WhatsApp, dan Tanggal Mulai Huni.');
        return;
      }

      const messageText = `Halo SmartKost! Saya ingin memesan Kamar ${nomorKamar} (${tipeKamar}).\n\nDetail Booking:\n- Nama: ${nama}\n- WhatsApp: ${hp}\n- Tanggal Mulai Huni: ${tgl}\n- Durasi Sewa: ${durasi} Bulan\n- Total Estimasi Bayar: ${total}\n\nMohon informasi selanjutnya. Terima kasih!`;
      
      const encodedMsg = encodeURIComponent(messageText);
      window.open(`https://wa.me/6281234567890?text=${encodedMsg}`, '_blank');
    }

    function waOnly() {
      const msg = encodeURIComponent(`Halo SmartKost! Saya tertarik dengan Kamar ${nomorKamar} (${tipeKamar}). Bisa dibantu informasi lebih lanjut?`);
      window.open(`https://wa.me/6281234567890?text=${msg}`, '_blank');
    }

    const galleryBgs = {
      'fas fa-door-open' : 'linear-gradient(135deg,#a78bfa,#7C3AED)',
      'fas fa-star'      : 'linear-gradient(135deg,#c4b5f7,#7C3AED)',
      'fas fa-crown'     : 'linear-gradient(135deg,#ddd6fe,#a78bfa)',
      'fas fa-bath'      : 'linear-gradient(135deg,#e9d0fc,#c4b5f7)',
      'fas fa-couch'     : 'linear-gradient(135deg,#ddd6fe,#c4b5f7)',
      'fas fa-images'    : 'linear-gradient(135deg,#f3e8ff,#e9d0fc)',
    };

    function switchThumb(el, icon, label, c1, c2) {
      document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
      el.classList.add('active');
      const main = document.getElementById('galleryMain');
      
      // If there's an image element in galleryMain, temporarily hide it and show placeholder text
      const mainImg = document.getElementById('mainGalleryImage');
      if (mainImg) {
        mainImg.style.display = (icon === 'fas <?= $iconClass ?>') ? 'block' : 'none';
      }
      
      // If we hid the image or it doesn't exist, use the background gradient and icon
      const bg = galleryBgs[icon] || `linear-gradient(135deg,${c1},${c2})`;
      main.style.background = bg;
      
      // Ensure we have a placeholder element visible when no image is shown
      let placeholder = main.querySelector('.gallery-main-placeholder');
      if (!placeholder) {
        placeholder = document.createElement('div');
        placeholder.className = 'gallery-main-placeholder text-white';
        placeholder.innerHTML = `<i class="" id="mainGalleryIcon"></i><br><span id="mainGalleryLabel"></span>`;
        main.appendChild(placeholder);
      }
      
      if (!mainImg || mainImg.style.display === 'none') {
        placeholder.style.display = 'flex';
        placeholder.querySelector('i').className = icon;
        placeholder.querySelector('span').textContent = label;
      } else {
        placeholder.style.display = 'none';
      }
    }

    hitungHarga();
  </script>
</body>
</html>
