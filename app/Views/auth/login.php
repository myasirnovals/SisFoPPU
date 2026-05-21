<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Login - Sistem Informasi Penilaian Praktikum') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-1: #07111f;
            --bg-2: #10243d;
            --card: rgba(9, 18, 33, 0.78);
            --card-border: rgba(148, 163, 184, 0.18);
            --text: #e5eef9;
            --muted: #9fb0c5;
            --accent: #1d9bf0;
            --accent-strong: #146fb3;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Inter, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(29, 155, 240, 0.24), transparent 32%),
                radial-gradient(circle at bottom right, rgba(20, 111, 179, 0.26), transparent 30%),
                linear-gradient(135deg, var(--bg-1) 0%, var(--bg-2) 100%);
        }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .auth-card {
            width: min(1120px, 100%);
            border-radius: 28px;
            overflow: hidden;
            background: var(--card);
            border: 1px solid var(--card-border);
            box-shadow: 0 24px 80px rgba(1, 6, 19, 0.45);
            backdrop-filter: blur(16px);
        }

        .hero-panel {
            min-height: 100%;
            padding: 42px;
            background:
                linear-gradient(160deg, rgba(29, 155, 240, 0.2), rgba(3, 7, 18, 0.9)),
                linear-gradient(145deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.01));
            position: relative;
        }

        .hero-panel::before {
            content: '';
            position: absolute;
            inset: auto -40px -40px auto;
            width: 180px;
            height: 180px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.05);
            filter: blur(12px);
        }

        .brand-badge {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: grid;
            place-items: center;
            background: rgba(15, 23, 42, 0.72);
            border: 1px solid rgba(148, 163, 184, 0.22);
            margin-bottom: 24px;
        }

        .brand-badge i {
            font-size: 1.8rem;
            color: #7dd3fc;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(29, 155, 240, 0.12);
            border: 1px solid rgba(125, 211, 252, 0.18);
            color: #c4e7ff;
            font-size: 0.9rem;
            margin-bottom: 18px;
        }

        .hero-title {
            margin: 0 0 14px;
            font-size: clamp(2rem, 4vw, 3.4rem);
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .hero-copy {
            max-width: 540px;
            color: rgba(229, 238, 249, 0.82);
            line-height: 1.75;
            font-size: 1.02rem;
            margin-bottom: 24px;
        }

        .feature-list {
            display: grid;
            gap: 12px;
            margin-top: 24px;
        }

        .feature-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.48);
            border: 1px solid rgba(148, 163, 184, 0.14);
        }

        .feature-item i {
            color: #7dd3fc;
            font-size: 1.05rem;
            margin-top: 2px;
        }

        .form-panel {
            padding: 42px;
            background: rgba(2, 6, 23, 0.5);
        }

        .form-wrap {
            width: min(100%, 420px);
            margin: 0 auto;
        }

        .panel-title {
            font-size: 1.9rem;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -0.03em;
        }

        .panel-subtitle {
            color: var(--muted);
            margin-bottom: 24px;
        }

        .form-label {
            color: #dbe7f3;
            font-weight: 600;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.72);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 14px;
            color: var(--text);
            padding: 0.92rem 1rem;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.9);
            color: var(--text);
            border-color: rgba(125, 211, 252, 0.8);
            box-shadow: 0 0 0 0.2rem rgba(29, 155, 240, 0.2);
        }

        .btn-login {
            border: 0;
            border-radius: 14px;
            padding: 0.92rem 1rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-strong) 100%);
            box-shadow: 0 16px 30px rgba(29, 155, 240, 0.25);
        }

        .btn-login:hover {
            filter: brightness(1.03);
        }

        .muted-link {
            color: #a8c9df;
            text-decoration: none;
        }

        .muted-link:hover {
            color: #d6ecfb;
            text-decoration: underline;
        }

        .auth-note {
            color: var(--muted);
            font-size: 0.93rem;
        }

        .alert-soft {
            background: rgba(29, 155, 240, 0.12);
            color: #e5f5ff;
            border-color: rgba(125, 211, 252, 0.25);
        }

        @media (max-width: 991.98px) {
            .hero-panel,
            .form-panel {
                padding: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <div class="auth-card row g-0 align-items-stretch">
            <div class="col-lg-7 hero-panel d-flex flex-column justify-content-between">
                <div>
                    <div class="brand-badge">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div class="eyebrow">
                        <i class="bi bi-journal-check"></i>
                        Portal Praktikum Terpadu
                    </div>
                    <h1 class="hero-title">Sistem Informasi Penilaian Praktikum</h1>
                    <p class="hero-copy">
                        Login untuk mengelola kehadiran, nilai, remedial, dan laporan praktikum dalam satu alur kerja yang rapi, aman, dan mudah dikembangkan ke RBAC.
                    </p>
                </div>

                <div class="feature-list">
                    <div class="feature-item">
                        <i class="bi bi-shield-lock"></i>
                        <div>
                            <div class="fw-semibold">Keamanan login</div>
                            <div class="text-white-50 small">Password diverifikasi dengan hashing dan session diperbarui setelah login berhasil.</div>
                        </div>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-diagram-3"></i>
                        <div>
                            <div class="fw-semibold">Siap RBAC</div>
                            <div class="text-white-50 small">Role admin, koordinator, dosen, asisten, dan mahasiswa sudah dipetakan sejak awal.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 form-panel d-flex align-items-center">
                <div class="form-wrap">
                    <h2 class="panel-title">Masuk</h2>
                    <p class="panel-subtitle">Gunakan NIM/NID 10 digit yang terdaftar untuk mengakses dashboard sesuai role Anda.</p>

                    <?php if ($success = session()->getFlashdata('success')): ?>
                        <div class="alert alert-success mb-3" role="alert">
                            <?= esc($success) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error = session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger mb-3" role="alert">
                            <?= esc($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($validation) && $validation !== null && $validation->getErrors() !== []): ?>
                        <div class="alert alert-warning mb-3" role="alert">
                            <div class="fw-semibold mb-2">Periksa kembali isian berikut:</div>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($validation->getErrors() as $message): ?>
                                    <li><?= esc($message) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('login') ?>" method="post" novalidate>
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="identity" class="form-label">NIM/NID</label>
                            <input
                                type="text"
                                class="form-control <?= isset($validation) && $validation !== null && $validation->hasError('identity') ? 'is-invalid' : '' ?>"
                                id="identity"
                                name="identity"
                                value="<?= esc(old('identity', $identity ?? '')) ?>"
                                autocomplete="username"
                                inputmode="numeric"
                                maxlength="10"
                                pattern="\d{10}"
                                placeholder="contoh: 1234567890"
                            >
                            <?php if (isset($validation) && $validation !== null && $validation->hasError('identity')): ?>
                                <div class="invalid-feedback">
                                    <?= esc($validation->getError('identity')) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input
                                type="password"
                                class="form-control <?= isset($validation) && $validation !== null && $validation->hasError('password') ? 'is-invalid' : '' ?>"
                                id="password"
                                name="password"
                                autocomplete="current-password"
                                placeholder="Masukkan password"
                            >
                            <?php if (isset($validation) && $validation !== null && $validation->hasError('password')): ?>
                                <div class="invalid-feedback">
                                    <?= esc($validation->getError('password')) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 small">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="rememberMe" name="remember_me">
                                <label class="form-check-label text-white-50" for="rememberMe">Ingat saya</label>
                            </div>
                            <a href="#" class="muted-link" onclick="return false;">Lupa password?</a>
                        </div>

                        <button type="submit" class="btn btn-login btn-primary w-100">
                            Login
                        </button>
                    </form>

                    <p class="auth-note mt-4 mb-0">
                        Akun admin default hanya untuk development dan harus diganti sebelum production.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
