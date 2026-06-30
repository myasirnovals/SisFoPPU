<?= $this->extend('layout/mahasiswa_layout') ?>

<?= $this->section('styles') ?>
<style>
    .hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #0f766e 55%, #f59e0b 100%);
        color: #fff;
        border: 0;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        position: relative;
        overflow: hidden;
    }

    .hero-card::before,
    .hero-card::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
    }

    .hero-card::before {
        width: 220px;
        height: 220px;
        right: -80px;
        top: -70px;
    }

    .hero-card::after {
        width: 170px;
        height: 170px;
        right: 120px;
        bottom: -70px;
    }

    .soft-card {
        background: rgba(255, 255, 255, 0.9);
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
        transition: transform 0.2s ease, box-shadow 0.2s ease;
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

    .progress-label {
        min-width: 3rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #64748b;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #cbd5e1;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="student-dashboard">

    <!-- ═══ HERO CARD ═══ -->
    <div class="hero-card rounded-5 mb-4">
        <div class="card-body p-4 p-lg-5 position-relative">
            <div class="row align-items-center g-4 position-relative">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge mini-pill rounded-pill px-3 py-2">Dashboard Mahasiswa</span>
                        <span class="badge mini-pill rounded-pill px-3 py-2"><?= esc($academicYear ?? '-') ?> - <?= esc($semesterLabel ?? '-') ?></span>
                        <span class="badge mini-pill rounded-pill px-3 py-2">Role: Mahasiswa</span>
                    </div>
                    <h1 class="display-6 fw-bold mb-3">Halo, <?= esc($studentProfile['full_name'] ?? 'Mahasiswa') ?></h1>
                    <p class="lead text-white-75 mb-0">Pantau progres praktikum, kehadiran, nilai, dan status remedial Anda secara real-time.</p>
                </div>
                <div class="col-lg-4">
                    <div class="bg-white bg-opacity-10 rounded-4 p-3 border border-white border-opacity-10">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <div class="text-white-50 small">NIM</div>
                                <div class="fw-semibold fs-5"><?= esc($studentProfile['student_number'] ?? '-') ?></div>
                            </div>
                            <div class="rounded-4 bg-white bg-opacity-15 d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
                                <i class="bi bi-person-circle fs-4"></i>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <div class="d-flex justify-content-between">
                                <span class="text-white-50">Program Studi</span>
                                <strong><?= esc($studentProfile['study_program'] ?? '-') ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-white-50">Angkatan</span>
                                <strong><?= esc($studentProfile['class_year'] ?? '-') ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-white-50">Tahun Akademik</span>
                                <strong><?= esc($academicYear ?? '-') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ STAT CARDS ═══ -->
    <?php if (!empty($summaryCards)): ?>
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
                                <div class="rounded-4 bg-<?= esc($card['color']) ?> bg-opacity-10 text-<?= esc($card['color']) ?> d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
                                    <i class="bi <?= esc($card['icon']) ?> fs-4"></i>
                                </div>
                            </div>
                            <div class="text-muted small"><?= esc($card['description'] ?? '') ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card rounded-5 mb-4">
            <div class="card-body empty-state">
                <i class="bi bi-inbox"></i>
                <h5 class="mb-2">Belum Ada Data</h5>
                <p class="mb-0">Anda belum terdaftar di kelas praktikum manapun.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- ═══ PROGRESS SECTION ═══ -->
    <?php if (!empty($summaryCards)): ?>
        <div class="card soft-card rounded-5 mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h4 section-title mb-1">Ringkasan Progress Praktikum</h2>
                        <p class="text-muted mb-0">Gambaran cepat progres kehadiran, kelengkapan nilai, dan finalisasi kelas.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2">
                        <?= esc($summaryMeta['total_classes'] ?? 0) ?> kelas
                    </span>
                </div>
                <div class="row g-3">
                    <!-- Rata-rata Kehadiran -->
                    <div class="col-12 col-lg-4">
                        <div class="p-3 rounded-4 border bg-light-subtle h-100">
                            <div class="text-muted small mb-2">Rata-rata Kehadiran</div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress flex-grow-1" style="height: 18px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: <?= esc($summaryMeta['attendance_average'] ?? 0) ?>%;"
                                        aria-valuenow="<?= esc($summaryMeta['attendance_average'] ?? 0) ?>"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="fw-semibold progress-label"><?= esc($summaryMeta['attendance_average'] ?? 0) ?>%</span>
                            </div>
                        </div>
                    </div>
                    <!-- Kelengkapan Nilai -->
                    <div class="col-12 col-lg-4">
                        <div class="p-3 rounded-4 border bg-light-subtle h-100">
                            <div class="text-muted small mb-2">Kelengkapan Nilai</div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress flex-grow-1" style="height: 18px;">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?= esc($summaryMeta['score_average'] ?? 0) ?>%;"
                                        aria-valuenow="<?= esc($summaryMeta['score_average'] ?? 0) ?>"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="fw-semibold progress-label"><?= esc($summaryMeta['score_average'] ?? 0) ?>%</span>
                            </div>
                        </div>
                    </div>
                    <!-- Kelas Final -->
                    <div class="col-12 col-lg-4">
                        <div class="p-3 rounded-4 border bg-light-subtle h-100">
                            <div class="text-muted small mb-2">Kelas Final</div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="fw-semibold fs-4"><?= esc($summaryMeta['finalized'] ?? 0) ?></div>
                                <span class="text-muted">dari <?= esc($summaryMeta['total_classes'] ?? 0) ?> kelas</span>
                            </div>
                            <div class="mt-2 text-muted small">Kelas dengan nilai terkunci/tervalidasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ═══ QUICK LINKS / NOTIFICATIONS PREVIEW ═══ -->
    <?php if (!empty($notifications)): ?>
        <div class="card soft-card rounded-5">
            <div class="card-body p-4">
                <h5 class="section-title mb-3">Notifikasi Terbaru</h5>
                <div class="list-group list-group-flush">
                    <?php foreach (array_slice($notifications, 0, 3) as $notif): ?>
                        <div class="list-group-item d-flex align-items-center gap-3 px-0">
                            <span class="badge bg-<?= esc($notif['badge']) ?>-subtle text-<?= esc($notif['badge']) ?>-emphasis rounded-pill">
                                <?= esc($notif['title']) ?>
                            </span>
                            <div class="flex-grow-1 text-truncate"><?= esc($notif['message']) ?></div>
                            <small class="text-muted"><?= esc($notif['time']) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 text-end">
                    <a href="<?= site_url('mahasiswa/notifikasi') ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>