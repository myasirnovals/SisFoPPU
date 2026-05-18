<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard Asisten Praktikum') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-dark-subtle sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand fw-semibold" href="<?= site_url('assistant/dashboard') ?>">
                <i class="bi bi-clipboard-data me-1"></i> Dashboard Asisten Praktikum
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#assistantNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="assistantNavbar">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <li class="nav-item"><a class="nav-link" href="#ringkasan">Ringkasan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kelas">Kelas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#absensi">Absensi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#nilai">Nilai</a></li>
                    <li class="nav-item"><a class="nav-link" href="#remedial">Remedial</a></li>
                    <li class="nav-item"><a class="nav-link" href="#aktivitas">Aktivitas</a></li>
                    <li class="nav-item"><a class="nav-link text-warning-emphasis" href="<?= site_url('logout') ?>">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-4 px-3 px-lg-4">
        <?= $this->renderSection('content') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>