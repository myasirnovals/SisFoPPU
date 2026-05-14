<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistem Penilaian Praktikum</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/dashboard') ?>">
            Penilaian Praktikum
        </a>

        <div class="ms-auto">
            <a href="<?= base_url('/logout') ?>" class="btn btn-light btn-sm">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h4 class="fw-bold">Dashboard</h4>

            <p class="mb-1">
                Selamat datang,
                <strong><?= esc(session()->get('name')) ?></strong>
            </p>

            <p class="mb-1">
                Email:
                <strong><?= esc(session()->get('email')) ?></strong>
            </p>

            <p class="mb-1">
                Username:
                <strong><?= esc(session()->get('username')) ?></strong>
            </p>

            <p class="mb-0">
                Role:
                <?php foreach ((session()->get('roles') ?? []) as $role) : ?>
                    <span class="badge bg-primary"><?= esc($role) ?></span>
                <?php endforeach; ?>
            </p>
        </div>
    </div>

    <div class="row mt-4">

        <?php if (in_array('admin', session()->get('roles') ?? [])) : ?>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold">Admin</h6>
                        <p class="text-muted mb-0">
                            Kelola user, role, master data, dan konfigurasi sistem.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (in_array('dosen', session()->get('roles') ?? []) || in_array('koordinator', session()->get('roles') ?? [])) : ?>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold">Dosen / Koordinator</h6>
                        <p class="text-muted mb-0">
                            Validasi nilai, template penilaian, dan monitoring praktikum.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (in_array('asisten', session()->get('roles') ?? [])) : ?>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold">Asisten Praktikum</h6>
                        <p class="text-muted mb-0">
                            Input kehadiran, nilai modul, dan catatan praktikum.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (in_array('mahasiswa', session()->get('roles') ?? [])) : ?>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold">Mahasiswa</h6>
                        <p class="text-muted mb-0">
                            Lihat nilai, kehadiran, status remedial, dan hasil akhir.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

</div>

</body>
</html>