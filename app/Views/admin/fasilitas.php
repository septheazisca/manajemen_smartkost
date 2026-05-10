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
    <div class="table-card-header">
        <div>
            <div class="table-card-title">Data Fasilitas Kamar</div>
            <div class="table-card-sub">Total <?= count($facilities) ?> fasilitas</div>
        </div>

        <div class="toolbar">
            <button class="btn-add" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-lg"></i> Tambah Fasilitas
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="tbl-wrap">
        <table class="data-table">
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
<?= $this->endSection() ?>