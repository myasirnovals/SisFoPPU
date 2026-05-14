<?= $this->extend('layout/coordinator_layout') ?>

<?= $this->section('styles') ?>
<style>
    .coordinator-dashboard {
        --panel-bg: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #334155 100%);
        --accent: #2563eb;
        font-family: "Segoe UI", Inter, Arial, sans-serif;
    }

    .hero-panel {
        background: var(--panel-bg);
        color: #fff;
        border: 0;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
        position: relative;
        overflow: hidden;
    }

    .hero-panel::after {
        content: "";
        position: absolute;
        inset: auto -60px -60px auto;
        width: 180px;
        height: 180px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
    }

    .hero-panel::before {
        content: "";
        position: absolute;
        top: -40px;
        right: 20%;
        width: 220px;
        height: 220px;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.22);
        filter: blur(10px);
    }

    .stat-card {
        border: 0;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.1);
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

    .badge-soft {
        border: 1px solid rgba(255, 255, 255, 0.16);
    }

    .table thead th {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #475569;
    }

    .pill-filter {
        min-width: 150px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="coordinator-dashboard">
    <div class="card hero-panel rounded-4 mb-4">
        <div class="card-body p-4 p-lg-5 position-relative">
            <div class="row align-items-center g-4 position-relative">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-dark badge-soft">Role: Koordinator Praktikum</span>
                        <span class="badge bg-light text-dark badge-soft">Monitoring lintas mata kuliah</span>
                        <span class="badge bg-light text-dark badge-soft"><?= esc($filters['academic_year'] ?: '2025/2026') ?></span>
                    </div>
                    <h1 class="display-6 fw-bold mb-3">Dashboard Koordinator Praktikum</h1>
                    <p class="lead mb-0 text-white-50">
                        Pantau progres input nilai, validasi, remedial, dan aktivitas nilai seluruh kelas praktikum aktif dalam satu layar.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="soft-surface rounded-4 p-3 text-dark">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small">Kondisi Monitoring</span>
                            <i class="bi bi-radar fs-4 text-primary"></i>
                        </div>
                        <div class="d-grid gap-2">
                            <div class="d-flex justify-content-between"><span>Kelas aktif</span><strong><?= esc($summaryCards[1]['value']) ?></strong></div>
                            <div class="d-flex justify-content-between"><span>Kelas perlu perhatian</span><strong><?= esc($attentionPagination['total']) ?></strong></div>
                            <div class="d-flex justify-content-between"><span>Peserta remedial</span><strong><?= esc($remedialStats['eligible']) ?></strong></div>
                            <div class="d-flex justify-content-between"><span>Nilai tervalidasi</span><strong><?= esc($summaryCards[6]['value']) ?></strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card soft-surface rounded-4 mb-4" id="filter-dashboard">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h2 class="h5 section-title mb-1">Filter Global</h2>
                    <p class="text-muted mb-0">Gunakan filter untuk menyesuaikan semua ringkasan, tabel, grafik, dan aktivitas terbaru.</p>
                </div>
            </div>

            <form method="get" action="<?= site_url('coordinator/dashboard') ?>" class="row g-3">
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Tahun Akademik</label>
                    <select name="academic_year" class="form-select pill-filter">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['academic_years'] as $item): ?>
                            <option value="<?= esc($item) ?>" <?= $filters['academic_year'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Semester</label>
                    <select name="semester" class="form-select pill-filter">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['semesters'] as $item): ?>
                            <option value="<?= esc($item) ?>" <?= $filters['semester'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Program Studi</label>
                    <select name="study_program" class="form-select pill-filter">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['study_programs'] as $item): ?>
                            <option value="<?= esc($item) ?>" <?= $filters['study_program'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Mata Kuliah</label>
                    <select name="course_id" class="form-select pill-filter">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['courses'] as $item): ?>
                            <option value="<?= esc($item['id']) ?>" <?= (string) $filters['course_id'] === (string) $item['id'] ? 'selected' : '' ?>><?= esc($item['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Kelas Praktikum</label>
                    <select name="class_id" class="form-select pill-filter">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['classes'] as $item): ?>
                            <option value="<?= esc($item['id']) ?>" <?= (string) $filters['class_id'] === (string) $item['id'] ? 'selected' : '' ?>><?= esc($item['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Dosen Pengampu</label>
                    <select name="lecturer" class="form-select pill-filter">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['lecturers'] as $item): ?>
                            <option value="<?= esc($item) ?>" <?= $filters['lecturer'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-xl-2">
                    <label class="form-label small text-muted">Status Nilai</label>
                    <select name="score_status" class="form-select pill-filter">
                        <option value="">Semua</option>
                        <?php foreach ($filterOptions['score_statuses'] as $item): ?>
                            <option value="<?= esc($item) ?>" <?= $filters['score_status'] === $item ? 'selected' : '' ?>><?= esc($item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-funnel-fill me-1"></i>Terapkan Filter</button>
                    <a href="<?= site_url('coordinator/dashboard') ?>" class="btn btn-outline-secondary px-4"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <?php foreach ($summaryCards as $card): ?>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card rounded-4 h-100 border-0">
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
                        <a href="<?= esc($card['link']) ?>" class="small fw-semibold text-decoration-none text-<?= esc($card['color']) ?>">Lihat detail</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card soft-surface rounded-4 mb-4" id="monitoring-kelas">
        <div class="card-body p-0">
            <div class="p-4 pb-0">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <h2 class="h5 section-title mb-1">Monitoring Progress Input Nilai</h2>
                        <p class="text-muted mb-0">Tabel ringkas progres kelengkapan nilai per kelas aktif.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill px-3 py-2"><?= esc($progressPagination['total']) ?> kelas</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Kelas</th>
                            <th>Dosen Pengampu</th>
                            <th>Jumlah Mahasiswa</th>
                            <th>Komponen Lengkap</th>
                            <th>Komponen Belum Lengkap</th>
                            <th>Progress</th>
                            <th>Status Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($progressRows)): ?>
                            <tr>
                                <td colspan="9">
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Tidak ada data progress input nilai untuk filter yang dipilih.
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($progressRows as $row): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= esc($row['course_name']) ?></div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">Kelas <?= esc($row['class_name']) ?></span></td>
                                    <td><?= esc($row['lecturer_name']) ?></td>
                                    <td><?= esc($row['student_count']) ?></td>
                                    <td><?= esc($row['complete_components']) ?></td>
                                    <td><?= esc($row['incomplete_components']) ?></td>
                                    <td style="min-width: 180px;">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar <?= $row['progress_percent'] === 100 ? 'bg-success' : 'bg-primary' ?>" role="progressbar" style="width: <?= esc($row['progress_percent']) ?>%;" aria-valuenow="<?= esc($row['progress_percent']) ?>" aria-valuemin="0" aria-valuemax="100">
                                                <?= esc($row['progress_percent']) ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge <?= esc($row['badge_class']) ?>"><?= esc($row['status']) ?></span></td>
                                    <td><a href="<?= esc($row['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 pt-3 border-top bg-white rounded-bottom-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">Menampilkan <?= esc(count($progressRows)) ?> dari <?= esc($progressPagination['total']) ?> data</small>
                    <div class="btn-group">
                        <?php $progressQuery = $filters + ['progress_page' => max(1, $progressPagination['page'] - 1)]; ?>
                        <a class="btn btn-outline-secondary btn-sm <?= $progressPagination['page'] <= 1 ? 'disabled' : '' ?>" href="<?= esc(site_url('coordinator/dashboard?' . http_build_query($progressQuery))) ?>">Sebelumnya</a>
                        <span class="btn btn-outline-secondary btn-sm disabled">Halaman <?= esc($progressPagination['page']) ?> / <?= esc($progressPagination['totalPages']) ?></span>
                        <?php $progressQueryNext = $filters + ['progress_page' => min($progressPagination['totalPages'], $progressPagination['page'] + 1)]; ?>
                        <a class="btn btn-outline-secondary btn-sm <?= $progressPagination['page'] >= $progressPagination['totalPages'] ? 'disabled' : '' ?>" href="<?= esc(site_url('coordinator/dashboard?' . http_build_query($progressQueryNext))) ?>">Berikutnya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4" id="perlu-perhatian">
        <div class="col-lg-6">
            <div class="card soft-surface rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="h5 section-title mb-1">Kelas Perlu Perhatian</h2>
                            <p class="text-muted mb-0">Kelas bermasalah, belum selesai, atau mendekati tenggat.</p>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><?= esc(count($attentionRows)) ?> kelas</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <th>Kelas</th>
                                    <th>Masalah</th>
                                    <th>Prioritas</th>
                                    <th>Deadline</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($attentionRows)): ?>
                                    <tr>
                                        <td colspan="6">
                                            <div class="text-center py-5 text-muted">
                                                <i class="bi bi-check2-circle fs-1 d-block mb-2"></i>
                                                Tidak ada kelas bermasalah pada filter yang dipilih.
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($attentionRows as $row): ?>
                                        <tr>
                                            <td><?= esc($row['course_name']) ?></td>
                                            <td><span class="badge bg-light text-dark border">Kelas <?= esc($row['class_name']) ?></span></td>
                                            <td style="max-width: 300px;"><span class="text-muted small"><?= esc($row['problem']) ?></span></td>
                                            <td><span class="badge <?= esc($row['priority_badge_class']) ?>"><?= esc($row['priority']) ?></span></td>
                                            <td><?= esc($row['deadline']) ?></td>
                                            <td><a href="<?= esc($row['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                        <small class="text-muted">Pagination monitoring perhatian</small>
                        <div class="btn-group">
                            <?php $attentionQuery = $filters + ['attention_page' => max(1, $attentionPagination['page'] - 1)]; ?>
                            <a class="btn btn-outline-secondary btn-sm <?= $attentionPagination['page'] <= 1 ? 'disabled' : '' ?>" href="<?= esc(site_url('coordinator/dashboard?' . http_build_query($attentionQuery))) ?>">Sebelumnya</a>
                            <span class="btn btn-outline-secondary btn-sm disabled">Halaman <?= esc($attentionPagination['page']) ?> / <?= esc($attentionPagination['totalPages']) ?></span>
                            <?php $attentionQueryNext = $filters + ['attention_page' => min($attentionPagination['totalPages'], $attentionPagination['page'] + 1)]; ?>
                            <a class="btn btn-outline-secondary btn-sm <?= $attentionPagination['page'] >= $attentionPagination['totalPages'] ? 'disabled' : '' ?>" href="<?= esc(site_url('coordinator/dashboard?' . http_build_query($attentionQueryNext))) ?>">Berikutnya</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6" id="laporan-quick">
            <div class="card soft-surface rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="h5 section-title mb-1">Quick Actions</h2>
                            <p class="text-muted mb-0">Akses cepat untuk halaman monitoring dan laporan.</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <?php foreach ($quickActions as $action): ?>
                            <div class="col-12 col-sm-6">
                                <a href="<?= esc($action['url']) ?>" class="text-decoration-none">
                                    <div class="card border-0 shadow-sm rounded-4 h-100">
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

                    <div class="mt-4">
                        <h3 class="h6 fw-bold mb-3">Status Validasi Nilai</h3>
                        <div class="small text-muted mb-2">Kondisi validasi per kelas praktikum aktif.</div>
                        <div class="table-responsive" id="validasi-nilai">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Mata Kuliah</th>
                                        <th>Kelas</th>
                                        <th>Status Input</th>
                                        <th>Status Validasi</th>
                                        <th>Submit</th>
                                        <th>Validasi</th>
                                        <th>Revisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($validationRows)): ?>
                                        <tr>
                                            <td colspan="7">
                                                <div class="text-center py-4 text-muted">Tidak ada data validasi untuk filter ini.</div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($validationRows as $row): ?>
                                            <tr>
                                                <td><?= esc($row['course_name']) ?></td>
                                                <td><?= esc($row['class_name']) ?></td>
                                                <td><span class="badge <?= esc($row['badge_class']) ?>"><?= esc($row['score_status']) ?></span></td>
                                                <td><span class="badge <?= esc($row['badge_class']) ?>"><?= esc($row['validation_status']) ?></span></td>
                                                <td><?= esc($row['submit_date']) ?></td>
                                                <td><?= esc($row['validation_date']) ?></td>
                                                <td><span class="badge bg-light text-dark border"><?= esc($row['revision_count']) ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2">
                            <small class="text-muted">Monitoring validasi lintas kelas</small>
                            <div class="btn-group">
                                <?php $validationQuery = $filters + ['validation_page' => max(1, $validationPagination['page'] - 1)]; ?>
                                <a class="btn btn-outline-secondary btn-sm <?= $validationPagination['page'] <= 1 ? 'disabled' : '' ?>" href="<?= esc(site_url('coordinator/dashboard?' . http_build_query($validationQuery))) ?>">Sebelumnya</a>
                                <span class="btn btn-outline-secondary btn-sm disabled">Halaman <?= esc($validationPagination['page']) ?> / <?= esc($validationPagination['totalPages']) ?></span>
                                <?php $validationQueryNext = $filters + ['validation_page' => min($validationPagination['totalPages'], $validationPagination['page'] + 1)]; ?>
                                <a class="btn btn-outline-secondary btn-sm <?= $validationPagination['page'] >= $validationPagination['totalPages'] ? 'disabled' : '' ?>" href="<?= esc(site_url('coordinator/dashboard?' . http_build_query($validationQueryNext))) ?>">Berikutnya</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4" id="remedial-monitoring">
        <div class="col-xl-4">
            <div class="card soft-surface rounded-4 h-100">
                <div class="card-body p-4">
                    <h2 class="h5 section-title mb-3">Monitoring Remedial</h2>
                    <div class="d-grid gap-2">
                        <div class="d-flex justify-content-between"><span>Eligible</span><strong><?= esc($remedialStats['eligible']) ?></strong></div>
                        <div class="d-flex justify-content-between"><span>Terdaftar</span><strong><?= esc($remedialStats['registered']) ?></strong></div>
                        <div class="d-flex justify-content-between"><span>Sudah Dinilai</span><strong><?= esc($remedialStats['graded']) ?></strong></div>
                        <div class="d-flex justify-content-between"><span>Sudah Divalidasi</span><strong><?= esc($remedialStats['validated']) ?></strong></div>
                        <div class="d-flex justify-content-between"><span>Tidak Mengikuti</span><strong><?= esc($remedialStats['not_attended']) ?></strong></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card soft-surface rounded-4 h-100">
                <div class="card-body p-4 p-0">
                    <div class="p-4 pb-0 d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h2 class="h5 section-title mb-1">Daftar Mahasiswa Remedial</h2>
                            <p class="text-muted mb-0">Mahasiswa yang eligible atau mengikuti remedial pada kelas aktif.</p>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><?= esc($remedialPagination['total']) ?> peserta</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Mata Kuliah</th>
                                    <th>Kelas</th>
                                    <th>Nilai Akhir</th>
                                    <th>Huruf Mutu</th>
                                    <th>Alasan Remedial</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($remedialRows)): ?>
                                    <tr>
                                        <td colspan="9">
                                            <div class="text-center py-5 text-muted">
                                                <i class="bi bi-clipboard2-check fs-1 d-block mb-2"></i>
                                                Tidak ada data remedial untuk filter yang dipilih.
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($remedialRows as $row): ?>
                                        <tr>
                                            <td><?= esc($row['nim']) ?></td>
                                            <td class="fw-semibold"><?= esc($row['student_name']) ?></td>
                                            <td><?= esc($row['course_name']) ?></td>
                                            <td><?= esc($row['class_name']) ?></td>
                                            <td><?= esc($row['final_score']) ?></td>
                                            <td><span class="badge bg-light text-dark border"><?= esc($row['grade']) ?></span></td>
                                            <td style="max-width: 260px;"><span class="text-muted small"><?= esc($row['reason']) ?></span></td>
                                            <td><span class="badge <?= esc($row['badge_class']) ?>"><?= esc($row['status']) ?></span></td>
                                            <td><a href="<?= esc($row['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 pt-3 border-top bg-white rounded-bottom-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted">Daftar remedial disusun untuk monitoring cepat</small>
                            <div class="btn-group">
                                <?php $remedialQuery = $filters + ['remedial_page' => max(1, $remedialPagination['page'] - 1)]; ?>
                                <a class="btn btn-outline-secondary btn-sm <?= $remedialPagination['page'] <= 1 ? 'disabled' : '' ?>" href="<?= esc(site_url('coordinator/dashboard?' . http_build_query($remedialQuery))) ?>">Sebelumnya</a>
                                <span class="btn btn-outline-secondary btn-sm disabled">Halaman <?= esc($remedialPagination['page']) ?> / <?= esc($remedialPagination['totalPages']) ?></span>
                                <?php $remedialQueryNext = $filters + ['remedial_page' => min($remedialPagination['totalPages'], $remedialPagination['page'] + 1)]; ?>
                                <a class="btn btn-outline-secondary btn-sm <?= $remedialPagination['page'] >= $remedialPagination['totalPages'] ? 'disabled' : '' ?>" href="<?= esc(site_url('coordinator/dashboard?' . http_build_query($remedialQueryNext))) ?>">Berikutnya</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4" id="aktivitas-terbaru">
        <div class="col-12">
            <div class="card soft-surface rounded-4 h-100">
                <div class="card-body p-4 p-0">
                    <div class="p-4 pb-0 d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h2 class="h5 section-title mb-1">Aktivitas Terbaru</h2>
                            <p class="text-muted mb-0">Aktivitas nilai, validasi, remedial, dan export laporan.</p>
                        </div>
                        <span class="badge bg-secondary rounded-pill px-3 py-2"><?= esc($activityPagination['total']) ?> log</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Aktivitas</th>
                                    <th>Modul</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($activityRows)): ?>
                                    <tr>
                                        <td colspan="6">
                                            <div class="text-center py-5 text-muted">
                                                <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                                                Belum ada aktivitas terbaru untuk filter ini.
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($activityRows as $row): ?>
                                        <tr>
                                            <td class="text-nowrap"><?= esc($row['time']) ?></td>
                                            <td><?= esc($row['user']) ?></td>
                                            <td><span class="badge bg-light text-dark border"><?= esc($row['role']) ?></span></td>
                                            <td><?= esc($row['activity']) ?></td>
                                            <td><span class="badge bg-primary-subtle text-primary-emphasis"><?= esc($row['module']) ?></span></td>
                                            <td style="max-width: 220px;"><span class="text-muted small"><?= esc($row['detail']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 pt-3 border-top bg-white rounded-bottom-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted">Log audit untuk penelusuran aktivitas nilai</small>
                            <div class="btn-group">
                                <?php $activityQuery = $filters + ['activity_page' => max(1, $activityPagination['page'] - 1)]; ?>
                                <a class="btn btn-outline-secondary btn-sm <?= $activityPagination['page'] <= 1 ? 'disabled' : '' ?>" href="<?= esc(site_url('coordinator/dashboard?' . http_build_query($activityQuery))) ?>">Sebelumnya</a>
                                <span class="btn btn-outline-secondary btn-sm disabled">Halaman <?= esc($activityPagination['page']) ?> / <?= esc($activityPagination['totalPages']) ?></span>
                                <?php $activityQueryNext = $filters + ['activity_page' => min($activityPagination['totalPages'], $activityPagination['page'] + 1)]; ?>
                                <a class="btn btn-outline-secondary btn-sm <?= $activityPagination['page'] >= $activityPagination['totalPages'] ? 'disabled' : '' ?>" href="<?= esc(site_url('coordinator/dashboard?' . http_build_query($activityQueryNext))) ?>">Berikutnya</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->endSection() ?>
