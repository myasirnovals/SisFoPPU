<?= $this->extend('layout/assistant_layout') ?>

<?= $this->section('styles') ?>
<style>
    .assistant-shell {
        --hero-start: #0f172a;
        --hero-mid: #1d4ed8;
        --hero-end: #0f766e;
    }

    .hero-panel {
        background: linear-gradient(135deg, var(--hero-start) 0%, var(--hero-mid) 55%, var(--hero-end) 100%);
        color: #fff;
        border: 0;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
    }

    .hero-panel::before,
    .hero-panel::after {
        content: "";
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
    }

    .hero-panel::before {
        width: 220px;
        height: 220px;
        right: -60px;
        top: -50px;
    }

    .hero-panel::after {
        width: 160px;
        height: 160px;
        right: 120px;
        bottom: -60px;
    }

    .soft-surface {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(148, 163, 184, 0.16);
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

    .table thead th {
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 0.74rem;
        color: #475569;
        white-space: nowrap;
    }

    .section-anchor {
        scroll-margin-top: 92px;
    }

    .quick-action-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .quick-action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="assistant-shell">
    <div class="card hero-panel rounded-5 mb-4 position-relative">
        <div class="card-body p-4 p-lg-5 position-relative">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">Role: Asisten Praktikum</span>
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2"><?= esc($academicYear) ?></span>
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2"><?= esc($semesterLabel) ?></span>
                    </div>
                    <h1 class="display-6 fw-bold mb-3">Dashboard Asisten Praktikum</h1>
                    <p class="lead mb-0 text-white-50">Pantau kelas, kehadiran, nilai, dan remedial praktikum yang Anda tangani.</p>
                </div>
                <div class="col-lg-4">
                    <div class="bg-white bg-opacity-10 rounded-4 p-3 border border-white border-opacity-10">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <div class="text-white-50 small">Asisten Login</div>
                                <div class="fw-semibold fs-5"><?= esc($assistantName) ?></div>
                            </div>
                            <div class="rounded-4 bg-white bg-opacity-15 d-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
                                <i class="bi bi-person-badge fs-4"></i>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <div class="d-flex justify-content-between"><span class="text-white-50">Tahun Akademik</span><strong><?= esc($academicYear) ?></strong></div>
                            <div class="d-flex justify-content-between"><span class="text-white-50">Semester</span><strong><?= esc($semesterLabel) ?></strong></div>
                            <div class="d-flex justify-content-between"><span class="text-white-50">Kelas Ditangani</span><strong><?= esc($summary['kelas'] ?? 0) ?></strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card soft-surface rounded-5 mb-4 section-anchor" id="ringkasan">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
                <div>
                    <h2 class="h4 section-title mb-1">Filter Dashboard</h2>
                    <p class="text-muted mb-0">Filter memengaruhi ringkasan, tabel kelas, absensi, nilai, dan remedial.</p>
                </div>
                <a href="<?= site_url('assistant/dashboard') ?>" class="btn btn-outline-secondary">Reset Filter</a>
            </div>

            <form method="get" action="<?= site_url('assistant/dashboard') ?>" class="row g-3">
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Tahun Akademik</label>
                    <select name="academic_year" class="form-select">
                        <?php foreach ($filterOptions['academic_years'] as $item): ?>
                            <option value="<?= esc($item) ?>" <?= ($filters['academic_year'] ?? '') === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Semester</label>
                    <select name="semester" class="form-select">
                        <?php foreach ($filterOptions['semesters'] as $item): ?>
                            <option value="<?= esc($item) ?>" <?= ($filters['semester'] ?? '') === $item ? 'selected' : '' ?>><?= esc(ucfirst($item)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Mata Kuliah Praktikum</label>
                    <select name="course_id" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['courses'] as $item): ?>
                            <option value="<?= esc($item['id']) ?>" <?= (string) ($filters['course_id'] ?? '') === (string) $item['id'] ? 'selected' : '' ?>><?= esc($item['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['classes'] as $item): ?>
                            <option value="<?= esc($item['id']) ?>" <?= (string) ($filters['class_id'] ?? '') === (string) $item['id'] ? 'selected' : '' ?>><?= esc($item['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Kelompok</label>
                    <select name="group_id" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['groups'] as $item): ?>
                            <option value="<?= esc($item['id']) ?>" <?= (string) ($filters['group_id'] ?? '') === (string) $item['id'] ? 'selected' : '' ?>><?= esc($item['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Status Kelas</label>
                    <select name="status_class" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['class_statuses'] as $item): ?>
                            <option value="<?= esc($item) ?>" <?= (string) ($filters['status_class'] ?? '') === (string) $item ? 'selected' : '' ?>><?= esc(ucfirst($item)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Status Kelengkapan Nilai</label>
                    <select name="status_score" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['score_statuses'] as $item): ?>
                            <option value="<?= esc($item) ?>" <?= (string) ($filters['status_score'] ?? '') === (string) $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Status Absensi</label>
                    <select name="status_attendance" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['attendance_statuses'] as $item): ?>
                            <option value="<?= esc($item) ?>" <?= (string) ($filters['status_attendance'] ?? '') === (string) $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-funnel me-1"></i>Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4" id="statistik">
        <?= $this->include('assistant/dashboard/partials/summary_cards') ?>
    </div>

    <div class="card soft-surface rounded-5 mb-4 section-anchor" id="aksi-cepat">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
                <div>
                    <h2 class="h4 section-title mb-1">Quick Actions</h2>
                    <p class="text-muted mb-0">Akses cepat ke fitur yang paling sering dipakai.</p>
                </div>
            </div>
            <div class="row g-3">
                <?php foreach ($quickActions as $action): ?>
                    <div class="col-12 col-sm-6 col-lg-4 col-xxl-3">
                        <a href="<?= esc($action['url']) ?>" class="text-decoration-none">
                            <div class="card quick-action-card border-0 rounded-4 h-100">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="rounded-4 bg-<?= esc($action['color']) ?> bg-opacity-10 text-<?= esc($action['color']) ?> d-inline-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
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
</div>
<?= $this->endSection() ?>