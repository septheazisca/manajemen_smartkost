<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-house-heart-fill"></i></div>
        <div class="brand-text">
            <strong>Smart<span>Kost</span></strong>
            <small>Manajemen Kost</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php $role = session()->get('role'); ?>

        <?php if ($role === 'admin'): ?>

            <div class="nav-section-label">Menu Utama</div>
            <a href="/admin/dashboard" class="nav-link-custom <?= uri_string() === 'admin/dashboard' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
            <a href="/admin/kamar" class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/kamar') ? 'active' : '' ?>">
                <i class="bi bi-door-open"></i> Kamar
            </a>
            <a href="/admin/penyewa" class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/penyewa') ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Penyewa
            </a>

            <div class="nav-section-label">Keuangan</div>
            <a href="/admin/tagihan" class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/tagihan') ? 'active' : '' ?>">
                <i class="bi bi-receipt"></i> Tagihan
            </a>
            <a href="/admin/pengeluaran" class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/pengeluaran') ? 'active' : '' ?>">
                <i class="bi bi-arrow-down-circle"></i> Pengeluaran
            </a>

            <div class="nav-section-label">Operasional</div>
            <a href="/admin/fasilitas" class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/fasilitas') ? 'active' : '' ?>">
                <i class="bi bi-router"></i> Fasilitas
            </a>
            <a href="/admin/maintenance" class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/maintenance') ? 'active' : '' ?>">
                <i class="bi bi-wrench-adjustable"></i> Maintenance
            </a>
            <a href="/admin/pj" class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/pj') ? 'active' : '' ?>">
                <i class="bi bi-person-gear"></i> Penanggung Jawab
            </a>

            <div class="nav-section-label">Laporan</div>
            <a href="/admin/laporan" class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/laporan') ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-line"></i> Laporan Keuangan
            </a>

            <div class="nav-section-label">Sistem</div>
            <a href="/admin/detail-kost" class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/detail-kost') ? 'active' : '' ?>">
                <i class="bi bi-info-circle"></i> Detail Kost
            </a>
            <a href="/admin/notifikasi" class="nav-link-custom <?= str_starts_with(uri_string(), 'admin/notifikasi') ? 'active' : '' ?>">
                <i class="bi bi-whatsapp"></i> Notifikasi WA
            </a>
            <a href="/admin/profile" class="nav-link-custom <?= uri_string() === 'admin/profile' ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i> Profil Admin
            </a>
            <a href="/change-password" class="nav-link-custom <?= uri_string() === 'change-password' ? 'active' : '' ?>">
                <i class="bi bi-key"></i> Ganti Password
            </a>

        <?php elseif ($role === 'pj'): ?>

            <div class="nav-section-label">Menu Utama</div>
            <a href="/pj/dashboard" class="nav-link-custom <?= uri_string() === 'pj/dashboard' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
            <a href="/pj/maintenance" class="nav-link-custom <?= str_starts_with(uri_string(), 'pj/maintenance') ? 'active' : '' ?>">
                <i class="bi bi-wrench-adjustable"></i> Tugas Maintenance
            </a>

            <div class="nav-section-label">Akun</div>
            <a href="/change-password" class="nav-link-custom <?= uri_string() === 'change-password' ? 'active' : '' ?>">
                <i class="bi bi-key"></i> Ganti Password
            </a>

        <?php elseif ($role === 'penyewa'): ?>

            <div class="nav-section-label">Menu Utama</div>
            <a href="/tenant/dashboard" class="nav-link-custom <?= uri_string() === 'tenant/dashboard' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
            <a href="/tenant/tagihan" class="nav-link-custom <?= str_starts_with(uri_string(), 'tenant/tagihan') ? 'active' : '' ?>">
                <i class="bi bi-receipt"></i> Tagihan Saya
            </a>
            <a href="/tenant/maintenance" class="nav-link-custom <?= str_starts_with(uri_string(), 'tenant/maintenance') ? 'active' : '' ?>">
                <i class="bi bi-wrench-adjustable"></i> Lapor Kerusakan
            </a>

            <div class="nav-section-label">Akun</div>
            <a href="/tenant/profile" class="nav-link-custom <?= uri_string() === 'tenant/profile' ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i> Profil Saya
            </a>
            <a href="/change-password" class="nav-link-custom <?= uri_string() === 'change-password' ? 'active' : '' ?>">
                <i class="bi bi-key"></i> Ganti Password
            </a>

        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">
                <?php $name = session()->get('name'); $initial = (!empty(trim($name))) ? strtoupper(substr(trim($name), 0, 1)) : 'U'; echo $initial; ?>
            </div>
            <div class="user-info">
                <strong><?= esc(session()->get('name') ?? '-') ?></strong>
                <span><?= ucfirst(session()->get('role') ?? '') ?></span>
            </div>
            <a href="/logout" class="btn-logout" title="Keluar">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</aside>