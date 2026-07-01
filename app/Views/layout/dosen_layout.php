<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard Dosen') ?> | Sisfo Praktikum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
</head>

<body class="bg-light">

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- NAVBAR DOSEN -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url('dosen/dashboard') ?>">
                <i class="bi bi-mortarboard-fill"></i>
                <span class="fw-bold">Sisfo Praktikum</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarDosen">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarDosen">
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    <li class="nav-item">
                        <a class="nav-link px-3 rounded-pill <?= ($activeMenu ?? '') === 'dashboard' ? 'active bg-white bg-opacity-10 fw-semibold' : '' ?>"
                            href="<?= site_url('dosen/dashboard') ?>">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 rounded-pill <?= ($activeMenu ?? '') === 'kelas-saya' ? 'active bg-white bg-opacity-10 fw-semibold' : '' ?>"
                            href="<?= site_url('dosen/kelas-saya') ?>">
                            <i class="bi bi-journal-text me-1"></i>Kelas Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 rounded-pill <?= ($activeMenu ?? '') === 'validasi' ? 'active bg-white bg-opacity-10 fw-semibold' : '' ?>"
                            href="<?= site_url('dosen/validasi') ?>">
                            <i class="bi bi-shield-check me-1"></i>Validasi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 rounded-pill <?= ($activeMenu ?? '') === 'remedial' ? 'active bg-white bg-opacity-10 fw-semibold' : '' ?>"
                            href="<?= site_url('dosen/remedial') ?>">
                            <i class="bi bi-arrow-repeat me-1"></i>Remedial
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-outline-light btn-sm rounded-pill px-3" href="<?= site_url('logout') ?>">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="container-fluid py-4 px-4">
        <?= $this->renderSection('content') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>