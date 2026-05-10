<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">Admin Dashboard</span>
            <a href="/logout" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">

        <h3>Selamat Datang, <?= session()->get('name') ?></h3>

        <div class="row mt-4">

            <div class="col-md-4">
                <div class="card p-3 shadow">
                    <h5>Kelola Kamar</h5>
                    <p>Tambah, edit, hapus kamar</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 shadow">
                    <h5>Keuangan</h5>
                    <p>Uang masuk & keluar</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 shadow">
                    <h5>Laporan</h5>
                    <p>Export PDF / Excel</p>
                </div>
            </div>

        </div>

    </div>

</body>

</html>