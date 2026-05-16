<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard Dosen') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.12), transparent 35%),
                radial-gradient(circle at bottom left, rgba(37, 99, 235, 0.14), transparent 35%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
        }

        .navbar-dosen {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #334155 100%);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.14);
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.12);
        }

        .page-shell {
            width: min(1600px, 100%);
            margin: 0 auto;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-dosen sticky-top">
        <div class="container-fluid page-shell px-3 px-lg-4 py-2">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="<?= site_url('dosen/dashboard') ?>">
                <span class="brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
                Sisfo Praktikum
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarDosen">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarDosen">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <li class="nav-item"><a class="nav-link <?= url_is('dosen/dashboard') ? 'active' : '' ?>" href="<?= site_url('dosen/dashboard') ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('dosen/dashboard#kelas-saya') ?>">Kelas Saya</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('dosen/dashboard#validasi-nilai') ?>">Validasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('dosen/dashboard#remedial') ?>">Remedial</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-light btn-sm" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="page-shell px-3 px-lg-4 py-4 py-lg-5">
        <?= $this->renderSection('content') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>