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

    .table thead th {
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-size: 0.74rem;
        color: #475569;
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

<!-- ═══════════════════════════════════════════════════════════════════════ -->
<!-- KELAS SAYA -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
<div class="card soft-card rounded-5 mb-4">
    <div class="card-body p-0">
        <div class="p-4 pb-0 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h2 class="h4 section-title mb-1">Kelas Praktikum Saya</h2>
                <p class="text-muted mb-0">Ringkasan kelas, progres nilai, progres kehadiran, dan status finalisasi.</p>
            </div>
            <a href="<?= site_url('dosen/kelas-saya') ?>" class="btn btn-sm btn-primary rounded-pill">
                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tahun Akademik</th>
                        <th>Semester</th>
                        <th>Mata Kuliah</th>
                        <th>Kode</th>
                        <th>Kelas</th>
                        <th>Mahasiswa</th>
                        <th>Progress Nilai</th>
                        <th>Progress Kehadiran</th>
                        <th>Rata-rata</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classRows)): ?>
                        <tr>
                            <td colspan="12" class="text-center py-5 text-muted">Belum ada kelas praktikum yang diampu.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classRows as $i => $classRow): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($classRow['academic_year']) ?></td>
                                <td><?= esc($classRow['semester']) ?></td>
                                <td class="fw-semibold"><?= esc($classRow['course_name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= esc($classRow['course_code']) ?></span></td>
                                <td><?= esc($classRow['class_name']) ?></td>
                                <td><?= esc($classRow['student_count']) ?></td>
                                <td style="min-width:140px;">
                                    <div class="progress" style="height:20px;">
                                        <div class="progress-bar bg-primary" style="width:<?= esc($classRow['progress_nilai']) ?>%"><?= esc($classRow['progress_nilai']) ?>%</div>
                                    </div>
                                </td>
                                <td style="min-width:140px;">
                                    <div class="progress" style="height:20px;">
                                        <div class="progress-bar bg-info" style="width:<?= esc($classRow['progress_kehadiran']) ?>%"><?= esc($classRow['progress_kehadiran']) ?>%</div>
                                    </div>
                                </td>
                                <td><?= esc(number_format((float)$classRow['average_score'], 1)) ?></td>
                                <td><span class="badge bg-<?= esc($classRow['status_badge']) ?>"><?= esc($classRow['status']) ?></span></td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="<?= esc($classRow['detail_url']) ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                        <a href="<?= esc($classRow['input_url']) ?>" class="btn btn-sm btn-outline-warning">Input</a>
                                        <a href="<?= esc($classRow['validation_url']) ?>" class="btn btn-sm btn-outline-success">Validasi</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════ -->
<!-- VALIDASI & RISIKO -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="card soft-card rounded-5 h-100">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h4 section-title mb-1">Nilai Perlu Validasi</h2>
                        <p class="text-muted mb-0">Submission yang masih menunggu review.</p>
                    </div>
                    <a href="<?= site_url('dosen/validasi') ?>" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mata Kuliah</th>
                                <th>Kelas</th>
                                <th>Komponen</th>
                                <th>Submitted By</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($validationRows)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada nilai yang perlu divalidasi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($validationRows as $v): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= esc($v['course_name']) ?></td>
                                        <td><?= esc($v['class_name']) ?></td>
                                        <td><?= esc($v['component_name']) ?></td>
                                        <td><?= esc($v['submitted_by']) ?></td>
                                        <td><?= esc($v['submitted_at']) ?></td>
                                        <td><span class="badge bg-<?= esc($v['badge_class']) ?>"><?= esc($v['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card soft-card rounded-5 h-100">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h4 section-title mb-1">Grafik Progress</h2>
                        <p class="text-muted mb-0">Distribusi progress input nilai.</p>
                    </div>
                </div>
                <div class="ratio ratio-16x9">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════════ -->
<!-- RISIKO & REMEDIAL -->
<!-- ═══════════════════════════════════════════════════════════════════════ -->
<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="card soft-card rounded-5 h-100">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h4 section-title mb-1">Mahasiswa Berisiko</h2>
                        <p class="text-muted mb-0">Mahasiswa yang perlu perhatian.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Mata Kuliah</th>
                                <th>Nilai</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($riskRows)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada mahasiswa berisiko.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($riskRows as $r): ?>
                                    <tr>
                                        <td><?= esc($r['nim']) ?></td>
                                        <td class="fw-semibold"><?= esc($r['student_name']) ?></td>
                                        <td><?= esc($r['course_name']) ?> (<?= esc($r['class_name']) ?>)</td>
                                        <td><?= esc($r['temporary_score']) ?></td>
                                        <td><span class="badge bg-<?= esc($r['badge_class']) ?>"><?= esc($r['risk_status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card soft-card rounded-5 h-100">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h2 class="h4 section-title mb-1">Mahasiswa Remedial</h2>
                        <p class="text-muted mb-0">Peserta remedial yang perlu dipantau.</p>
                    </div>
                    <a href="<?= site_url('dosen/remedial') ?>" class="btn btn-sm btn-outline-warning rounded-pill">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Mata Kuliah</th>
                                <th>Nilai</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($remedialRows)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada mahasiswa remedial.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($remedialRows as $r): ?>
                                    <tr>
                                        <td><?= esc($r['nim']) ?></td>
                                        <td class="fw-semibold"><?= esc($r['student_name']) ?></td>
                                        <td><?= esc($r['course_name']) ?></td>
                                        <td><?= esc($r['score']) ?> (<?= esc($r['grade']) ?>)</td>
                                        <td><span class="badge bg-<?= esc($r['badge_class']) ?>"><?= esc($r['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('progressChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($chartData['labels'] ?? [], JSON_UNESCAPED_UNICODE) ?>,
                datasets: [{
                    data: <?= json_encode($chartData['values'] ?? [], JSON_UNESCAPED_UNICODE) ?>,
                    backgroundColor: ['#2563eb', '#14b8a6', '#f59e0b', '#ef4444', '#64748b', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '68%'
            }
        });
    }
</script>
<?= $this->endSection() ?>