<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard Mahasiswa') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --student-navy: #0f172a;
            --student-teal: #0f766e;
            --student-gold: #f59e0b;
            --student-coral: #ef4444;
            --student-ink: #111827;
            --student-muted: #6b7280;
        }

        body {
            font-family: "Plus Jakarta Sans", "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(14, 116, 144, 0.16), transparent 45%),
                radial-gradient(circle at 85% 15%, rgba(245, 158, 11, 0.14), transparent 35%),
                radial-gradient(circle at 12% 85%, rgba(239, 68, 68, 0.12), transparent 40%),
                linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            color: var(--student-ink);
            min-height: 100vh;
        }

        .navbar-student {
            background: linear-gradient(135deg, #0b1220 0%, #1e293b 55%, #0f766e 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.18);
        }

        .navbar-student .nav-link {
            color: rgba(255, 255, 255, 0.78);
        }

        .navbar-student .nav-link:hover,
        .navbar-student .nav-link.active {
            color: #fff;
        }

        .page-shell {
            width: min(1600px, 100%);
            margin: 0 auto;
        }

        .nav-quick {
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 0.25rem 0.65rem;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-student sticky-top">
        <div class="container-fluid page-shell px-3 px-lg-4 py-2">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="<?= site_url('mahasiswa/dashboard') ?>">
                <span class="rounded-4 d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(255,255,255,0.12);">
                    <i class="bi bi-person-badge"></i>
                </span>
                Sisfo Praktikum
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMahasiswa">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMahasiswa">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <li class="nav-item"><a class="nav-link" href="#ringkasan">Ringkasan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#praktikum">Praktikum</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kehadiran">Kehadiran</a></li>
                    <li class="nav-item"><a class="nav-link" href="#nilai">Nilai</a></li>
                    <li class="nav-item"><a class="nav-link" href="#remedial">Remedial</a></li>
                    <li class="nav-item"><a class="nav-link" href="#notifikasi">Notifikasi</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-light btn-sm nav-quick" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
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
