<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<div class="breadcrumb-custom mb-3">
    <a href="/admin/dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <i class="bi bi-chevron-right"></i>
    <span>Fasilitas Kamar</span>
</div>

<!-- TABLE CARD -->
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
                    <?php $no = 1;
                    foreach ($facilities as $f): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $f['nama_fasilitas'] ?></td>
                            <td>
                                <div style="display:flex;gap:.35rem;">
                                    <button class="action-btn edit" title="Edit" data-bs-toggle="modal" data-bs-target="#editModal<?= $f['id'] ?>"><i class="bi bi-pencil"></i></button>
                                    <button type="button" class="action-btn del btn-delete" title="Hapus" data-url="/admin/fasilitas/delete/<?= $f['id'] ?>" data-nama="<?= $f['nama_fasilitas'] ?>"> <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 2rem; color: #6c757d;">
                            <i class="bi bi-info-circle" style="font-size: 1.5rem; display: block; mb-2;"></i>
                            Data tidak tersedia
                        </td>
                    </tr>
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
                    Tambah Fasilitas
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/fasilitas/store" method="post">
                <div class="modal-body">
                    <div class="row">
                        <label class="form-label">Nama Fasilitas</label>
                        <input type="text" name="nama_fasilitas" class="form-control mb-2" placeholder="Nama fasilitas">
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
<?php foreach ($facilities as $f): ?>
    <div class="modal fade" id="editModal<?= $f['id'] ?>">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title d-flex align-items-center">
                        <span class="modal-icon add"><i class="bi bi-plus-circle-fill"></i></span>
                        Edit Fasilitas
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/admin/fasilitas/update/<?= $f['id'] ?>" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <label class="form-label">Nama Fasilitas</label>
                            <input type="text" name="nama_fasilitas" class="form-control mb-2" value="<?= $f['nama_fasilitas'] ?>">
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
<?php endforeach; ?>


<!-- Load SweetAlert2 via CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const deleteButtons = document.querySelectorAll('.btn-delete');

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
    function openDeleteModal(id) {
        const r = rooms.find(x => x.id === id);
        deleteTargetId = id;
        document.getElementById('delTarget').textContent = r ? r.nomor : '';
        modalDel.show();
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchInput = document.getElementById('searchFasilitas');
        const table = document.getElementById('tableFasilitas');
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