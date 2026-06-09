<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>


<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard">
        <i class="bi bi-house"></i> Dashboard
    </a>
    <i class="bi bi-chevron-right"></i>
    <span>Data Penyewa</span>
</div>

<!-- CARD -->
<div class="table-card">

    <div class="table-card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 w-100">
        <div class="text-nowrap">
            <div class="table-card-title fw-bold" style="font-size: 1.15rem; color: #1e293b;">Data Penyewa</div>
            <div class="table-card-sub text-muted small">Total <?= count($penyewa) ?> penyewa</div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end" style="max-width: 500px;">
            <div class="input-group flex-grow-1" style="max-width: 260px;">
                <input type="text" id="searchPenyewa" class="form-control" placeholder="Cari nama penyewa...">
                <span class="input-group-text bg-light text-muted">
                    <i class="bi bi-search"></i>
                </span>
            </div>

            <button class="btn btn-primary btn-add text-nowrap" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Penyewa
            </button>
        </div>
    </div>

    <!-- ALERT -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success m-2">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- TABLE -->
    <div class="tbl-wrap">
        <table class="data-table" id="tablePenyewa">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Kamar</th>
                    <th>Tanggal Masuk</th>
                    <th class="text-center">Status Akun</th>
                    <th class="text-center" width="260">Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($penyewa)) : ?>
                    <?php $no = 1; ?>
                    <?php foreach ($penyewa as $p) : ?>

                        <tr>
                            <td><?= $no++ ?></td>

                            <td><?= esc($p['name']) ?></td>

                            <td><?= esc($p['email']) ?></td>

                            <td><?= esc($p['phone']) ?></td>

                            <td>
                                Kamar <?= esc($p['nomor_kamar']) ?>
                            </td>

                            <td>
                                <?= date('d M Y', strtotime($p['tanggal_masuk'])) ?>
                            </td>

                            <td class="text-center">
                                <?php if ($p['is_active'] == 1) : ?>
                                    <span class="badge bg-success">
                                        Aktif
                                    </span>
                                <?php else : ?>
                                    <span class="badge bg-danger">
                                        Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center">
                                <!-- EDIT -->
                                <button class="action-btn edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal<?= $p['id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- STATUS -->
                                <a href="/admin/penyewa/toggle-status/<?= $p['id'] ?>"
                                    class="action-btn <?= $p['is_active'] == 1 ? 'del' : 'edit' ?> btn-toggle-status"
                                    data-nama="<?= esc($p['name']) ?>">
                                    <i class="bi <?= $p['is_active'] == 1 ? 'bi-person-x' : 'bi-person-check' ?>"></i>
                                </a>

                                <!-- RESET PASSWORD -->
                                <a href="/admin/penyewa/reset-password/<?= $p['id'] ?>"
                                    class="action-btn btn-reset-password"
                                    data-nama="<?= esc($p['name']) ?>">
                                    <i class="bi bi-key"></i>
                                </a>

                                <!-- CHECKOUT -->
                                <a href="/admin/penyewa/checkout/<?= $p['id'] ?>"
                                    class="action-btn del btn-checkout"
                                    data-nama="<?= esc($p['name']) ?>">
                                    <i class="bi bi-box-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else : ?>

                    <tr>
                        <td colspan="8" class="text-center">
                            Belum ada data penyewa
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>


<!-- ================================= -->
<!-- ADD MODAL -->
<!-- ================================= -->

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="/admin/penyewa/store" method="post">

                <?= csrf_field() ?>

                <div class="modal-header">
                    <h5 class="modal-title">
                        Tambah Penyewa
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>No HP</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Kamar</label>

                            <select name="kamar_id" class="form-select" required>

                                <option value="">
                                    -- Pilih Kamar --
                                </option>

                                <?php foreach ($kamar_kosong as $k) : ?>

                                    <option value="<?= $k['id'] ?>">
                                        Kamar <?= $k['nomor_kamar'] ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Asal Kota</label>
                            <input type="text" name="asal_kota" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Status Pekerjaan</label>
                            <select name="status_pekerjaan" class="form-select">
                                <option value="" selected>-- Pilih Status Pekerjaan --</option>
                                <option value="bekerja">Bekerja</option>
                                <option value="pelajar/mahasiswa">Pelajar / Mahasiswa</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Status Pernikahan</label>

                            <select name="status_pernikahan" class="form-select">
                                <option value="" selected>-- Pilih Status Pernikahan --</option>
                                <option value="belum menikah">Belum Menikah</option>
                                <option value="menikah">Menikah</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Nomor Darurat</label>
                            <input type="text" name="nomor_darurat" class="form-control">
                        </div>

                        <div class="col-12 mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" rows="3" class="form-control"></textarea>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- ========================= -->
<!-- EDIT MODAL -->
<!-- ========================= -->
<?php foreach ($penyewa as $p) : ?>
    <div class="modal fade" id="editModal<?= $p['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form action="/admin/penyewa/update/<?= $p['id'] ?>" method="post">

                    <?= csrf_field() ?>

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Edit Penyewa
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Nama</label>
                                <input type="text"
                                    name="name"
                                    class="form-control"
                                    value="<?= esc($p['name']) ?>"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?= esc($p['email']) ?>"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>No HP</label>
                                <input type="text"
                                    name="phone"
                                    class="form-control"
                                    value="<?= esc($p['phone']) ?>"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Tanggal Masuk</label>
                                <input type="date"
                                    name="tanggal_masuk"
                                    class="form-control"
                                    value="<?= date('Y-m-d', strtotime($p['tanggal_masuk'])) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Asal Kota</label>
                                <input type="text"
                                    name="asal_kota"
                                    class="form-control"
                                    value="<?= esc($p['asal_kota']) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Status Pekerjaan</label>
                                <select name="status_pekerjaan" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="bekerja" <?= ($p['status_pekerjaan'] ?? '') == 'bekerja' ? 'selected' : '' ?>>Bekerja</option>
                                    <option value="pelajar/mahasiswa" <?= ($p['status_pekerjaan'] ?? '') == 'pelajar/mahasiswa' ? 'selected' : '' ?>>Pelajar/Mahasiswa</option>
                                    <option value="lainnya" <?= ($p['status_pekerjaan'] ?? '') == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Status Pernikahan</label>
                                <select name="status_pernikahan" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="belum menikah" <?= ($p['status_pernikahan'] ?? '') == 'belum menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                                    <option value="menikah" <?= ($p['status_pernikahan'] ?? '') == 'menikah' ? 'selected' : '' ?>>Menikah</option>
                                    <option value="lainnya" <?= ($p['status_pernikahan'] ?? '') == 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Nomor Darurat</label>
                                <input type="text"
                                    name="nomor_darurat"
                                    class="form-control"
                                    value="<?= esc($p['nomor_darurat']) ?>">
                            </div>

                            <div class="col-12 mb-3">
                                <label>Alamat</label>
                                <textarea
                                    name="alamat"
                                    class="form-control"
                                    rows="3"><?= esc($p['alamat']) ?></textarea>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchInput = document.getElementById('searchPenyewa');
        const table = document.getElementById('tablePenyewa');
        const rows = table.querySelectorAll('tbody tr');

        searchInput.addEventListener('keyup', function() {

            const keyword = this.value.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();

                if (text.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Toggle Status
        document.querySelectorAll('.btn-toggle-status').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const url = this.href;
                const nama = this.dataset.nama;

                Swal.fire({
                    title: 'Ubah Status?',
                    text: `Status penyewa "${nama}" akan diubah.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });

        // Reset Password
        document.querySelectorAll('.btn-reset-password').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const url = this.href;
                const nama = this.dataset.nama;

                Swal.fire({
                    title: 'Reset Password?',
                    text: `Password penyewa "${nama}" akan direset ke password default.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f39c12',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });

        // Checkout
        document.querySelectorAll('.btn-checkout').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const url = this.href;
                const nama = this.dataset.nama;

                Swal.fire({
                    title: 'Checkout Penyewa?',
                    text: `Penyewa "${nama}" akan dikeluarkan dari kamar.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Checkout!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });

    });
</script>

<?= $this->endSection() ?>