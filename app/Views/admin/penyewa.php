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

    <!-- HEADER -->
    <div class="table-card-header">
        <div>
            <div class="table-card-title">Data Penyewa</div>
            <div class="table-card-sub">
                Total <?= count($penyewa) ?> penyewa
            </div>
        </div>

        <div class="toolbar">
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-lg"></i> Tambah Penyewa
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
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Kamar</th>
                    <th>Tanggal Masuk</th>
                    <th>Status Akun</th>
                    <th width="260">Aksi</th>
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

                            <td>
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

                            <td>
                                <!-- EDIT -->
                                <button class="action-btn edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal<?= $p['id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- STATUS -->
                                <a href="/admin/penyewa/toggle-status/<?= $p['id'] ?>"
                                    class="action-btn <?= $p['is_active'] == 1 ? 'del' : 'edit' ?>">
                                    <i class="bi <?= $p['is_active'] == 1 ? 'bi-person-x' : 'bi-person-check' ?>"></i>
                                </a>

                                <!-- RESET PASSWORD -->
                                <a href="/admin/penyewa/reset-password/<?= $p['id'] ?>"
                                    class="action-btn"
                                    onclick="return confirm('Reset password penyewa?')">
                                    <i class="bi bi-key"></i>
                                </a>

                                <!-- CHECKOUT -->
                                <a href="/admin/penyewa/checkout/<?= $p['id'] ?>"
                                    class="action-btn del"
                                    onclick="return confirm('Checkout penyewa ini?')">
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
                                <option value="Belum Menikah">Belum Menikah</option>
                                <option value="Menikah">Menikah</option>
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
                                    <option value=""
                                        <?= empty($p['status_pekerjaan']) ? 'selected' : '' ?>>
                                        -- Pilih Status Pekerjaan --
                                    </option>
                                    <option value="bekerja"
                                        <?= $p['status_pekerjaan'] == 'bekerja' ? 'selected' : '' ?>>
                                        Bekerja
                                    </option>
                                    <option value="pelajar/mahasiswa"
                                        <?= $p['status_pekerjaan'] == 'pelajar/mahasiswa' ? 'selected' : '' ?>>
                                        Pelajar / Mahasiswa
                                    </option>
                                    <option value="lainnya"
                                        <?= $p['status_pekerjaan'] == 'lainnya' ? 'selected' : '' ?>>
                                        Lainnya
                                    </option>

                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Status Pernikahan</label>
                                <select name="status_pernikahan" class="form-select">

                                    <option value="Belum Menikah"
                                        <?= $p['status_pernikahan'] == 'Belum Menikah' ? 'selected' : '' ?>>
                                        Belum Menikah
                                    </option>

                                    <option value="Menikah"
                                        <?= $p['status_pernikahan'] == 'Menikah' ? 'selected' : '' ?>>
                                        Menikah
                                    </option>

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

<?= $this->endSection() ?>