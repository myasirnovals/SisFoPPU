<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #091120 0%, #102a43 100%);
            color: #e6eef7;
        }

        .dashboard-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .dashboard-card {
            width: min(840px, 100%);
            border-radius: 24px;
            background: rgba(10, 18, 33, 0.82);
            border: 1px solid rgba(148, 163, 184, 0.16);
            box-shadow: 0 24px 70px rgba(1, 6, 19, 0.38);
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: rgba(29, 155, 240, 0.12);
            color: #d7f0ff;
            border: 1px solid rgba(125, 211, 252, 0.2);
        }
    </style>
</head>
<body>
    <div class="dashboard-shell">
        <div class="dashboard-card p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <div class="role-badge mb-3">
                        <i class="bi bi-shield-check"></i>
                        <?= esc($roleLabel ?? 'Dashboard') ?>
                    </div>
                    <h1 class="h3 fw-bold mb-2"><?= esc($title ?? 'Dashboard') ?></h1>
                    <p class="text-white-50 mb-0">Selamat datang, <?= esc($username ?? 'Pengguna') ?>.</p>
                </div>
                <a href="<?= esc($logoutUrl ?? '#') ?>" class="btn btn-outline-light">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-3 rounded-4 bg-dark bg-opacity-25 border border-light border-opacity-10 h-100">
                        <div class="text-white-50 small mb-2">Role aktif</div>
                        <div class="fw-semibold"><?= esc($roleLabel ?? '-') ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded-4 bg-dark bg-opacity-25 border border-light border-opacity-10 h-100">
                        <div class="text-white-50 small mb-2">Pengguna login</div>
                        <div class="fw-semibold"><?= esc($username ?? '-') ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded-4 bg-dark bg-opacity-25 border border-light border-opacity-10 h-100">
                        <div class="text-white-50 small mb-2">Path dashboard</div>
                        <div class="fw-semibold"><?= esc($dashboardPath ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
