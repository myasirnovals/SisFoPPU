<?= $this->extend('layout/coordinator_layout') ?>

<?= $this->section('styles') ?>
<style>
    .summary-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #2563eb 100%);
        color: #fff;
        border: 0;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
        overflow: hidden;
        position: relative;
    }

    .summary-hero::after {
        content: "";
        position: absolute;
        inset: auto -50px -50px auto;
        width: 180px;
        height: 180px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
    }

    .summary-card {
        border: 0;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    }

    .section-title {
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .soft-surface {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(148, 163, 184, 0.18);
        backdrop-filter: blur(8px);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card summary-hero rounded-4 mb-4">
    <div class="card-body p-4 p-lg-5 position-relative">
        <div class="row align-items-center g-4 position-relative">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-light text-dark">Role: Koordinator Praktikum</span>
                    <span class="badge bg-light text-dark">Ringkasan monitoring</span>
                    <span class="badge bg-light text-dark"><?= esc($filters['academic_year'] ?: '2025/2026') ?></span>
                </div>
                <h1 class="display-6 fw-bold mb-3">Dashboard Koordinator Praktikum</h1>
                <p class="lead mb-0 text-white-50">
                    Halaman ini hanya berisi ringkasan. Detail monitoring kelas, perhatian, remedial, validasi, dan aktivitas tersedia di halaman masing-masing.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="soft-surface rounded-4 p-3 text-dark">
                    <h2 class="h6 fw-bold mb-3">Status Cepat</h2>
                    <div class="d-grid gap-2">
                        <div class="d-flex justify-content-between"><span>Monitoring Kelas</span><strong><?= esc($overviewStats['monitoring_kelas']) ?></strong></div>
                        <div class="d-flex justify-content-between"><span>Kelas Perlu Perhatian</span><strong><?= esc($overviewStats['kelas_perhatian']) ?></strong></div>
                        <div class="d-flex justify-content-between"><span>Remedial</span><strong><?= esc($overviewStats['remedial']) ?></strong></div>
                        <div class="d-flex justify-content-between"><span>Validasi</span><strong><?= esc($overviewStats['validasi']) ?></strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card soft-surface rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h2 class="h5 section-title mb-1">Filter Global</h2>
                <p class="text-muted mb-0">Filter ini mempengaruhi ringkasan dan diteruskan ke halaman detail.</p>
            </div>
        </div>

        <form method="get" action="<?= site_url('coordinator/dashboard') ?>" class="row g-3">
            <div class="col-md-6 col-xl-2">
                <select name="academic_year" class="form-select">
                    <option value="">Tahun Akademik</option>
                    <?php foreach ($filterOptions['academic_years'] as $item): ?>
                        <option value="<?= esc($item) ?>" <?= $filters['academic_year'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <select name="semester" class="form-select">
                    <option value="">Semester</option>
                    <?php foreach ($filterOptions['semesters'] as $item): ?>
                        <option value="<?= esc($item) ?>" <?= $filters['semester'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <select name="study_program" class="form-select">
                    <option value="">Program Studi</option>
                    <?php foreach ($filterOptions['study_programs'] as $item): ?>
                        <option value="<?= esc($item) ?>" <?= $filters['study_program'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <select name="course_id" class="form-select">
                    <option value="">Mata Kuliah</option>
                    <?php foreach ($filterOptions['courses'] as $item): ?>
                        <option value="<?= esc($item['id']) ?>" <?= (string) $filters['course_id'] === (string) $item['id'] ? 'selected' : '' ?>><?= esc($item['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <select name="class_id" class="form-select">
                    <option value="">Kelas Praktikum</option>
                    <?php foreach ($filterOptions['classes'] as $item): ?>
                        <option value="<?= esc($item['id']) ?>" <?= (string) $filters['class_id'] === (string) $item['id'] ? 'selected' : '' ?>><?= esc($item['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <select name="lecturer" class="form-select">
                    <option value="">Dosen Pengampu</option>
                    <?php foreach ($filterOptions['lecturers'] as $item): ?>
                        <option value="<?= esc($item) ?>" <?= $filters['lecturer'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <select name="score_status" class="form-select">
                    <option value="">Status Nilai</option>
                    <?php foreach ($filterOptions['score_statuses'] as $item): ?>
                        <option value="<?= esc($item) ?>" <?= $filters['score_status'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="<?= site_url('coordinator/dashboard') ?>" class="btn btn-outline-secondary">Reset Filter</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($summaryCards as $card): ?>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card summary-card rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <div class="text-muted small mb-1"><?= esc($card['title']) ?></div>
                            <div class="display-6 fw-bold mb-0"><?= esc(number_format((float) $card['value'])) ?></div>
                        </div>
                        <div class="rounded-4 bg-<?= esc($card['color']) ?> bg-opacity-10 text-<?= esc($card['color']) ?> d-inline-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
                            <i class="bi <?= esc($card['icon']) ?> fs-4"></i>
                        </div>
                    </div>
                    <a href="<?= esc($card['link']) ?>" class="small fw-semibold text-decoration-none text-<?= esc($card['color']) ?>">Buka halaman detail</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card soft-surface rounded-4">
    <div class="card-body p-4">
        <h2 class="h5 section-title mb-3">Akses Cepat</h2>
        <div class="row g-3">
            <?php foreach ($quickActions as $action): ?>
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="<?= esc($action['url']) ?>" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="rounded-4 bg-<?= esc($action['color']) ?> bg-opacity-10 text-<?= esc($action['color']) ?> d-inline-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
                                    <i class="bi <?= esc($action['icon']) ?> fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark"><?= esc($action['label']) ?></div>
                                    <small class="text-muted">Masuk ke halaman khusus</small>
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
