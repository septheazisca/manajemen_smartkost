<!DOCTYPE html>
<html>

<head>
    <title>PJ Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <span class="navbar-brand">PJ Dashboard</span>
            <a href="/logout" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">

        <h3>Halo, <?= session()->get('name') ?></h3>

        <div class="card p-3 shadow mt-3">
            <h5>Maintenance</h5>
            <p>Input perbaikan kamar, AC, listrik</p>
        </div>

        <div class="card p-3 shadow mt-3">
            <h5>Monitoring Kamar</h5>
            <p>Status kamar & laporan kerusakan</p>
        </div>

    </div>

</body>

</html>