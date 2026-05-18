<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #07111f 0%, #12263d 100%);
            color: #e7eef7;
        }

        .error-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .error-card {
            width: min(720px, 100%);
            border-radius: 24px;
            background: rgba(9, 18, 33, 0.82);
            border: 1px solid rgba(148, 163, 184, 0.16);
            box-shadow: 0 24px 70px rgba(1, 6, 19, 0.38);
        }
    </style>
</head>
<body>
    <div class="error-shell">
        <div class="error-card p-4 p-md-5 text-center">
            <div class="display-4 fw-bold mb-3">403</div>
            <h1 class="h3 fw-bold mb-3">Akses ditolak</h1>
            <p class="text-white-50 mb-4">
                <?= esc($message ?? 'Anda tidak memiliki izin untuk membuka halaman ini.') ?>
            </p>
            <a href="<?= function_exists('site_url') ? site_url('login') : '/login' ?>" class="btn btn-primary">Kembali ke Login</a>
        </div>
    </div>
</body>
</html>
