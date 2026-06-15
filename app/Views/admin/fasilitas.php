<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<style>
    .nav-pills .nav-link {
        color: var(--text-muted);
        background: transparent;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: var(--font);
        font-size: .83rem;
        font-weight: 600;
        transition: all .2s;
        padding: .5rem 1.1rem;
    }
    .nav-pills .nav-link:hover {
        color: var(--primary);
        background: var(--primary-xlight);
        border-color: var(--primary-light);
    }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, var(--primary), var(--accent));
        color: #fff !important;
        border-color: transparent;
        box-shadow: 0 4px 14px rgba(196,132,245,.25);
    }
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Fasilitas</span>
</div>

<!-- Nav Pills Tabs -->
<ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pills-kamar-tab" data-bs-toggle="pill" data-bs-target="#pills-kamar" type="button" role="tab" aria-controls="pills-kamar" aria-selected="true">
            <i class="bi bi-door-open me-1"></i> Fasilitas Kamar
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-bersama-tab" data-bs-toggle="pill" data-bs-target="#pills-bersama" type="button" role="tab" aria-controls="pills-bersama" aria-selected="false">
            <i class="bi bi-people me-1"></i> Fasilitas Bersama
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="pills-tabContent">
    
    <!-- ============= TAB 1: FASILITAS KAMAR ============= -->
    <div class="tab-pane fade show active" id="pills-kamar" role="tabpanel" aria-labelledby="pills-kamar-tab">
        <div class="table-card">
            <!-- Header -->
            <div class="table-card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 w-100">
                <div class="text-nowrap">
                    <div class="table-card-title fw-bold" style="font-size: 1.15rem; color: #1e293b;">Data Fasilitas Kamar</div>
                    <div class="table-card-sub text-muted small">Total <?= count($facilities) ?> fasilitas</div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end" style="max-width: 500px;">
                    <div class="input-group flex-grow-1" style="max-width: 260px;">
                        <input type="text" id="searchFasilitas" class="form-control" placeholder="Cari nama fasilitas...">
                        <span class="input-group-text bg-light text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                    </div>

                    <button class="btn btn-primary btn-add text-nowrap" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Fasilitas
                    </button>
                </div>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger border-0 shadow-sm m-3" style="border-radius: 12px;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success border-0 shadow-sm m-3" style="border-radius: 12px;">
                    <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="tbl-wrap">
                <table class="data-table" id="tableFasilitas">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Fasilitas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($facilities)): ?>
                            <?php $no = 1; foreach ($facilities as $f): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($f['nama_fasilitas']) ?></td>
                                    <td>
                                        <div style="display:flex;gap:.35rem;">
                                            <button class="action-btn edit" title="Edit" data-bs-toggle="modal" data-bs-target="#editModal<?= $f['id'] ?>"><i class="bi bi-pencil"></i></button>
                                            <button type="button" class="action-btn del btn-delete" title="Hapus" data-url="/admin/fasilitas/delete/<?= $f['id'] ?>" data-nama="<?= esc($f['nama_fasilitas']) ?>"> <i class="bi bi-trash3"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center" style="padding: 2rem; color: #6c757d;">
                                    <i class="bi bi-info-circle" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                    Data tidak tersedia
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============= TAB 2: FASILITAS BERSAMA ============= -->
    <div class="tab-pane fade" id="pills-bersama" role="tabpanel" aria-labelledby="pills-bersama-tab">
        <div class="table-card">
            <!-- Header -->
            <div class="table-card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 w-100">
                <div class="text-nowrap">
                    <div class="table-card-title fw-bold" style="font-size: 1.15rem; color: #1e293b;">Data Fasilitas Bersama</div>
                    <div class="table-card-sub text-muted small">Total <?= count($shared_facilities) ?> fasilitas bersama</div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end" style="max-width: 500px;">
                    <div class="input-group flex-grow-1" style="max-width: 260px;">
                        <input type="text" id="searchFasilitasBersama" class="form-control" placeholder="Cari fasilitas bersama...">
                        <span class="input-group-text bg-light text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                    </div>

                    <button class="btn btn-primary btn-add text-nowrap" data-bs-toggle="modal" data-bs-target="#addSharedModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Fasilitas Bersama
                    </button>
                </div>
            </div>

            <?php if (session()->getFlashdata('error_bersama')): ?>
                <div class="alert alert-danger border-0 shadow-sm m-3" style="border-radius: 12px;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error_bersama') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success_bersama')): ?>
                <div class="alert alert-success border-0 shadow-sm m-3" style="border-radius: 12px;">
                    <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success_bersama') ?>
                </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="tbl-wrap">
                <table class="data-table" id="tableFasilitasBersama">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Fasilitas Bersama</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($shared_facilities)): ?>
                            <?php $no = 1; foreach ($shared_facilities as $sf): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($sf['nama_fasilitas']) ?></td>
                                    <td>
                                        <div style="display:flex;gap:.35rem;">
                                            <button class="action-btn edit" title="Edit" data-bs-toggle="modal" data-bs-target="#editSharedModal<?= $sf['id'] ?>"><i class="bi bi-pencil"></i></button>
                                            <button type="button" class="action-btn del btn-delete-shared" title="Hapus" data-url="/admin/fasilitas-bersama/delete/<?= $sf['id'] ?>" data-nama="<?= esc($sf['nama_fasilitas']) ?>"> <i class="bi bi-trash3"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center" style="padding: 2rem; color: #6c757d;">
                                    <i class="bi bi-info-circle" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                    Data tidak tersedia
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ============= MODAL TAMBAH KAMAR FASILITAS ============= -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title d-flex align-items-center">
                    <span class="modal-icon add"><i class="bi bi-plus-circle-fill"></i></span>
                    Tambah Fasilitas Kamar
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/fasilitas/store" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Fasilitas Kamar</label>
                        <input type="text" name="nama_fasilitas" class="form-control" placeholder="Contoh: AC Split" required>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-primary-custom">
                        <i class="bi bi-check-lg me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============= MODAL EDIT KAMAR FASILITAS ============= -->
<?php foreach ($facilities as $f): ?>
    <div class="modal fade" id="editModal<?= $f['id'] ?>">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title d-flex align-items-center">
                        <span class="modal-icon edit"><i class="bi bi-pencil-fill"></i></span>
                        Edit Fasilitas Kamar
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/admin/fasilitas/update/<?= $f['id'] ?>" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Fasilitas Kamar</label>
                            <input type="text" name="nama_fasilitas" class="form-control" value="<?= esc($f['nama_fasilitas']) ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer gap-2">
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-primary-custom">
                            <i class="bi bi-check-lg me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- ============= MODAL TAMBAH FASILITAS BERSAMA ============= -->
<div class="modal fade" id="addSharedModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title d-flex align-items-center">
                    <span class="modal-icon add"><i class="bi bi-plus-circle-fill"></i></span>
                    Tambah Fasilitas Bersama
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/fasilitas-bersama/store" method="post" onsubmit="submitSharedForm('addSharedModal')">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Fasilitas Bersama</label>
                        <input type="text" name="nama_fasilitas" class="form-control" placeholder="Contoh: Dapur Bersama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Ikon Fasilitas</label>
                        <select name="icon_select" class="form-select mb-2" onchange="toggleCustomIcon(this, 'add_custom_icon_wrapper')">
                            <option value="fa-shield-halved">🛡️ Keamanan / CCTV (fa-shield-halved)</option>
                            <option value="fa-motorcycle">🏍️ Parkir Motor (fa-motorcycle)</option>
                            <option value="fa-tshirt">👕 Laundry / Cuci (fa-tshirt)</option>
                            <option value="fa-utensils">🍳 Dapur Bersama (fa-utensils)</option>
                            <option value="fa-couch">🛋️ Ruang Tamu / Sofa (fa-couch)</option>
                            <option value="fa-leaf">🍃 Taman / Area Hijau (fa-leaf)</option>
                            <option value="fa-wifi">📶 WiFi Bersama (fa-wifi)</option>
                            <option value="fa-tv">📺 TV Area (fa-tv)</option>
                            <option value="fa-temperature-low">❄️ Kulkas / Pendingin (fa-temperature-low)</option>
                            <option value="fa-faucet">🚰 Dispenser Air (fa-faucet)</option>
                            <option value="fa-dumbbell">🏋️ Gym Mini (fa-dumbbell)</option>
                            <option value="custom">✍️ Tulis Custom (FontAwesome class)...</option>
                        </select>
                        
                        <div id="add_custom_icon_wrapper" style="display:none;">
                            <label class="form-label small text-muted">Nama Kelas FontAwesome (tanpa awalan 'fas', contoh: fa-bicycle)</label>
                            <input type="text" name="icon_custom" class="form-control" placeholder="fa-bicycle">
                        </div>
                        <input type="hidden" name="icon" id="add_final_icon" value="fa-shield-halved">
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-primary-custom">
                        <i class="bi bi-check-lg me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============= MODAL EDIT FASILITAS BERSAMA ============= -->
<?php if (!empty($shared_facilities)): ?>
    <?php foreach ($shared_facilities as $sf): ?>
        <div class="modal fade" id="editSharedModal<?= $sf['id'] ?>">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title d-flex align-items-center">
                            <span class="modal-icon edit"><i class="bi bi-pencil-fill"></i></span>
                            Edit Fasilitas Bersama
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="/admin/fasilitas-bersama/update/<?= $sf['id'] ?>" method="post" onsubmit="submitSharedForm('editSharedModal<?= $sf['id'] ?>')">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Fasilitas Bersama</label>
                                <input type="text" name="nama_fasilitas" class="form-control" value="<?= esc($sf['nama_fasilitas']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pilih Ikon Fasilitas</label>
                                <?php 
                                    $knownIcons = ['fa-shield-halved', 'fa-motorcycle', 'fa-tshirt', 'fa-utensils', 'fa-couch', 'fa-leaf', 'fa-wifi', 'fa-tv', 'fa-temperature-low', 'fa-faucet', 'fa-dumbbell'];
                                    $isCustom = !in_array($sf['icon'] ?? '', $knownIcons);
                                ?>
                                <select name="icon_select" class="form-select mb-2" onchange="toggleCustomIcon(this, 'edit_custom_icon_wrapper_<?= $sf['id'] ?>')">
                                    <option value="fa-shield-halved" <?= ($sf['icon'] ?? '') === 'fa-shield-halved' ? 'selected' : '' ?>>🛡️ Keamanan / CCTV (fa-shield-halved)</option>
                                    <option value="fa-motorcycle" <?= ($sf['icon'] ?? '') === 'fa-motorcycle' ? 'selected' : '' ?>>🏍️ Parkir Motor (fa-motorcycle)</option>
                                    <option value="fa-tshirt" <?= ($sf['icon'] ?? '') === 'fa-tshirt' ? 'selected' : '' ?>>👕 Laundry / Cuci (fa-tshirt)</option>
                                    <option value="fa-utensils" <?= ($sf['icon'] ?? '') === 'fa-utensils' ? 'selected' : '' ?>>🍳 Dapur Bersama (fa-utensils)</option>
                                    <option value="fa-couch" <?= ($sf['icon'] ?? '') === 'fa-couch' ? 'selected' : '' ?>>🛋️ Ruang Tamu / Sofa (fa-couch)</option>
                                    <option value="fa-leaf" <?= ($sf['icon'] ?? '') === 'fa-leaf' ? 'selected' : '' ?>>🍃 Taman / Area Hijau (fa-leaf)</option>
                                    <option value="fa-wifi" <?= ($sf['icon'] ?? '') === 'fa-wifi' ? 'selected' : '' ?>>📶 WiFi Bersama (fa-wifi)</option>
                                    <option value="fa-tv" <?= ($sf['icon'] ?? '') === 'fa-tv' ? 'selected' : '' ?>>📺 TV Area (fa-tv)</option>
                                    <option value="fa-temperature-low" <?= ($sf['icon'] ?? '') === 'fa-temperature-low' ? 'selected' : '' ?>>❄️ Kulkas / Pendingin (fa-temperature-low)</option>
                                    <option value="fa-faucet" <?= ($sf['icon'] ?? '') === 'fa-faucet' ? 'selected' : '' ?>>🚰 Dispenser Air (fa-faucet)</option>
                                    <option value="fa-dumbbell" <?= ($sf['icon'] ?? '') === 'fa-dumbbell' ? 'selected' : '' ?>>🏋️ Gym Mini (fa-dumbbell)</option>
                                    <option value="custom" <?= $isCustom ? 'selected' : '' ?>>✍️ Tulis Custom (FontAwesome class)...</option>
                                </select>
                                
                                <div id="edit_custom_icon_wrapper_<?= $sf['id'] ?>" style="display: <?= $isCustom ? 'block' : 'none' ?>;">
                                    <label class="form-label small text-muted">Nama Kelas FontAwesome (tanpa awalan 'fas', contoh: fa-bicycle)</label>
                                    <input type="text" name="icon_custom" class="form-control" value="<?= esc($sf['icon'] ?? '') ?>" placeholder="fa-bicycle">
                                </div>
                                <input type="hidden" name="icon" id="edit_final_icon_<?= $sf['id'] ?>" value="<?= esc($sf['icon'] ?? 'fa-circle-check') ?>">
                            </div>
                        </div>
                        <div class="modal-footer gap-2">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-primary-custom">
                                <i class="bi bi-check-lg me-1"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Load SweetAlert2 via CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function toggleCustomIcon(selectEl, wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        if (selectEl.value === 'custom') {
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
        }
    }

    function submitSharedForm(modalId) {
        const modal = document.getElementById(modalId);
        const select = modal.querySelector('select[name="icon_select"]');
        const customInput = modal.querySelector('input[name="icon_custom"]');
        const finalHidden = modal.querySelector('input[name="icon"]');
        
        if (select.value === 'custom') {
            finalHidden.value = customInput.value.trim() || 'fa-circle-check';
        } else {
            finalHidden.value = select.value;
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hapus Kamar Fasilitas
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const url = this.getAttribute('data-url');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: `Fasilitas "${nama}" akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d51717',
                    cancelButtonColor: '#175fd4',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });

        // Hapus Fasilitas Bersama
        const deleteSharedButtons = document.querySelectorAll('.btn-delete-shared');
        deleteSharedButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const url = this.getAttribute('data-url');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: `Fasilitas bersama "${nama}" akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d51717',
                    cancelButtonColor: '#175fd4',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search Fasilitas Kamar
        const searchInput = document.getElementById('searchFasilitas');
        const table = document.getElementById('tableFasilitas');
        if (searchInput && table) {
            const rows = table.querySelectorAll('tbody tr');
            searchInput.addEventListener('keyup', function() {
                const keyword = this.value.toLowerCase();
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        }

        // Search Fasilitas Bersama
        const searchSharedInput = document.getElementById('searchFasilitasBersama');
        const tableShared = document.getElementById('tableFasilitasBersama');
        if (searchSharedInput && tableShared) {
            const rowsShared = tableShared.querySelectorAll('tbody tr');
            searchSharedInput.addEventListener('keyup', function() {
                const keyword = this.value.toLowerCase();
                rowsShared.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>