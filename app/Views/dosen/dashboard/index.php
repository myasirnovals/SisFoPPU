<?= $this->extend('layout/dosen_layout') ?>

<?= $this->section('styles') ?>
<style>
    .hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #14b8a6 100%);
        color: #fff;
        border: 0;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        overflow: hidden;
        position: relative;
    }

    .hero-card::before,
    .hero-card::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
    }

    .hero-card::before {
        width: 240px;
        height: 240px;
        right: -80px;
        top: -80px;
    }

    .hero-card::after {
        width: 160px;
        height: 160px;
        right: 120px;
        bottom: -50px;
    }

    .soft-card {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(148, 163, 184, 0.18);
        backdrop-filter: blur(10px);
        box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
    }

    .section-title {
        letter-spacing: -0.03em;
        font-weight: 800;
    }

    .stat-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        transition: all 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 24px 44px rgba(15, 23, 42, 0.12);
    }

    .mini-pill {
        border: 1px solid rgba(255, 255, 255, 0.16);
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
    }

    .shortcut-card {
        transition: all 0.2s;
    }

    .shortcut-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ═══════════════════════════════════════════════════════════════════════ -->
<!-- HERO SECTION -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
<div class="hero-card rounded-5 mb-4">
    <div class="card-body p-4 p-lg-5 position-relative">
        <div class="row align-items-center g-4 position-relative">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge mini-pill rounded-pill px-3 py-2">Dashboard Dosen</span>
                    <span class="badge mini-pill rounded-pill px-3 py-2"><?= esc($academicYear) ?> - <?= esc($semesterLabel) ?></span>
                    <span class="badge mini-pill rounded-pill px-3 py-2"><?= esc($todayLabel) ?></span>
                </div>
                <h1 class="display-6 fw-bold mb-3">Selamat datang, <?= esc($userName) ?></h1>
                <p class="lead text-white-75 mb-0">
                    Pantau kelas praktikum yang Anda ampu, cek progres input nilai, lihat mahasiswa berisiko, dan selesaikan validasi dalam satu halaman.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="bg-white bg-opacity-10 rounded-4 p-3 border border-white border-opacity-10">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="text-white-50 small">Dosen Login</div>
                            <div class="fw-semibold fs-5"><?= esc($lecturerName) ?></div>
                        </div>
                        <div class="rounded-4 bg-white bg-opacity-15 d-flex align-items-center justify-content-center" style="width:3rem;height:3rem;">
                            <i class="bi bi-person-badge fs-4"></i>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <div class="d-flex justify-content-between"><span class="text-white-50">Tahun Akademik</span><strong><?= esc($academicYear) ?></strong></div>
                        <div class="d-flex justify-content-between"><span class="text-white-50">Semester</span><strong><?= esc($semesterLabel) ?></strong></div>
                        <div class="d-flex justify-content-between"><span class="text-white-50">Kelas Diampu</span><strong><?= esc($classTotal) ?></strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════ -->
<!-- STAT CARDS -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
<div class="row g-3 mb-4">
    <?php foreach ($summaryCards as $card): ?>
        <div class="col-12 col-sm-6 col-xl-4 col-xxl-2">
            <div class="card stat-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <div class="text-muted small mb-1"><?= esc($card['title']) ?></div>
                            <div class="h2 fw-bold mb-0"><?= esc($card['value']) ?></div>
                        </div>
                        <div class="rounded-4 bg-<?= esc($card['color']) ?> bg-opacity-10 text-<?= esc($card['color']) ?> d-flex align-items-center justify-content-center" style="width:3rem;height:3rem;">
                            <i class="bi <?= esc($card['icon']) ?> fs-4"></i>
                        </div>
                    </div>
                    <div class="text-muted small"><?= esc($card['description']) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════ -->
<!-- SHORTCUT MENU -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
<div class="card soft-card rounded-5 mb-4">
    <div class="card-body p-4 p-lg-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
            <div>
                <h2 class="h4 section-title mb-1">Shortcut Menu</h2>
                <p class="text-muted mb-0">Akses cepat ke modul inti dosen.</p>
            </div>
        </div>
        <div class="row g-3">
            <?php foreach ($quickActions as $action): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="<?= esc($action['url']) ?>" class="text-decoration-none">
                        <div class="card shortcut-card border-0 rounded-4 h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="rounded-4 bg-<?= esc($action['color']) ?> bg-opacity-10 text-<?= esc($action['color']) ?> d-inline-flex align-items-center justify-content-center" style="width:3rem;height:3rem;">
                                    <i class="bi <?= esc($action['icon']) ?> fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark"><?= esc($action['label']) ?></div>
                                    <small class="text-muted">Buka modul terkait</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>