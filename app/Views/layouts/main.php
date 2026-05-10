<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SmartKost</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>

<body>

    <?= $this->include('layouts/sidebar') ?>

    <div class="main-wrapper">

        <?= $this->include('layouts/topbar') ?>

        <main class="page-content">

            <?= $this->renderSection('content') ?>

        </main>
    </div>

    <!-- <script src="script.js"></script> -->
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('show');
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</html>