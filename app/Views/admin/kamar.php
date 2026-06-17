<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Data Kamar</span>
</div>

<div class="table-card">

    <div class="table-card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 w-100">
        <div class="text-nowrap">
            <div class="table-card-title fw-bold" style="font-size: 1.15rem; color: #1e293b;">Data Kamar Kost</div>
            <div class="table-card-sub text-muted small">Total <?= count($rooms) ?> kamar</div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end" style="max-width: 500px;">
            <div class="input-group flex-grow-1" style="max-width: 260px;">
                <input type="text" id="searchKamar" class="form-control" placeholder="Cari nomor kamar...">
                <span class="input-group-text bg-light text-muted">
                    <i class="bi bi-search"></i>
                </span>
            </div>

            <button class="btn btn-primary btn-add text-nowrap" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Kamar
            </button>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger border-0 shadow-sm m-3" style="border-radius: 12px;">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                <div>
                    <strong>Gagal menyimpan data:</strong>
                    <ul class="mb-0 mt-1">
                        <?php foreach (session()->getFlashdata('errors') as $e): ?>
                            <li><?= esc($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

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

    <!-- TABLE -->
    <div class="tbl-wrap">
        <table class="data-table" id="tableKamar">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No Kamar</th>
                    <th>Lantai</th>
                    <th>Luas</th>
                    <th>Harga</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($rooms)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: #6c757d;">
                            <i class="bi bi-info-circle" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                            Data kamar tidak ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1;
                    foreach ($rooms as $r): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $r['nomor_kamar'] ?></td>
                            <td><?= $r['lantai'] ?></td>
                            <td><?= $r['luas'] ?></td>
                            <td>Rp <?= number_format($r['harga']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-<?= esc($r['badge_class']) ?>">
                                    <i class="bi <?= esc($r['icon']) ?> me-1"></i><?= esc(ucfirst($r['status'])) ?>
                                </span>
                            </td>
                            <td class="d-flex justify-content-center">
                                <div style="display:flex;gap:.35rem; margin: 0 auto; ">
                                    <button class="action-btn edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal<?= $r['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <?php if ($r['status'] === 'kosong'): ?>
                                        <button class="action-btn del btn-delete btn-delete-kamar"
                                            data-url="/admin/kamar/delete/<?= $r['id'] ?>"
                                            data-nama="Kamar <?= $r['nomor_kamar'] ?>">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="action-btn del btn-delete btn-locked" title="Kamar masih terisi">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                    <?php endif; ?>

                                    <!-- <button class="action-btn del btn-delete"
                                        data-url="/admin/kamar/delete/<?= $r['id'] ?>"
                                        data-nama="<?= $r['nomor_kamar'] ?>">
                                        <i class="bi bi-trash3"></i>
                                    </button> -->
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        </table>
    </div>
</div>

<!-- ============= MODAL TAMBAH ============= -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title d-flex align-items-center">
                    <span class="modal-icon add"><i class="bi bi-plus-circle-fill"></i></span>
                    Tambah Kamar
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/kamar/store" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Nomor Kamar</label>
                            <input type="text" name="nomor_kamar" class="form-control mb-2" placeholder="Nomor Kamar Kost" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Tipe Kamar</label>
                            <select name="tipe" class="form-select mb-2" required>
                                <option value="Standard">Standard</option>
                                <option value="Deluxe">Deluxe</option>
                                <option value="Premium">Premium</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Lantai Kamar</label>
                            <input type="number" name="lantai" class="form-control mb-2" placeholder="Lantai Kamar Kost" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Luas Kamar</label>
                            <input type="text" name="luas" class="form-control mb-2" placeholder="Luas Kamar Kost">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Harga Kamar</label>
                            <input type="text" name="harga" class="form-control mb-2" placeholder="Harga Kamar Kost" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Foto Kamar</label>
                            <input type="file" name="foto" class="form-control mb-2" accept="image/*">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label">Deskripsi Kamar</label>
                            <textarea name="deskripsi" class="form-control mb-2" placeholder="Deskripsi Kamar Kost"></textarea>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label fw-bold">Fasilitas Kamar</label>

                            <!-- Wrapper dengan scrollbar -->
                            <div style="height: 150px; overflow-y: auto; overflow-x: hidden; border: 1px solid #dee2e6; padding: 10px; border-radius: 5px;">
                                <div class="row">
                                    <?php foreach ($facilities as $f): ?>
                                        <div class="col-6 mb-2"> <!-- col-6 membagi menjadi 2 kolom -->
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="fasilitas[]"
                                                    value="<?= $f['id'] ?>"
                                                    id="faskam<?= $f['id'] ?>">
                                                <label class="form-check-label" for="faskam<?= $f['id'] ?>">
                                                    <?= esc($f['nama_fasilitas']) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <small class="text-muted">* Gulir ke bawah untuk melihat lebih banyak</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button class="btn-primary-custom">
                        <i class="bi bi-check-lg me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ============= MODAL EDIT ============= -->

<?php foreach ($rooms as $r): ?>
    <div class="modal fade" id="editModal<?= $r['id'] ?>">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title d-flex align-items-center">
                        <span class="modal-icon add"><i class="bi bi-pencil-fill"></i></span>
                        Edit Kamar
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- ✅ action diarahkan ke update dengan ID kamar -->
                <form action="/admin/kamar/update/<?= $r['id'] ?>" method="post" enctype="multipart/form-data">
                     <?= csrf_field() ?>
                     <div class="modal-body">
                         <div class="row">
                             <div class="col-md-6 mb-2">
                                 <label class="form-label">Nomor Kamar</label>
                                 <input type="text" name="nomor_kamar" class="form-control mb-2" value="<?= $r['nomor_kamar'] ?>" required>
                             </div>
                             <div class="col-md-6 mb-2">
                                 <label class="form-label">Tipe Kamar</label>
                                 <select name="tipe" class="form-select mb-2" required>
                                     <option value="Standard" <?= ($r['tipe'] ?? '') === 'Standard' ? 'selected' : '' ?>>Standard</option>
                                     <option value="Deluxe" <?= ($r['tipe'] ?? '') === 'Deluxe' ? 'selected' : '' ?>>Deluxe</option>
                                     <option value="Premium" <?= ($r['tipe'] ?? '') === 'Premium' ? 'selected' : '' ?>>Premium</option>
                                 </select>
                             </div>
                             <div class="col-md-6 mb-2">
                                 <label class="form-label">Lantai Kamar</label>
                                 <input type="number" name="lantai" class="form-control mb-2" value="<?= $r['lantai'] ?>" required>
                             </div>
                             <div class="col-md-6 mb-2">
                                 <label class="form-label">Luas Kamar</label>
                                 <input type="text" name="luas" class="form-control mb-2" value="<?= $r['luas'] ?>">
                             </div>
                             <div class="col-md-6 mb-2">
                                 <label class="form-label">Harga Kamar</label>
                                 <input type="text" name="harga" class="form-control mb-2" value="<?= $r['harga'] ?>" required>
                             </div>
                             <div class="col-md-6 mb-2">
                                 <label class="form-label">Foto Kamar</label>
                                 <input type="file" name="foto" class="form-control mb-2" accept="image/*">
                                 <?php if (!empty($r['foto'])): ?>
                                     <small class="text-muted d-block">Foto saat ini: <?= esc($r['foto']) ?></small>
                                 <?php endif; ?>
                             </div>
                             <div class="col-md-12 mb-2">
                                 <label class="form-label">Deskripsi Kamar</label>
                                 <textarea name="deskripsi" class="form-control mb-2"><?= $r['deskripsi'] ?></textarea>
                             </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label fw-bold">Fasilitas Kamar</label>
                                <div style="height:150px;overflow-y:auto;overflow-x:hidden;border:1px solid #dee2e6;padding:10px;border-radius:5px;">
                                    <div class="row">
                                        <?php
                                        // ✅ cast ke string sekali, di luar inner loop
                                        $checked = array_map('strval', $roomFacilities[$r['id']] ?? []);
                                        ?>
                                        <?php foreach ($facilities as $f): ?>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="fasilitas[]"
                                                        value="<?= $f['id'] ?>"
                                                        id="editfaskam_<?= $r['id'] ?>_<?= $f['id'] ?>"
                                                        <?= in_array((string)$f['id'], $checked) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="editfaskam_<?= $r['id'] ?>_<?= $f['id'] ?>">
                                                        <?= esc($f['nama_fasilitas']) ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <small class="text-muted">* Gulir ke bawah untuk melihat lebih banyak</small>
                            </div>
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


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const deleteButtons = document.querySelectorAll('.btn-delete-kamar');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const url = this.getAttribute('data-url');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: `Fasilitas "${nama}" akan dihapus permanen!`,
                    icon: 'warning',
                    color: 'd51717',
                    showCancelButton: true,
                    confirmButtonColor: '#d51717',
                    cancelButtonColor: '#175fd4',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika klik Ya, arahkan ke URL penghapusan
                        window.location.href = url;
                    }
                });
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cari semua tombol yang memiliki class btn-locked
        const lockedButtons = document.querySelectorAll('.btn-locked');

        lockedButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // Mencegah aksi bawaan jika ada

                // Tampilkan SweetAlert
                Swal.fire({
                    icon: 'info',
                    iconColor: '#c06eff', // Mengubah warna ikon informasi menjadi ungu
                    title: 'Informasi',
                    text: 'Kamar tidak bisa dihapus karena status kamar masih terisi.',
                    confirmButtonColor: '#c06eff', // Mengubah warna tombol menjadi ungu
                    confirmButtonText: 'Batal'
                });
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchInput = document.getElementById('searchKamar');
        const table = document.getElementById('tableKamar');
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

<?= $this->endSection() ?>