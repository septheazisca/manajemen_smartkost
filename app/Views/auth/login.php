<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SmartKost – Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>

<body class="login">

    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="login-wrapper">
        <div class="card-login">

            <!-- Brand -->
            <div class="brand-logo"><i class="bi bi-house-heart-fill"></i></div>
            <div class="brand-name">Smart<span>Kost</span></div>
            <div class="brand-sub">Sistem Informasi Manajemen Kost</div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger p-2 mt-3" style="font-size: 12px;">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/login">
                <!-- Form -->
                <div class="my-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email anda" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" id="pwdInput" placeholder="Masukkan password" />
                        <button class="btn-eye" onclick="togglePwd()" type="button">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>


                <button class="btn-primary-custom mb-3" type="submit">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setRole(el, role) {
            document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
        }

        function togglePwd() {
            const i = document.getElementById('pwdInput');
            const eye = document.getElementById('eyeIcon');
            if (i.type === 'password') {
                i.type = 'text';
                eye.className = 'bi bi-eye-slash';
            } else {
                i.type = 'password';
                eye.className = 'bi bi-eye';
            }
        }
    </script>
</body>

</html>