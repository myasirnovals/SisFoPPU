<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Login Sisfo Praktikum') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/unjani.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --page-bg: #0f172a;
            --panel: rgba(15, 23, 42, 0.84);
            --panel-border: rgba(148, 163, 184, 0.18);
            --text-main: #e2e8f0;
            --text-soft: #94a3b8;
            --accent: #38bdf8;
            --accent-strong: #0ea5e9;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--text-main);
            font-family: Inter, "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(56, 189, 248, 0.26), transparent 32%),
                radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.24), transparent 30%),
                linear-gradient(135deg, #020617 0%, #0f172a 45%, #111827 100%);
        }

        .login-shell {
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 24px;
        }

        .login-shell::before,
        .login-shell::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.08);
            filter: blur(10px);
            pointer-events: none;
        }

        .login-shell::before {
            width: 280px;
            height: 280px;
            top: -60px;
            right: -40px;
        }

        .login-shell::after {
            width: 220px;
            height: 220px;
            left: -50px;
            bottom: -60px;
        }

        .login-card {
            position: relative;
            z-index: 1;
            width: min(1100px, 100%);
            margin: 0 auto;
            border: 1px solid var(--panel-border);
            border-radius: 28px;
            overflow: hidden;
            background: var(--panel);
            backdrop-filter: blur(18px);
            box-shadow: 0 30px 80px rgba(2, 6, 23, 0.45);
        }

        .hero-panel {
            padding: 44px;
            background:
                linear-gradient(160deg, rgba(14, 165, 233, 0.18), rgba(15, 23, 42, 0.96)),
                url('https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80') center/cover;
            min-height: 100%;
        }

        .brand-mark {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(148, 163, 184, 0.22);
            display: grid;
            place-items: center;
            margin-bottom: 24px;
        }

        .brand-mark img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            color: var(--accent);
            background: rgba(14, 165, 233, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.18);
            font-size: 0.875rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .hero-title {
            font-size: clamp(2rem, 4vw, 3.6rem);
            line-height: 1.05;
            margin: 0 0 16px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .hero-copy {
            max-width: 520px;
            color: rgba(226, 232, 240, 0.82);
            font-size: 1.05rem;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 28px;
        }

        .stat-card {
            padding: 16px;
            border-radius: 18px;
            background: rgba(15, 23, 42, 0.56);
            border: 1px solid rgba(148, 163, 184, 0.16);
        }

        .stat-card strong {
            display: block;
            font-size: 1.25rem;
        }

        .stat-card span {
            color: var(--text-soft);
            font-size: 0.92rem;
        }

        .form-panel {
            padding: 44px;
            background: rgba(2, 6, 23, 0.54);
        }

        .login-form-wrap {
            max-width: 440px;
            margin: 0 auto;
        }

        .panel-title {
            font-size: 1.9rem;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .panel-subtitle {
            color: var(--text-soft);
            margin-bottom: 28px;
        }

        .form-label {
            color: #cbd5e1;
            font-weight: 600;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(148, 163, 184, 0.2);
            color: var(--text-main);
            border-radius: 14px;
            padding: 0.9rem 1rem;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.9);
            color: var(--text-main);
            border-color: rgba(56, 189, 248, 0.7);
            box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.18);
        }

        .btn-login {
            border: none;
            border-radius: 14px;
            padding: 0.95rem 1rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-strong) 100%);
            box-shadow: 0 18px 35px rgba(14, 165, 233, 0.28);
        }

        .btn-login:hover {
            filter: brightness(1.04);
        }

        .hint-box {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.74);
            border: 1px solid rgba(148, 163, 184, 0.15);
            color: #cbd5e1;
        }

        .alert-soft {
            background: rgba(14, 165, 233, 0.14);
            color: #dff7ff;
            border-color: rgba(56, 189, 248, 0.28);
        }

        .small-note {
            color: var(--text-soft);
            font-size: 0.92rem;
        }

        @media (max-width: 991.98px) {
            .hero-panel,
            .form-panel {
                padding: 28px;
            }

            .stat-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="login-shell">
    <div class="login-card row g-0">
        <div class="col-lg-7 hero-panel d-flex flex-column justify-content-between">
            <div>
                <div class="brand-mark">
                    <img src="<?= base_url('assets/images/unjani.png') ?>" alt="Logo Sisfo">
                </div>
                <div class="eyebrow">
                    <i class="bi bi-shield-lock-fill"></i>
                    Portal Sisfo Praktikum
                </div>
                <h1 class="hero-title">Masuk ke panel admin dan koordinator dari satu pintu.</h1>
                <p class="hero-copy">
                    Halaman ini menjadi gerbang utama aplikasi. Route utama sudah diarahkan ke login supaya alur akses lebih jelas sebelum masuk ke dashboard yang dilindungi filter role.
                </p>
            </div>

            <div class="stat-grid">
                <div class="stat-card">
                    <strong>Admin</strong>
                    <span>Pengelolaan data, template, dan pengguna.</span>
                </div>
                <div class="stat-card">
                    <strong>Koordinator</strong>
                    <span>Monitoring kelas, validasi, dan remedial.</span>
                </div>
                <div class="stat-card">
                    <strong>Terpusat</strong>
                    <span>Satu titik masuk untuk seluruh alur praktikum.</span>
                </div>
            </div>
        </div>

        <div class="col-lg-5 form-panel d-flex align-items-center">
            <div class="login-form-wrap w-100">
                <h2 class="panel-title">Login</h2>
                <p class="panel-subtitle">Gunakan akun yang terhubung ke backend autentikasi aplikasi.</p>

                <?php if (! empty($notice)): ?>
                    <div class="alert alert-soft border mb-4" role="alert">
                        <?= esc($notice) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('login') ?>" method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email atau username</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="nama@kampus.ac.id" autocomplete="username">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" autocomplete="current-password">
                    </div>

                    <button type="submit" class="btn btn-login btn-primary w-100">
                        Masuk ke Sistem
                    </button>
                </form>

                <div class="hint-box mt-4">
                    <div class="fw-semibold mb-1">Catatan</div>
                    <div class="small-note">
                        Route utama sudah mengarah ke halaman ini. Jika backend autentikasi ditambahkan nanti, form ini bisa langsung dihubungkan tanpa mengubah struktur route utama.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>