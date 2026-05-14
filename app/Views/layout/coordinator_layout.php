<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/unjani.png') ?>">
    <title><?= $title ?? 'Coordinator Panel' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <?= $this->renderSection('styles') ?>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <img src="<?= base_url('assets/images/unjani.png') ?>" alt="Logo Sisfo" width="30" height="30" class="d-inline-block align-text-top me-2">
            <a class="navbar-brand fw-semibold" href="<?= site_url('coordinator/dashboard') ?>">Koordinator Praktikum</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#coordinatorNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="coordinatorNavbar">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link <?= url_is('coordinator/dashboard') ? 'active' : '' ?>" href="<?= site_url('coordinator/dashboard') ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link <?= url_is('coordinator/classes') ? 'active' : '' ?>" href="<?= site_url('coordinator/classes') ?>">Monitoring Kelas</a></li>
                    <li class="nav-item"><a class="nav-link <?= url_is('coordinator/attention') ? 'active' : '' ?>" href="<?= site_url('coordinator/attention') ?>">Perlu Perhatian</a></li>
                    <li class="nav-item"><a class="nav-link <?= url_is('coordinator/remedial') ? 'active' : '' ?>" href="<?= site_url('coordinator/remedial') ?>">Remedial</a></li>
                    <li class="nav-item"><a class="nav-link <?= url_is('coordinator/validation') ? 'active' : '' ?>" href="<?= site_url('coordinator/validation') ?>">Validasi Nilai</a></li>
                    <li class="nav-item"><a class="nav-link <?= url_is('coordinator/activity') ? 'active' : '' ?>" href="<?= site_url('coordinator/activity') ?>">Aktivitas</a></li>
                    <li class="nav-item"><a class="nav-link text-light-emphasis" href="#">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4 px-3 px-lg-4">
        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>