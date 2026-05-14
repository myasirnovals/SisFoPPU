<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/unjani.png') ?>">
    <title><?= $title ?? 'Admin Panel' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <?= $this->renderSection('styles') ?>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <img src="<?= base_url('assets/images/unjani.png') ?>" alt="Logo Sisfo" width="30" height="30" class="d-inline-block align-text-top me-2">
            <a class="navbar-brand" href="#">Sisfo Praktikum</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('admin/dashboard') ? 'active' : '' ?>" href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= url_is('admin/matakuliah') || url_is('admin/pengguna') ? 'active' : '' ?>" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Data Master
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item <?= url_is('admin/matakuliah') ? 'active bg-primary text-white' : '' ?>" href="<?= site_url('admin/matakuliah') ?>">Data Mata Kuliah</a></li>
                            <li><a class="dropdown-item <?= url_is('admin/pengguna') ? 'active bg-primary text-white' : '' ?>" href="<?= site_url('admin/pengguna') ?>">Manajemen Pengguna</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= url_is('admin/template') ? 'active' : '' ?>" href="<?= site_url('admin/template') ?>">Template Penilaian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="#">Logout</a>
                    </li>
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