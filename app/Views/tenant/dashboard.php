<!DOCTYPE html>
<html>

<head>
    <title>Penyewa Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-dark bg-success">
        <div class="container">
            <span class="navbar-brand">Penyewa Dashboard</span>
            <a href="/logout" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">

        <h3>Halo, <?= session()->get('name') ?></h3>

        <div class="card p-3 shadow">
            <h5>Status Kamar</h5>
            <p>Info kamar yang kamu tempati</p>
        </div>

        <div class="card p-3 shadow mt-3">
            <h5>Pembayaran</h5>
            <p>Lihat tagihan & riwayat bayar</p>
        </div>

        <div class="card p-3 shadow mt-3">
            <h5>Komplain</h5>
            <p>Laporkan kerusakan + upload gambar</p>
        </div>

    </div>

</body>

</html>